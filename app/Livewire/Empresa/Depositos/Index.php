<?php

namespace App\Livewire\Empresa\Depositos;

use App\Models\Empresa\Deposito;
use App\Models\Empresa\Sucursal;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarSucursal = '';
    public $buscarActivo = '';
    public $paginado = 10;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarSucursal', 'buscarActivo', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.empresa.depositos.index', [
            'depositos' => Deposito::with('sucursal')
                ->whereHas('sucursal.empresa', function ($query) {
                    $query->whereNotNull('id');
                })
                ->buscador($this->buscador)
                ->buscarSucursal($this->buscarSucursal)
                ->buscarActivo($this->buscarActivo)
                ->orderBy('codigo')
                ->paginate($this->paginado),
            'sucursales' => Sucursal::with('empresa')
                ->whereHas('empresa')
                ->soloActivos()
                ->orderBy('codigo_establecimiento')
                ->get(),
        ]);
    }

    public function activar($id)
    {
        Deposito::findOrFail($id)->update(['activo' => true, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Depósito activado!');
    }

    public function inactivar($id)
    {
        Deposito::findOrFail($id)->update(['activo' => false, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Depósito inactivado!');
    }

    public function eliminar($id)
    {
        $deposito = Deposito::findOrFail($id);
        // Aquí podrías agregar validaciones adicionales si hay stock o movimientos
        $deposito->delete();
        session()->flash('success', 'Depósito eliminado!');
    }
}
