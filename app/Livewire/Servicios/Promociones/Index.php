<?php

namespace App\Livewire\Servicios\Promociones;

use App\Models\Servicios\Promocion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarTipo = '';
    public $buscarEstado = '';
    public $paginado = 10;

    public $mostrarModal = false;
    public $promocionSeleccionada = null;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarTipo', 'buscarEstado', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $promociones = Promocion::query()
            ->when($this->buscador, function ($query) {
                $query->where('codigo', 'ILIKE', "%{$this->buscador}%")
                    ->orWhere('nombre', 'ILIKE', "%{$this->buscador}%");
            })
            ->when($this->buscarTipo, function ($query) {
                $query->where('tipo', $this->buscarTipo);
            })
            ->when($this->buscarEstado !== '', function ($query) {
                $query->where('activo', $this->buscarEstado);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->paginado);

        return view('livewire.servicios.promociones.index', compact('promociones'));
    }

    public function activar($id)
    {
        Promocion::findOrFail($id)->update(['activo' => true, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Promoción activada correctamente!');
    }

    public function inactivar($id)
    {
        Promocion::findOrFail($id)->update(['activo' => false, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Promoción inactivada correctamente!');
    }

    public function verPromocion($id)
    {
        $this->promocionSeleccionada = Promocion::findOrFail($id);
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->promocionSeleccionada = null;
    }
}
