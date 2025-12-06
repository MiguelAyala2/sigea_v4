<?php

namespace App\Livewire\Empresa\Sucursales;

use App\Models\Empresa\Empresa;
use App\Models\Empresa\Sucursal;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarActivo = '';
    public $paginado = 10;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarActivo', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $empresa = Empresa::actual();

        return view('livewire.empresa.sucursales.index', [
            'sucursales' => $empresa
                ? Sucursal::where('empresa_id', $empresa->id)
                    ->buscador($this->buscador)
                    ->buscarActivo($this->buscarActivo)
                    ->orderBy('codigo_establecimiento')
                    ->paginate($this->paginado)
                : collect(),
            'empresa' => $empresa,
        ]);
    }

    public function activar($id)
    {
        Sucursal::findOrFail($id)->update(['activo' => true, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Sucursal activada!');
    }

    public function inactivar($id)
    {
        Sucursal::findOrFail($id)->update(['activo' => false, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Sucursal inactivada!');
    }

    public function eliminar($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        if ($sucursal->depositos()->count() > 0 || $sucursal->puntosExpedicion()->count() > 0) {
            session()->flash('error', 'No se puede eliminar una sucursal que tiene depósitos o puntos de expedición asociados.');
            return;
        }
        $sucursal->delete();
        session()->flash('success', 'Sucursal eliminada!');
    }
}
