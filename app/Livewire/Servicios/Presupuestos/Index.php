<?php

namespace App\Livewire\Servicios\Presupuestos;

use App\Models\Servicios\Presupuesto;
use App\Models\Servicios\OrdenServicio;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        try {
            DB::beginTransaction();

            $presupuesto = Presupuesto::findOrFail($id);

            if ($presupuesto->estado !== 'pendiente_aprobacion') {
                session()->flash('error', 'El presupuesto no está pendiente de aprobación.');
                return;
            }

            // Actualizar estado del presupuesto
            $presupuesto->update([
                'estado' => 'aprobado',
                'actualizadoPor' => Auth::id(),
            ]);

            // Crear orden de servicio automáticamente
            $orden = OrdenServicio::create([
                'codigo' => OrdenServicio::generarCodigo(),
                'fecha_orden' => now()->toDateString(),
                'presupuesto_id' => $presupuesto->id,
                'estado' => 'pendiente',
                'progreso' => 0,
                'activo' => true,
                'creadoPor' => Auth::id(),
            ]);

            DB::commit();

            session()->flash('success', 'Presupuesto aprobado correctamente! Se ha generado la orden de servicio ' . $orden->codigo);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al aprobar el presupuesto: ' . $e->getMessage());
        }
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
