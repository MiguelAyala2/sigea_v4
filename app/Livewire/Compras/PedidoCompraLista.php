<?php

namespace App\Livewire\Compras;

use App\Models\Compras\PedidoCompra;
use Livewire\Component;
use Livewire\WithPagination;

class PedidoCompraLista extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $estado = '';
    public $prioridad = '';
    public $tipo_pedido = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'estado' => ['except' => ''],
        'prioridad' => ['except' => ''],
        'tipo_pedido' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset([
            'search',
            'estado',
            'prioridad',
            'tipo_pedido',
            'fecha_desde',
            'fecha_hasta'
        ]);
    }

    public function eliminar($id)
    {
        $pedido = PedidoCompra::findOrFail($id);

        if (!in_array($pedido->estado, ['PENDIENTE', 'RECHAZADO'])) {
            session()->flash('error', 'Solo se pueden eliminar pedidos en estado PENDIENTE o RECHAZADO');
            return;
        }

        $pedido->delete();
        session()->flash('success', 'Pedido eliminado correctamente');
    }

    public function render()
    {
        $pedidos = PedidoCompra::query()
            ->with(['usuarioSolicitante', 'detalles'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('numero_pedido', 'like', '%' . $this->search . '%')
                      ->orWhere('justificacion', 'like', '%' . $this->search . '%')
                      ->orWhereHas('usuarioSolicitante', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->estado, function ($query) {
                $query->where('estado', $this->estado);
            })
            ->when($this->prioridad, function ($query) {
                $query->where('prioridad', $this->prioridad);
            })
            ->when($this->tipo_pedido, function ($query) {
                $query->where('tipo_pedido', $this->tipo_pedido);
            })
            ->when($this->fecha_desde, function ($query) {
                $query->where('fecha_pedido', '>=', $this->fecha_desde);
            })
            ->when($this->fecha_hasta, function ($query) {
                $query->where('fecha_pedido', '<=', $this->fecha_hasta);
            })
            ->orderBy('fecha_pedido', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.compras.pedido-compra-lista', [
            'pedidos' => $pedidos
        ]);
    }
}
