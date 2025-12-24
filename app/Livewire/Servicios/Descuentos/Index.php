<?php

namespace App\Livewire\Servicios\Descuentos;

use App\Models\Servicios\Descuento;
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
    public $descuentoSeleccionado = null;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarTipo', 'buscarEstado', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $descuentos = Descuento::query()
            ->when($this->buscador, function ($query) {
                $query->where('codigo', 'ILIKE', "%{$this->buscador}%")
                    ->orWhere('descripcion', 'ILIKE', "%{$this->buscador}%");
            })
            ->when($this->buscarTipo, function ($query) {
                $query->where('tipo_descuento', $this->buscarTipo);
            })
            ->when($this->buscarEstado !== '', function ($query) {
                $query->where('activo', $this->buscarEstado);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->paginado);

        return view('livewire.servicios.descuentos.index', compact('descuentos'));
    }

    public function activar($id)
    {
        Descuento::findOrFail($id)->update(['activo' => true, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Descuento activado correctamente!');
    }

    public function inactivar($id)
    {
        Descuento::findOrFail($id)->update(['activo' => false, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Descuento inactivado correctamente!');
    }

    public function verDescuento($id)
    {
        $this->descuentoSeleccionado = Descuento::findOrFail($id);
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->descuentoSeleccionado = null;
    }
}
