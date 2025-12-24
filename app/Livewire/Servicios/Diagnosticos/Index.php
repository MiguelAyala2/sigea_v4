<?php

namespace App\Livewire\Servicios\Diagnosticos;

use App\Models\Servicios\Diagnostico;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarEstadoDiagnostico = '';
    public $buscarEstado = '';
    public $buscarActivo = '';
    public $paginado = 10;

    // Modal Ver
    public $mostrarModal = false;
    public $diagnosticoSeleccionado = null;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarEstadoDiagnostico', 'buscarEstado', 'buscarActivo', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.servicios.diagnosticos.index', [
            'diagnosticos' => Diagnostico::query()
                ->with(['cliente', 'producto', 'solicitud', 'recepcion', 'repuestos'])
                ->buscador($this->buscador)
                ->buscarEstadoDiagnostico($this->buscarEstadoDiagnostico)
                ->buscarEstado($this->buscarEstado)
                ->buscarActivo($this->buscarActivo)
                ->orderBy('fecha_diagnostico', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($this->paginado),
        ]);
    }

    public function activar($id)
    {
        Diagnostico::findOrFail($id)->update(['activo' => true, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Diagnóstico activado correctamente!');
    }

    public function inactivar($id)
    {
        Diagnostico::findOrFail($id)->update(['activo' => false, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Diagnóstico inactivado correctamente!');
    }

    public function verDiagnostico($id)
    {
        $this->diagnosticoSeleccionado = Diagnostico::with([
            'cliente',
            'solicitud',
            'recepcion',
            'producto',
            'repuestos.producto.precioActual',
            'tiposServicio.tipoServicio'
        ])->findOrFail($id);

        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->diagnosticoSeleccionado = null;
    }
}
