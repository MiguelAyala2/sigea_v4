<?php

namespace App\Livewire\Servicios\Entrega;

use App\Models\Servicios\OrdenServicio;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $estado = 'finalizada'; // Por defecto mostrar órdenes finalizadas listas para entrega
    public $fecha_desde = '';
    public $fecha_hasta = '';
    public $paginado = 15;

    // Modal de entrega
    public $mostrarModalEntrega = false;
    public $ordenSeleccionada = null;
    public $fecha_entrega = '';
    public $observaciones_entrega = '';
    public $recibido_por = '';
    public $documento_receptor = '';

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'fecha_entrega' => 'required|date',
        'recibido_por' => 'required|string|max:255',
        'documento_receptor' => 'nullable|string|max:50',
        'observaciones_entrega' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        // Por defecto mostrar el mes actual
        $this->fecha_desde = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_hasta = now()->endOfMonth()->format('Y-m-d');
        $this->fecha_entrega = now()->format('Y-m-d');
    }

    public function updatingBuscador()
    {
        $this->resetPage();
    }

    public function updatingEstado()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->buscador = '';
        $this->estado = 'finalizada';
        $this->fecha_desde = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_hasta = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function abrirModalEntrega($ordenId)
    {
        $this->ordenSeleccionada = OrdenServicio::with([
            'presupuesto.diagnostico.recepcion.solicitud.cliente',
            'presupuesto.diagnostico.recepcion.producto',
            'tecnico'
        ])->findOrFail($ordenId);

        // Reiniciar campos del modal
        $this->fecha_entrega = now()->format('Y-m-d');
        $this->recibido_por = $this->ordenSeleccionada->presupuesto?->diagnostico?->recepcion?->solicitud?->cliente?->nombre ?? '';
        $this->documento_receptor = $this->ordenSeleccionada->presupuesto?->diagnostico?->recepcion?->solicitud?->cliente?->documento ?? '';
        $this->observaciones_entrega = '';

        $this->mostrarModalEntrega = true;
    }

    public function cerrarModalEntrega()
    {
        $this->mostrarModalEntrega = false;
        $this->ordenSeleccionada = null;
        $this->reset(['fecha_entrega', 'recibido_por', 'documento_receptor', 'observaciones_entrega']);
        $this->resetErrorBag();
    }

    public function registrarEntrega()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Actualizar orden de servicio
            $this->ordenSeleccionada->update([
                'estado' => 'entregada',
                'fecha_entrega' => $this->fecha_entrega,
                'observaciones' => $this->ordenSeleccionada->observaciones .
                    "\n\nENTREGA: " . $this->observaciones_entrega .
                    "\nRecibido por: " . $this->recibido_por .
                    ($this->documento_receptor ? "\nDocumento: " . $this->documento_receptor : ''),
                'actualizadoPor' => auth()->id(),
            ]);

            DB::commit();

            session()->flash('success', 'Orden de servicio entregada exitosamente.');

            $this->cerrarModalEntrega();
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar la entrega: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $ordenes = OrdenServicio::query()
            ->with([
                'presupuesto.diagnostico.recepcion.solicitud.cliente',
                'presupuesto.diagnostico.recepcion.producto',
                'tecnico'
            ])
            ->when($this->buscador, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'ilike', '%' . $this->buscador . '%')
                        ->orWhereHas('presupuesto.diagnostico.recepcion.solicitud.cliente', function ($qc) {
                            $qc->where('nombre', 'ilike', '%' . $this->buscador . '%');
                        })
                        ->orWhereHas('presupuesto.diagnostico.recepcion.producto', function ($qp) {
                            $qp->where('nombre', 'ilike', '%' . $this->buscador . '%');
                        });
                });
            })
            ->when($this->estado, function ($query) {
                $query->where('estado', $this->estado);
            })
            ->when($this->fecha_desde, function ($query) {
                $query->whereDate('fecha_orden', '>=', $this->fecha_desde);
            })
            ->when($this->fecha_hasta, function ($query) {
                $query->whereDate('fecha_orden', '<=', $this->fecha_hasta);
            })
            ->orderBy('fecha_orden', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($this->paginado);

        // Estadísticas
        $stats = [
            'pendiente_entrega' => OrdenServicio::where('estado', 'finalizada')->count(),
            'entregadas_hoy' => OrdenServicio::where('estado', 'entregada')
                ->whereDate('fecha_entrega', now())
                ->count(),
            'entregadas_mes' => OrdenServicio::where('estado', 'entregada')
                ->whereMonth('fecha_entrega', now()->month)
                ->whereYear('fecha_entrega', now()->year)
                ->count(),
            'total' => OrdenServicio::whereIn('estado', ['finalizada', 'entregada'])->count(),
        ];

        return view('livewire.servicios.entrega.index', [
            'ordenes' => $ordenes,
            'stats' => $stats,
        ]);
    }
}
