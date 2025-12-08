<?php

namespace App\Livewire\Stock\UnidadesMedida;

use App\Models\Stock\UnidadMedida;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarNombre = '';
    public $buscarActivo = '';
    public $paginado = 10;

    public function updating($propertyName)
    {
        if (in_array($propertyName, ['buscador', 'buscarNombre', 'buscarActivo', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function activar($id)
    {
        $unidad = UnidadMedida::findOrFail($id);
        $unidad->update([
            'activo' => true,
            'actualizadoPor' => auth()->id(),
        ]);

        session()->flash('success', 'Unidad de medida activada correctamente!');
    }

    public function inactivar($id)
    {
        $unidad = UnidadMedida::findOrFail($id);
        $unidad->update([
            'activo' => false,
            'actualizadoPor' => auth()->id(),
        ]);

        session()->flash('success', 'Unidad de medida inactivada correctamente!');
    }

    public function eliminar($id)
    {
        $unidad = UnidadMedida::findOrFail($id);

        // TODO: Descomentar cuando se implemente el módulo de Productos
        // Verificar si tiene productos asociados
        // if ($unidad->productos()->count() > 0) {
        //     session()->flash('error', 'No se puede eliminar la unidad de medida porque tiene productos asociados.');
        //     return;
        // }

        $unidad->delete();

        session()->flash('success', 'Unidad de medida eliminada correctamente!');
    }

    public function render()
    {
        $unidadesMedida = UnidadMedida::query()
            ->buscador($this->buscador)
            ->when($this->buscarNombre, function ($query) {
                return $query->where('nombre', 'ILIKE', "%{$this->buscarNombre}%");
            })
            ->when($this->buscarActivo !== '', function ($query) {
                return $query->where('activo', $this->buscarActivo);
            })
            ->orderBy('nombre', 'asc')
            ->paginate($this->paginado);

        return view('livewire.stock.unidades-medida.index', [
            'unidadesMedida' => $unidadesMedida,
        ]);
    }
}
