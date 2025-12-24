<?php

namespace App\Livewire\Servicios\Presupuestos;

use App\Models\Servicios\Presupuesto;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $buscador = '';
    public $buscarEstado = '';

    public $presupuestoSeleccionado = null;
    public $mostrarModal = false;

    protected $listeners = ['presupuestoGuardado' => '$refresh'];

    public function updatingBuscador()
    {
        $this->resetPage();
    }

    public function updatingBuscarEstado()
    {
        $this->resetPage();
    }

    public function verPresupuesto($id)
    {
        $this->presupuestoSeleccionado = Presupuesto::with([
            'diagnostico.recepcion.solicitud.cliente',
            'diagnostico.recepcion.producto',
            'diagnostico.tiposServicio',
            'diagnostico.repuestos',
            'promocion',
            'descuento'
        ])->findOrFail($id);
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->presupuestoSeleccionado = null;
    }

    public function aprobar($id)
    {
        $presupuesto = Presupuesto::findOrFail($id);

        if ($presupuesto->estado !== 'pendiente_aprobacion') {
            session()->flash('error', 'El presupuesto no está pendiente de aprobación.');
            return;
        }

        $presupuesto->update(['estado' => 'aprobado']);
        session()->flash('success', 'Presupuesto aprobado correctamente!');
    }

    public function rechazar($id)
    {
        $presupuesto = Presupuesto::findOrFail($id);

        if ($presupuesto->estado !== 'pendiente_aprobacion') {
            session()->flash('error', 'El presupuesto no está pendiente de aprobación.');
            return;
        }

        $presupuesto->update(['estado' => 'rechazado']);
        session()->flash('success', 'Presupuesto rechazado.');
    }

    public function render()
    {
        $presupuestos = Presupuesto::with([
            'diagnostico.recepcion.solicitud.cliente',
            'diagnostico.recepcion.producto'
        ])
            ->when($this->buscador, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', '%' . $this->buscador . '%')
                      ->orWhereHas('diagnostico.recepcion.solicitud.cliente', function ($q2) {
                          $q2->where('nombre', 'like', '%' . $this->buscador . '%');
                      });
                });
            })
            ->when($this->buscarEstado, function ($query) {
                $query->where('estado', $this->buscarEstado);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.servicios.presupuestos.index', compact('presupuestos'));
    }
}
