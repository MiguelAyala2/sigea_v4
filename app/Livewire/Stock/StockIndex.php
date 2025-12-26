<?php

namespace App\Livewire\Stock;

use App\Models\Stock\Stock;
use App\Models\Stock\Producto;
use Livewire\Component;
use Livewire\WithPagination;

class StockIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $busqueda = '';
    public $depositoId = '';
    public $estadoStock = '';
    public $categoriaId = '';
    public $soloActivos = true;

    protected $queryString = [
        'busqueda' => ['except' => ''],
        'depositoId' => ['except' => ''],
        'estadoStock' => ['except' => ''],
    ];

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingDepositoId()
    {
        $this->resetPage();
    }

    public function updatingEstadoStock()
    {
        $this->resetPage();
    }

    public function updatingCategoriaId()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->depositoId = '';
        $this->estadoStock = '';
        $this->categoriaId = '';
        $this->soloActivos = true;
        $this->resetPage();
    }

    public function render()
    {
        $query = Stock::with(['producto.unidadMedida', 'producto.categoria', 'producto.precioActual', 'deposito']);

        // Filtrar solo productos activos
        if ($this->soloActivos) {
            $query->whereHas('producto', function($q) {
                $q->where('activo', true);
            });
        }

        // Filtrar por búsqueda (nombre o código de producto)
        if ($this->busqueda) {
            $query->whereHas('producto', function($q) {
                $q->where('nombre', 'ilike', '%' . $this->busqueda . '%')
                  ->orWhere('codigo', 'ilike', '%' . $this->busqueda . '%')
                  ->orWhere('codigo_barras', 'ilike', '%' . $this->busqueda . '%');
            });
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

        // Filtrar por estado de stock
        if ($this->estadoStock) {
            switch ($this->estadoStock) {
                case 'agotado':
                    $query->where('stock_actual', '<=', 0);
                    break;
                case 'bajo':
                    $query->whereRaw('stock_actual > 0 AND stock_actual <= stock_minimo');
                    break;
                case 'normal':
                    $query->whereRaw('stock_actual > stock_minimo')
                          ->where(function($q) {
                              $q->whereNull('stock_maximo')
                                ->orWhereRaw('stock_actual < stock_maximo');
                          });
                    break;
                case 'alto':
                    $query->whereNotNull('stock_maximo')
                          ->whereRaw('stock_actual >= stock_maximo');
                    break;
            }
        }

        $stocks = $query->orderBy('stock_actual', 'asc')
                        ->paginate(20);

        // Estadísticas
        $estadisticas = $this->calcularEstadisticas();

        // Obtener listas para filtros
        $depositos = collect(); // Pendiente de módulo Empresa
        $categorias = \App\Models\Stock\Categoria::where('activo', true)
                                                  ->orderBy('nombre')
                                                  ->get();

        return view('livewire.stock.stock-index', [
            'stocks' => $stocks,
            'depositos' => $depositos,
            'categorias' => $categorias,
            'estadisticas' => $estadisticas,
        ]);
    }

    private function calcularEstadisticas()
    {
        $query = Stock::query();

        if ($this->soloActivos) {
            $query->whereHas('producto', function($q) {
                $q->where('activo', true);
            });
        }

        if ($this->depositoId) {
            $query->where('deposito_id', $this->depositoId);
        }

        $total = $query->count();
        $agotados = (clone $query)->where('stock_actual', '<=', 0)->count();
        $bajos = (clone $query)->whereRaw('stock_actual > 0 AND stock_actual <= stock_minimo')->count();

        return [
            'total' => $total,
            'agotados' => $agotados,
            'bajos' => $bajos,
            'normales' => $total - $agotados - $bajos,
        ];
    }
}
