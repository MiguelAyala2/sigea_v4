<?php

namespace App\Livewire\Empresa\PuntosExpedicion;

use App\Models\Empresa\PuntoExpedicion;
use App\Models\Empresa\Sucursal;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarSucursal = '';
    public $buscarTipo = '';
    public $buscarActivo = '';
    public $paginado = 10;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarSucursal', 'buscarTipo', 'buscarActivo', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.empresa.puntos-expedicion.index', [
            'puntosExpedicion' => PuntoExpedicion::with('sucursal')
                ->whereHas('sucursal.empresa', function ($query) {
                    $query->whereNotNull('id');
                })
                ->buscador($this->buscador)
                ->buscarSucursal($this->buscarSucursal)
                ->buscarTipo($this->buscarTipo)
                ->buscarActivo($this->buscarActivo)
                ->orderBy('codigo')
                ->paginate($this->paginado),
            'sucursales' => Sucursal::with('empresa')
                ->whereHas('empresa')
                ->soloActivos()
                ->orderBy('codigo_establecimiento')
                ->get(),
            'tipos' => PuntoExpedicion::TIPOS,
        ]);
    }

    public function activar($id)
    {
        PuntoExpedicion::findOrFail($id)->update(['activo' => true, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Punto de expedición activado!');
    }

    public function inactivar($id)
    {
        PuntoExpedicion::findOrFail($id)->update(['activo' => false, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Punto de expedición inactivado!');
    }

    public function eliminar($id)
    {
        $punto = PuntoExpedicion::findOrFail($id);
        // Aquí podrías agregar validaciones adicionales si hay documentos asociados
        $punto->delete();
        session()->flash('success', 'Punto de expedición eliminado!');
    }
}
