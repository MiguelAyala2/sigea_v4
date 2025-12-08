<?php

namespace App\Livewire\Stock\Categorias;

use App\Models\Stock\Categoria;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarNombre = '';
    public $buscarActivo = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingBuscador()
    {
        $this->resetPage();
    }

    public function activar($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update([
            'activo' => true,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Categoría activada correctamente!');
    }

    public function inactivar($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update([
            'activo' => false,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Categoría inactivada correctamente!');
    }

    public function eliminar($id)
    {
        $categoria = Categoria::findOrFail($id);

        // Verificar si tiene subcategorías
        if ($categoria->children()->count() > 0) {
            session()->flash('error', 'No se puede eliminar la categoría porque tiene subcategorías asociadas.');
            return;
        }

        // TODO: Descomentar cuando se implemente el módulo de Productos
        // Verificar si tiene productos asociados
        // if ($categoria->productos()->count() > 0) {
        //     session()->flash('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        //     return;
        // }

        $categoria->delete();

        session()->flash('success', 'Categoría eliminada correctamente!');
    }

    public function render()
    {
        $categorias = Categoria::query()
            ->with('parent')
            ->buscador($this->buscador)
            ->when($this->buscarNombre, function ($query) {
                return $query->where('nombre', 'ILIKE', "%{$this->buscarNombre}%");
            })
            ->when($this->buscarActivo !== '', function ($query) {
                return $query->where('activo', $this->buscarActivo === 'true');
            })
            ->ordenadas()
            ->paginate(10);

        return view('livewire.stock.categorias.index', compact('categorias'));
    }
}
