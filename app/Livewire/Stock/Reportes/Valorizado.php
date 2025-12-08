<?php

namespace App\Livewire\Stock\Reportes;

use App\Models\Stock\Stock;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class Valorizado extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $depositoId = '';
    public $categoriaId = '';
    public $busqueda = '';
    public $soloConStock = true;
    public $ordenarPor = 'valor_desc';

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

    public function updatingSoloConStock()
    {
        $this->resetPage();
    }

    public function updatingOrdenarPor()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Stock::with(['producto.unidadMedida', 'producto.categoria', 'producto.precioActual', 'deposito'])
                      ->whereHas('producto', function($q) {
                          $q->where('activo', true)
                            ->where('maneja_stock', true);
                      });

        // Filtrar solo con stock
        if ($this->soloConStock) {
            $query->where('stock_actual', '>', 0);
        }

        // Filtrar por depósito
        if ($this->depositoId) {
            $query->where('deposito_id', $this->depositoId);
        }

        // Filtrar por categoría
        if ($this->categoriaId) {
            $query->whereHas('producto', function($q) {
                $q->where('categoria_id', $this->categoriaId);
            });
        }

        // Filtrar por búsqueda
        if ($this->busqueda) {
            $query->whereHas('producto', function($q) {
                $q->where('nombre', 'ilike', '%' . $this->busqueda . '%')
                  ->orWhere('codigo', 'ilike', '%' . $this->busqueda . '%');
            });
        }

        $stocks = $query->get();

        // Calcular valorización para cada stock
        $stocksValorizados = $stocks->map(function($stock) {
            $precioCompra = $stock->producto->precioActual?->precio_compra ?? 0;
            $precioVenta = $stock->producto->precioActual?->precio_venta ?? 0;

            $valorCompra = $stock->stock_actual * $precioCompra;
            $valorVenta = $stock->stock_actual * $precioVenta;
            $utilidadPotencial = $valorVenta - $valorCompra;

            return [
                'stock' => $stock,
                'precio_compra' => $precioCompra,
                'precio_venta' => $precioVenta,
                'valor_compra' => $valorCompra,
                'valor_venta' => $valorVenta,
                'utilidad_potencial' => $utilidadPotencial,
            ];
        });

        // Ordenar
        $stocksValorizados = $this->ordenarStocks($stocksValorizados);

        // Calcular totales
        $totales = [
            'total_valor_compra' => $stocksValorizados->sum('valor_compra'),
            'total_valor_venta' => $stocksValorizados->sum('valor_venta'),
            'total_utilidad_potencial' => $stocksValorizados->sum('utilidad_potencial'),
            'cantidad_productos' => $stocksValorizados->count(),
        ];

        // Paginar manualmente
        $page = request()->get('page', 1);
        $perPage = 20;
        $total = $stocksValorizados->count();

        // Obtener items de la página actual
        $items = $stocksValorizados->slice(($page - 1) * $perPage, $perPage)->values();

        // Crear paginador
        $stocksValorizadosPaginados = new LengthAwarePaginator(
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

        return view('livewire.stock.reportes.valorizado', [
            'stocksValorizados' => $stocksValorizadosPaginados,
            'depositos' => $depositos,
            'categorias' => $categorias,
            'totales' => $totales,
        ]);
    }

    private function ordenarStocks($stocks)
    {
        switch ($this->ordenarPor) {
            case 'valor_desc':
                return $stocks->sortByDesc('valor_compra');
            case 'valor_asc':
                return $stocks->sortBy('valor_compra');
            case 'utilidad_desc':
                return $stocks->sortByDesc('utilidad_potencial');
            case 'utilidad_asc':
                return $stocks->sortBy('utilidad_potencial');
            case 'nombre':
                return $stocks->sortBy('stock.producto.nombre');
            case 'stock_desc':
                return $stocks->sortByDesc('stock.stock_actual');
            default:
                return $stocks->sortByDesc('valor_compra');
        }
    }
}
