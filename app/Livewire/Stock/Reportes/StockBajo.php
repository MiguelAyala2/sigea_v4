<?php

namespace App\Livewire\Stock\Reportes;

use App\Models\Stock\Stock;
use Livewire\Component;
use Livewire\WithPagination;

class StockBajo extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $depositoId = '';
    public $categoriaId = '';
    public $busqueda = '';
    public $incluirAgotados = true;

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

    public function updatingIncluirAgotados()
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

        // Filtrar por stock bajo o agotado
        if ($this->incluirAgotados) {
            $query->whereRaw('stock_actual <= stock_minimo');
        } else {
            $query->whereRaw('stock_actual > 0 AND stock_actual <= stock_minimo');
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

        $stocks = $query->orderBy('stock_actual', 'asc')
                        ->paginate(20);

        // Estadísticas
        $estadisticas = $this->calcularEstadisticas();

        // Obtener listas para filtros
        $depositos = collect(); // Pendiente de módulo Empresa
        $categorias = \App\Models\Stock\Categoria::where('activo', true)
                                                  ->orderBy('nombre')
                                                  ->get();

        return view('livewire.stock.reportes.stock-bajo', [
            'stocks' => $stocks,
            'depositos' => $depositos,
            'categorias' => $categorias,
            'estadisticas' => $estadisticas,
        ]);
    }

    private function calcularEstadisticas()
    {
        $query = Stock::whereHas('producto', function($q) {
            $q->where('activo', true)->where('maneja_stock', true);
        });

        if ($this->depositoId) {
            $query->where('deposito_id', $this->depositoId);
        }

        if ($this->categoriaId) {
            $query->whereHas('producto', function($q) {
                $q->where('categoria_id', $this->categoriaId);
            });
        }

        $agotados = (clone $query)->where('stock_actual', '<=', 0)->count();
        $bajos = (clone $query)->whereRaw('stock_actual > 0 AND stock_actual <= stock_minimo')->count();
        $totalProblemas = $agotados + $bajos;

        return [
            'agotados' => $agotados,
            'bajos' => $bajos,
            'total' => $totalProblemas,
        ];
    }
}
