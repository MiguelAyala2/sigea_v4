<?php

namespace App\Livewire\Compras;

use App\Models\Compras\Presupuesto;
use Livewire\Component;
use Livewire\WithPagination;

class PresupuestoLista extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $estado_filter = '';
    public $proveedor_filter = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';

    protected $queryString = ['search', 'estado_filter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $presupuestos = Presupuesto::with(['proveedor', 'pedidoCompra'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('numero_presupuesto', 'ILIKE', '%' . $this->search . '%')
                      ->orWhereHas('proveedor', function($q2) {
                          $q2->where('razon_social', 'ILIKE', '%' . $this->search . '%')
                             ->orWhere('nombre_fantasia', 'ILIKE', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->estado_filter, function($query) {
                $query->where('estado', $this->estado_filter);
            })
            ->when($this->fecha_desde, function($query) {
                $query->whereDate('fecha_solicitud', '>=', $this->fecha_desde);
            })
            ->when($this->fecha_hasta, function($query) {
                $query->whereDate('fecha_solicitud', '<=', $this->fecha_hasta);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.compras.presupuesto-lista', [
            'presupuestos' => $presupuestos,
        ]);
    }
}
