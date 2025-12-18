<?php

namespace App\Livewire\Compras;

use App\Models\Compras\Compra;
use Livewire\Component;
use Livewire\WithPagination;

class CompraLista extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $estado_filtro = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'estado_filtro' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEstadoFiltro()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'estado_filtro', 'fecha_desde', 'fecha_hasta']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Compra::with(['proveedor', 'ordenCompra']);

        // Filtro de búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('numero_factura', 'ILIKE', '%' . $this->search . '%')
                  ->orWhereHas('proveedor', function($pq) {
                      $pq->where('nombre_fantasia', 'ILIKE', '%' . $this->search . '%')
                         ->orWhere('razon_social', 'ILIKE', '%' . $this->search . '%');
                  });
            });
        }

        // Filtro de estado
        if ($this->estado_filtro) {
            $query->where('estado', $this->estado_filtro);
        }

        // Filtro de fecha
        if ($this->fecha_desde) {
            $query->whereDate('fecha_emision', '>=', $this->fecha_desde);
        }

        if ($this->fecha_hasta) {
            $query->whereDate('fecha_emision', '<=', $this->fecha_hasta);
        }

        $compras = $query->orderBy('fecha_emision', 'desc')
                         ->orderBy('id', 'desc')
                         ->paginate(15);

        return view('livewire.compras.compra-lista', [
            'compras' => $compras,
        ]);
    }
}
