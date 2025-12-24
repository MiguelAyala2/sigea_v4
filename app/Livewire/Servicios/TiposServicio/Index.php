<?php

namespace App\Livewire\Servicios\TiposServicio;

use App\Models\Servicios\TipoServicio;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $buscar = '';
    public $filtroActivo = '';
    public $porPagina = 10;

    public $confirmandoEliminacion = false;
    public $tipoServicioAEliminar = null;

    protected $queryString = [
        'buscar' => ['except' => ''],
        'filtroActivo' => ['except' => ''],
    ];

    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function updatingFiltroActivo()
    {
        $this->resetPage();
    }

    public function confirmarEliminacion($id)
    {
        $this->tipoServicioAEliminar = $id;
        $this->confirmandoEliminacion = true;
    }

    public function cancelarEliminacion()
    {
        $this->confirmandoEliminacion = false;
        $this->tipoServicioAEliminar = null;
    }

    public function eliminar()
    {
        $tipoServicio = TipoServicio::find($this->tipoServicioAEliminar);

        if ($tipoServicio) {
            $tipoServicio->actualizadoPor = Auth::id();
            $tipoServicio->save();
            $tipoServicio->delete();

            session()->flash('success', 'Tipo de Servicio eliminado correctamente!');
        }

        $this->confirmandoEliminacion = false;
        $this->tipoServicioAEliminar = null;
    }

    public function toggleActivo($id)
    {
        $tipoServicio = TipoServicio::find($id);

        if ($tipoServicio) {
            $tipoServicio->activo = !$tipoServicio->activo;
            $tipoServicio->actualizadoPor = Auth::id();
            $tipoServicio->save();

            $estado = $tipoServicio->activo ? 'activado' : 'desactivado';
            session()->flash('success', "Tipo de Servicio {$estado} correctamente!");
        }
    }

    public function render()
    {
        $tiposServicio = TipoServicio::query()
            ->buscador($this->buscar)
            ->buscarActivo($this->filtroActivo)
            ->orderBy('codigo', 'asc')
            ->paginate($this->porPagina);

        return view('livewire.servicios.tipos-servicio.index', [
            'tiposServicio' => $tiposServicio,
        ]);
    }
}
