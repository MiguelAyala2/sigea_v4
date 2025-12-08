<?php

namespace App\Livewire\Stock\Marcas;

use App\Models\Stock\Marca;
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
        $marca = Marca::findOrFail($id);
        $marca->update([
            'activo' => true,
            'actualizadoPor' => auth()->id(),
        ]);
        
        session()->flash('success', 'Marca activada correctamente!');
    }

    public function inactivar($id)
    {
        $marca = Marca::findOrFail($id);
        $marca->update([
            'activo' => false,
            'actualizadoPor' => auth()->id(),
        ]);

        session()->flash('success', 'Marca inactivada correctamente!');
    }

    public function eliminar($id)
    {
        $marca = Marca::findOrFail($id);

        // TODO: Descomentar cuando se implemente el módulo de Productos
        // Verificar si tiene productos asociados
        // if ($marca->productos()->count() > 0) {
        //     session()->flash('error', 'No se puede eliminar la marca porque tiene productos asociados.');
        //     return;
        // }

        $marca->delete();

        session()->flash('success', 'Marca eliminada correctamente!');
    }

    public function render()
    {
        $marcas = Marca::query()
            ->buscador($this->buscador)
            ->when($this->buscarNombre, function ($query) {
                return $query->where('nombre', 'ILIKE', "%{$this->buscarNombre}%");
            })
            ->when($this->buscarActivo !== '', function ($query) {
                return $query->where('activo', $this->buscarActivo);
            })
            ->orderBy('nombre', 'asc')
            ->paginate($this->paginado);

        return view('livewire.stock.marcas.index', [
            'marcas' => $marcas,
        ]);
    }
}