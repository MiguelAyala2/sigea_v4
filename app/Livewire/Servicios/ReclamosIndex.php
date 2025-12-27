<?php

namespace App\Livewire\Servicios;

use App\Models\Servicios\Reclamo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReclamosIndex extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarEstado = '';
    public $buscarPrioridad = '';
    public $paginado = 10;

    public $mostrarModal = false;
    public $reclamoSeleccionado = null;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarEstado', 'buscarPrioridad', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $reclamos = Reclamo::with(['cliente', 'ordenServicio', 'responsable'])
            ->when($this->buscador, function ($query) {
                $query->where('codigo', 'ILIKE', "%{$this->buscador}%")
                    ->orWhereHas('cliente', function ($q) {
                        $q->where('nombre', 'ILIKE', "%{$this->buscador}%");
                    });
            })
            ->when($this->buscarEstado, function ($query) {
                $query->where('estado', $this->buscarEstado);
            })
            ->when($this->buscarPrioridad, function ($query) {
                $query->where('prioridad', $this->buscarPrioridad);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->paginado);

        return view('livewire.servicios.reclamos-index', compact('reclamos'));
    }

    public function verReclamo($id)
    {
        $this->reclamoSeleccionado = Reclamo::with(['cliente', 'ordenServicio', 'responsable', 'creador'])
            ->findOrFail($id);
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->reclamoSeleccionado = null;
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        $reclamo = Reclamo::findOrFail($id);
        $reclamo->update([
            'estado' => $nuevoEstado,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Estado actualizado correctamente!');
    }
}
