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
    public $paginado = 10;

    public function updating($propertyName): void
    {
        if (in_array($propertyName, ['buscador', 'estado', 'deposito_id', 'fecha_desde', 'fecha_hasta', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function marcarComoCompleta($recepcionId)
    {
        $recepcion = CompraRecepcion::findOrFail($recepcionId);
        $recepcion->marcarComoCompleta();
        
        session()->flash('success', 'Recepción marcada como completa.');
    }

    public function render()
    {
        $recepciones = CompraRecepcion::query()
            ->with(['compra.proveedor', 'deposito.sucursal', 'receptor'])
            ->buscador($this->buscador)
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
            ->paginate($this->paginado);

        return view('livewire.compras.recepciones.index', [
            'recepciones' => $recepciones,
            'depositos' => Deposito::where('activo', true)
                ->with('sucursal')
                ->orderBy('nombre')
                ->get(),
        ]);
    }
}
