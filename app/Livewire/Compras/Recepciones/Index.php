<?php

namespace App\Livewire\Compras\Recepciones;

use App\Models\Compras\CompraRecepcion;
use App\Models\Empresa\Deposito;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $estado = '';
    public $deposito_id = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';
    public $paginado = 15;

    protected $paginationTheme = 'bootstrap';

    public function updatingBuscador()
    {
        $this->resetPage();
    }

    public function updatingEstado()
    {
        $this->resetPage();
    }

    public function updatingDepositoId()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->buscador = '';
        $this->estado = '';
        $this->deposito_id = '';
        $this->fecha_desde = '';
        $this->fecha_hasta = '';
        $this->resetPage();
    }

    public function marcarComoCompleta($recepcionId)
    {
        $recepcion = CompraRecepcion::findOrFail($recepcionId);
        $recepcion->marcarComoCompleta();

        session()->flash('success', 'Recepción marcada como completa exitosamente.');
    }

    public function render()
    {
        $recepciones = CompraRecepcion::query()
            ->with(['compra.proveedor', 'deposito.sucursal', 'receptor'])
            ->when($this->buscador, function ($query) {
                $query->where(function ($q) {
                    $q->where('numero_remision', 'ilike', '%' . $this->buscador . '%')
                        ->orWhere('guia_transporte', 'ilike', '%' . $this->buscador . '%')
                        ->orWhereHas('compra', function ($qc) {
                            $qc->where('numero_factura', 'ilike', '%' . $this->buscador . '%');
                        })
                        ->orWhereHas('compra.proveedor', function ($qp) {
                            $qp->where('razon_social', 'ilike', '%' . $this->buscador . '%');
                        });
                });
            })
            ->when($this->estado, function ($query) {
                $query->where('estado', $this->estado);
            })
            ->when($this->deposito_id, function ($query) {
                $query->where('deposito_id', $this->deposito_id);
            })
            ->when($this->fecha_desde, function ($query) {
                $query->whereDate('fecha_recepcion', '>=', $this->fecha_desde);
            })
            ->when($this->fecha_hasta, function ($query) {
                $query->whereDate('fecha_recepcion', '<=', $this->fecha_hasta);
            })
            ->orderBy('fecha_recepcion', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($this->paginado);

        // Cargar depósitos para el filtro
        $depositos = Deposito::with('sucursal')->where('activo', true)->get();

        return view('livewire.compras.recepciones.index', compact('recepciones', 'depositos'));
    }
}
