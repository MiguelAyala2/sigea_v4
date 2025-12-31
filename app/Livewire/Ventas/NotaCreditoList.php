<?php

namespace App\Livewire\Ventas;

use App\Models\Ventas\NotaCredito;
use Livewire\Component;
use Livewire\WithPagination;

class NotaCreditoList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filtros
    public $search = '';
    public $filter_estado = '';
    public $filter_fecha_desde = '';
    public $filter_fecha_hasta = '';
    public $filter_factura_id = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filter_estado' => ['except' => ''],
        'filter_fecha_desde' => ['except' => ''],
        'filter_fecha_hasta' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterEstado()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->filter_estado = '';
        $this->filter_fecha_desde = '';
        $this->filter_fecha_hasta = '';
        $this->filter_factura_id = '';
        $this->resetPage();
    }

    public function render()
    {
        $notasCredito = NotaCredito::query()
            ->with(['factura', 'cliente', 'puntoExpedicion', 'emitidoPor'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('numero_nota', 'ilike', '%' . $this->search . '%')
                        ->orWhereHas('factura', function ($qf) {
                            $qf->where('numero_factura', 'ilike', '%' . $this->search . '%');
                        })
                        ->orWhereHas('cliente', function ($qc) {
                            $qc->where('nombre', 'ilike', '%' . $this->search . '%')
                                ->orWhere('documento', 'ilike', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filter_estado, function ($query) {
                $query->where('estado', $this->filter_estado);
            })
            ->when($this->filter_fecha_desde, function ($query) {
                $query->whereDate('fecha_emision', '>=', $this->filter_fecha_desde);
            })
            ->when($this->filter_fecha_hasta, function ($query) {
                $query->whereDate('fecha_emision', '<=', $this->filter_fecha_hasta);
            })
            ->when($this->filter_factura_id, function ($query) {
                $query->where('factura_id', $this->filter_factura_id);
            })
            ->orderBy('fecha_emision', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('livewire.ventas.nota-credito-list', [
            'notasCredito' => $notasCredito,
        ]);
    }
}
