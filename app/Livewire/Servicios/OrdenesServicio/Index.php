<?php

namespace App\Livewire\Servicios\OrdenesServicio;

use App\Models\Servicios\OrdenServicio;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $buscador = '';
    public $buscarEstado = '';

    public $ordenSeleccionada = null;
    public $mostrarModal = false;

    // Variables para asignar técnico
    public $mostrarModalAsignar = false;
    public $ordenParaAsignar = null;
    public $tecnicoSeleccionado = null;

    protected $listeners = ['ordenGuardada' => '$refresh'];

    public function updatingBuscador()
    {
        $this->resetPage();
    }

    public function updatingBuscarEstado()
    {
        $this->resetPage();
    }

    public function verOrden($id)
    {
        $this->ordenSeleccionada = OrdenServicio::with([
            'presupuesto.diagnostico.recepcion.solicitud.cliente',
            'presupuesto.diagnostico.recepcion.producto',
            'presupuesto.diagnostico.tiposServicio.tipoServicio',
            'presupuesto.diagnostico.repuestos.producto',
            'tecnico'
        ])->findOrFail($id);
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->ordenSeleccionada = null;
    }

    public function abrirModalAsignar($id)
    {
        $this->ordenParaAsignar = OrdenServicio::findOrFail($id);
        $this->tecnicoSeleccionado = $this->ordenParaAsignar->tecnico_id;
        $this->mostrarModalAsignar = true;
    }

    public function cerrarModalAsignar()
    {
        $this->mostrarModalAsignar = false;
        $this->ordenParaAsignar = null;
        $this->tecnicoSeleccionado = null;
    }

    public function asignarTecnico()
    {
        if (!$this->tecnicoSeleccionado) {
            session()->flash('error', 'Debe seleccionar un técnico.');
            return;
        }

        $this->ordenParaAsignar->update([
            'tecnico_id' => $this->tecnicoSeleccionado,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Técnico asignado correctamente.');
        $this->cerrarModalAsignar();
    }

    public function iniciarOrden($id)
    {
        $orden = OrdenServicio::findOrFail($id);

        if ($orden->estado !== 'pendiente') {
            session()->flash('error', 'La orden no está en estado pendiente.');
            return;
        }

        if (!$orden->tecnico_id) {
            session()->flash('error', 'Debe asignar un técnico antes de iniciar la orden.');
            return;
        }

        $orden->update([
            'estado' => 'en_proceso',
            'fecha_inicio' => now(),
            'progreso' => 0,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Orden iniciada correctamente.');
    }

    public function render()
    {
        $ordenes = OrdenServicio::with([
            'presupuesto.diagnostico.recepcion.solicitud.cliente',
            'presupuesto.diagnostico.recepcion.producto',
            'tecnico'
        ])
            ->when($this->buscador, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', '%' . $this->buscador . '%')
                      ->orWhereHas('presupuesto.diagnostico.recepcion.solicitud.cliente', function ($q2) {
                          $q2->where('nombre', 'like', '%' . $this->buscador . '%');
                      });
                });
            })
            ->when($this->buscarEstado, function ($query) {
                $query->where('estado', $this->buscarEstado);
            })
            ->where('activo', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Obtener técnicos (usuarios con rol de técnico)
        $tecnicos = User::where('activo', true)->orderBy('name')->get();

        return view('livewire.servicios.ordenes-servicio.index', compact('ordenes', 'tecnicos'));
    }
}
