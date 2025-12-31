<?php

namespace App\Livewire\Compras\Aprobaciones;

use App\Models\Compras\AprobacionFlujo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MisAprobaciones extends Component
{
    use WithPagination;

    public $buscador = '';
    public $tipo_documento = '';
    public $estado = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';
    public $paginado = 15;

    protected $paginationTheme = 'bootstrap';

    public function updatingBuscador()
    {
        $this->resetPage();
    }

    public function updatingTipoDocumento()
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
        $this->tipo_documento = '';
        $this->estado = '';
        $this->fecha_desde = '';
        $this->fecha_hasta = '';
        $this->resetPage();
    }

    public function render()
    {
        $aprobaciones = AprobacionFlujo::query()
            ->with(['aprobador', 'creadoPorUsuario'])
            ->where('usuario_aprobador_id', Auth::id())
            ->when($this->buscador, function ($query) {
                $query->where(function ($q) {
                    $q->where('comentarios', 'ilike', '%' . $this->buscador . '%')
                        ->orWhere('rol_requerido', 'ilike', '%' . $this->buscador . '%')
                        ->orWhere('observaciones_rechazo', 'ilike', '%' . $this->buscador . '%');
                });
            })
            ->when($this->tipo_documento, function ($query) {
                $query->where('documento_tipo', $this->tipo_documento);
            })
            ->when($this->estado, function ($query) {
                $query->where('estado', $this->estado);
            })
            ->when($this->fecha_desde, function ($query) {
                $query->whereDate('fecha_aprobacion', '>=', $this->fecha_desde);
            })
            ->when($this->fecha_hasta, function ($query) {
                $query->whereDate('fecha_aprobacion', '<=', $this->fecha_hasta);
            })
            ->orderBy('fecha_aprobacion', 'desc')
            ->orderBy('fecha_asignacion', 'desc')
            ->paginate($this->paginado);

        // Estadísticas del usuario actual
        $stats = [
            'total' => AprobacionFlujo::where('usuario_aprobador_id', Auth::id())->count(),
            'pendientes' => AprobacionFlujo::where('usuario_aprobador_id', Auth::id())
                ->where('estado', 'PENDIENTE')
                ->count(),
            'aprobados' => AprobacionFlujo::where('usuario_aprobador_id', Auth::id())
                ->where('estado', 'APROBADO')
                ->count(),
            'rechazados' => AprobacionFlujo::where('usuario_aprobador_id', Auth::id())
                ->where('estado', 'RECHAZADO')
                ->count(),
        ];

        return view('livewire.compras.aprobaciones.mis-aprobaciones', compact('aprobaciones', 'stats'));
    }
}
