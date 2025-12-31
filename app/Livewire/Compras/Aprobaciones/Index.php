<?php

namespace App\Livewire\Compras\Aprobaciones;

use App\Models\Compras\AprobacionFlujo;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $tipo_documento = '';
    public $estado = '';
    public $nivel_aprobacion = '';
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

    public function updatingNivelAprobacion()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->buscador = '';
        $this->tipo_documento = '';
        $this->estado = '';
        $this->nivel_aprobacion = '';
        $this->resetPage();
    }

    public function render()
    {
        $aprobaciones = AprobacionFlujo::query()
            ->with(['aprobador', 'creadoPorUsuario'])
            ->when($this->buscador, function ($query) {
                $query->where(function ($q) {
                    $q->where('comentarios', 'ilike', '%' . $this->buscador . '%')
                        ->orWhere('rol_requerido', 'ilike', '%' . $this->buscador . '%')
                        ->orWhereHas('aprobador', function ($qap) {
                            $qap->where('name', 'ilike', '%' . $this->buscador . '%');
                        });
                });
            })
            ->when($this->tipo_documento, function ($query) {
                $query->where('documento_tipo', $this->tipo_documento);
            })
            ->when($this->estado, function ($query) {
                $query->where('estado', $this->estado);
            })
            ->when($this->nivel_aprobacion, function ($query) {
                $query->where('nivel_aprobacion', $this->nivel_aprobacion);
            })
            ->orderBy('fecha_asignacion', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($this->paginado);

        // Estadísticas
        $stats = [
            'total' => AprobacionFlujo::count(),
            'pendientes' => AprobacionFlujo::where('estado', 'PENDIENTE')->count(),
            'aprobados' => AprobacionFlujo::where('estado', 'APROBADO')->count(),
            'rechazados' => AprobacionFlujo::where('estado', 'RECHAZADO')->count(),
            'vencidos' => AprobacionFlujo::vencidas()->count(),
        ];

        return view('livewire.compras.aprobaciones.index', compact('aprobaciones', 'stats'));
    }
}
