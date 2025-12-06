<?php

namespace App\Livewire\Empresa\Timbrados;

use App\Models\Empresa\Empresa;
use App\Models\Empresa\Timbrado;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarTipoDocumento = '';
    public $buscarActivo = '';
    public $paginado = 10;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarTipoDocumento', 'buscarActivo', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $empresa = Empresa::actual();

        return view('livewire.empresa.timbrados.index', [
            'timbrados' => $empresa
                ? Timbrado::where('empresa_id', $empresa->id)
                    ->buscador($this->buscador)
                    ->buscarTipoDocumento($this->buscarTipoDocumento)
                    ->buscarActivo($this->buscarActivo)
                    ->orderByDesc('fecha_inicio_vigencia')
                    ->paginate($this->paginado)
                : collect(),
            'tiposDocumento' => Timbrado::TIPOS_DOCUMENTO,
            'empresa' => $empresa,
        ]);
    }

    public function activar($id)
    {
        Timbrado::findOrFail($id)->update(['activo' => true, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Timbrado activado!');
    }

    public function inactivar($id)
    {
        Timbrado::findOrFail($id)->update(['activo' => false, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Timbrado inactivado!');
    }

    public function eliminar($id)
    {
        $timbrado = Timbrado::findOrFail($id);
        if ($timbrado->numero_actual > 0) {
            session()->flash('error', 'No se puede eliminar un timbrado utilizado.');
            return;
        }
        $timbrado->delete();
        session()->flash('success', 'Timbrado eliminado!');
    }
}
