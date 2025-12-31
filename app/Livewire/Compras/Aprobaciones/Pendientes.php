<?php

namespace App\Livewire\Compras\Aprobaciones;

use App\Models\Compras\AprobacionFlujo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Pendientes extends Component
{
    use WithPagination;

    public $buscador = '';
    public $tipo_documento = '';
    public $paginado = 15;

    // Modal de aprobación
    public $mostrar_modal_aprobar = false;
    public $mostrar_modal_rechazar = false;
    public $aprobacion_seleccionada = null;
    public $comentarios_aprobacion = '';
    public $observaciones_rechazo = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingBuscador()
    {
        $this->resetPage();
    }

    public function updatingTipoDocumento()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->buscador = '';
        $this->tipo_documento = '';
        $this->resetPage();
    }

    public function abrirModalAprobar($aprobacionId)
    {
        $this->aprobacion_seleccionada = AprobacionFlujo::findOrFail($aprobacionId);
        $this->comentarios_aprobacion = '';
        $this->mostrar_modal_aprobar = true;
    }

    public function cerrarModalAprobar()
    {
        $this->mostrar_modal_aprobar = false;
        $this->aprobacion_seleccionada = null;
        $this->comentarios_aprobacion = '';
    }

    public function aprobar()
    {
        $this->validate([
            'comentarios_aprobacion' => 'nullable|string|max:500',
        ]);

        if (!$this->aprobacion_seleccionada) {
            session()->flash('error', 'No se encontró la aprobación.');
            return;
        }

        $this->aprobacion_seleccionada->aprobar($this->comentarios_aprobacion);

        session()->flash('success', 'Aprobación registrada exitosamente.');
        $this->cerrarModalAprobar();
        $this->dispatch('aprobacion-actualizada');
    }

    public function abrirModalRechazar($aprobacionId)
    {
        $this->aprobacion_seleccionada = AprobacionFlujo::findOrFail($aprobacionId);
        $this->observaciones_rechazo = '';
        $this->mostrar_modal_rechazar = true;
    }

    public function cerrarModalRechazar()
    {
        $this->mostrar_modal_rechazar = false;
        $this->aprobacion_seleccionada = null;
        $this->observaciones_rechazo = '';
    }

    public function rechazar()
    {
        $this->validate([
            'observaciones_rechazo' => 'required|string|max:500',
        ], [
            'observaciones_rechazo.required' => 'Debe indicar el motivo del rechazo.',
        ]);

        if (!$this->aprobacion_seleccionada) {
            session()->flash('error', 'No se encontró la aprobación.');
            return;
        }

        $this->aprobacion_seleccionada->rechazar($this->observaciones_rechazo);

        session()->flash('success', 'Rechazo registrado exitosamente.');
        $this->cerrarModalRechazar();
        $this->dispatch('aprobacion-actualizada');
    }

    public function render()
    {
        $aprobaciones = AprobacionFlujo::query()
            ->with(['aprobador', 'creadoPorUsuario'])
            ->where('usuario_aprobador_id', Auth::id())
            ->where('estado', 'PENDIENTE')
            ->when($this->buscador, function ($query) {
                $query->where(function ($q) {
                    $q->where('comentarios', 'ilike', '%' . $this->buscador . '%')
                        ->orWhere('rol_requerido', 'ilike', '%' . $this->buscador . '%');
                });
            })
            ->when($this->tipo_documento, function ($query) {
                $query->where('documento_tipo', $this->tipo_documento);
            })
            ->orderBy('fecha_vencimiento', 'asc')
            ->orderBy('fecha_asignacion', 'asc')
            ->paginate($this->paginado);

        // Estadísticas del usuario actual
        $stats = [
            'total_pendientes' => AprobacionFlujo::where('usuario_aprobador_id', Auth::id())
                ->where('estado', 'PENDIENTE')
                ->count(),
            'vencidas' => AprobacionFlujo::where('usuario_aprobador_id', Auth::id())
                ->vencidas()
                ->count(),
            'hoy' => AprobacionFlujo::where('usuario_aprobador_id', Auth::id())
                ->where('estado', 'PENDIENTE')
                ->whereDate('fecha_vencimiento', today())
                ->count(),
            'proximas' => AprobacionFlujo::where('usuario_aprobador_id', Auth::id())
                ->where('estado', 'PENDIENTE')
                ->whereBetween('fecha_vencimiento', [now(), now()->addDays(3)])
                ->count(),
        ];

        return view('livewire.compras.aprobaciones.pendientes', compact('aprobaciones', 'stats'));
    }
}
