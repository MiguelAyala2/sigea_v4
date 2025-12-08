<?php

namespace App\Livewire\Stock\Reportes;

use App\Models\Stock\Producto;
use App\Models\Stock\MovimientoStock;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class Rotacion extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $fechaDesde;
    public $fechaHasta;
    public $depositoId = '';
    public $categoriaId = '';
    public $busqueda = '';
    public $ordenarPor = 'rotacion_desc';

    public function mount()
    {
        // Establecer fechas por defecto (último mes)
        $this->fechaHasta = now()->format('Y-m-d');
        $this->fechaDesde = now()->subMonths(3)->format('Y-m-d');
    }

    public function updatingDepositoId()
    {
        $this->resetPage();
    }

    public function updatingCategoriaId()
    {
        $this->resetPage();
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingOrdenarPor()
    {
        $this->resetPage();
    }

    public function aplicarFiltros()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Producto::where('activo', true)
                         ->where('maneja_stock', true)
                         ->with(['unidadMedida', 'categoria', 'stock', 'precioActual']);

        // Filtrar por categoría
        if ($this->categoriaId) {
            $query->where('categoria_id', $this->categoriaId);
        }

        // Filtrar por búsqueda
        if ($this->busqueda) {
            $query->where(function($q) {
                $q->where('nombre', 'ilike', '%' . $this->busqueda . '%')
                  ->orWhere('codigo', 'ilike', '%' . $this->busqueda . '%');
            });
        }

        $productos = $query->get();

        // Calcular datos de rotación para cada producto
        $productosConRotacion = $productos->map(function($producto) {
            $movimientos = MovimientoStock::where('producto_id', $producto->id)
                                          ->whereDate('fecha_movimiento', '>=', $this->fechaDesde)
                                          ->whereDate('fecha_movimiento', '<=', $this->fechaHasta);

            if ($this->depositoId) {
                $movimientos->where('deposito_id', $this->depositoId);
            }

            $movimientos = $movimientos->get();

            $totalEntradas = $movimientos->filter->es_entrada->sum('cantidad');
            $totalSalidas = $movimientos->filter->es_salida->sum('cantidad');

            // Calcular stock promedio
            $stockActual = $this->depositoId
                ? ($producto->stock()->where('deposito_id', $this->depositoId)->first()->stock_actual ?? 0)
                : $producto->stock->sum('stock_actual');

            $stockPromedio = $stockActual > 0 ? $stockActual : ($totalSalidas / 2);

            // Índice de rotación = Salidas / Stock Promedio
            $rotacion = $stockPromedio > 0 ? ($totalSalidas / $stockPromedio) : 0;

            // Días de inventario = (Período en días) / Rotación
            $diasPeriodo = Carbon::parse($this->fechaDesde)->diffInDays(Carbon::parse($this->fechaHasta));
            $diasInventario = $rotacion > 0 ? ($diasPeriodo / $rotacion) : 0;

            return [
                'producto' => $producto,
                'total_entradas' => $totalEntradas,
                'total_salidas' => $totalSalidas,
                'stock_actual' => $stockActual,
                'stock_promedio' => $stockPromedio,
                'rotacion' => $rotacion,
                'dias_inventario' => $diasInventario,
                'clasificacion' => $this->clasificarRotacion($rotacion),
            ];
        });

        // Ordenar
        $productosConRotacion = $this->ordenarProductos($productosConRotacion);

        // Paginar manualmente
        $page = request()->get('page', 1);
        $perPage = 20;
        $total = $productosConRotacion->count();

        // Obtener items de la página actual
        $items = $productosConRotacion->slice(($page - 1) * $perPage, $perPage)->values();

        // Crear paginador
        $productosConRotacionPaginados = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Obtener listas para filtros
        $depositos = collect(); // Pendiente de módulo Empresa
        $categorias = \App\Models\Stock\Categoria::where('activo', true)
                                                  ->orderBy('nombre')
                                                  ->get();

        return view('livewire.stock.reportes.rotacion', [
            'productosConRotacion' => $productosConRotacionPaginados,
            'depositos' => $depositos,
            'categorias' => $categorias,
        ]);
    }

    private function clasificarRotacion($rotacion)
    {
        if ($rotacion >= 6) {
            return ['clase' => 'A', 'badge' => 'success', 'texto' => 'Alta'];
        } elseif ($rotacion >= 2) {
            return ['clase' => 'B', 'badge' => 'primary', 'texto' => 'Media'];
        } elseif ($rotacion > 0) {
            return ['clase' => 'C', 'badge' => 'warning', 'texto' => 'Baja'];
        } else {
            return ['clase' => 'D', 'badge' => 'danger', 'texto' => 'Sin movimiento'];
        }
    }

    private function ordenarProductos($productos)
    {
        switch ($this->ordenarPor) {
            case 'rotacion_desc':
                return $productos->sortByDesc('rotacion');
            case 'rotacion_asc':
                return $productos->sortBy('rotacion');
            case 'salidas_desc':
                return $productos->sortByDesc('total_salidas');
            case 'salidas_asc':
                return $productos->sortBy('total_salidas');
            case 'nombre':
                return $productos->sortBy('producto.nombre');
            default:
                return $productos->sortByDesc('rotacion');
        }
    }
}
