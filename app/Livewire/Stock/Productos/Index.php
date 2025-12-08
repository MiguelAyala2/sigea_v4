<?php

namespace App\Livewire\Stock\Productos;

use App\Models\Stock\Producto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarNombre = '';
    public $buscarTipo = '';
    public $buscarCategoria = '';
    public $buscarMarca = '';
    public $buscarActivo = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingBuscador()
    {
        $this->resetPage();
    }

    public function activar($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update([
            'activo' => true,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Producto activado correctamente!');
    }

    public function inactivar($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update([
            'activo' => false,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Producto inactivado correctamente!');
    }

    public function eliminar($id)
    {
        $producto = Producto::findOrFail($id);

        // TODO: Verificar si tiene movimientos de stock o ventas/compras
        // if ($producto->movimientos()->count() > 0) {
        //     session()->flash('error', 'No se puede eliminar el producto porque tiene movimientos de stock.');
        //     return;
        // }

        $producto->delete();

        session()->flash('success', 'Producto eliminado correctamente!');
    }

    public function render()
    {
        $productos = Producto::query()
            ->with(['categoria', 'marca', 'unidadMedida', 'imagenPrincipal'])
            ->buscador($this->buscador)
            ->when($this->buscarNombre, function ($query) {
                return $query->where('nombre', 'ILIKE', "%{$this->buscarNombre}%");
            })
            ->when($this->buscarTipo, function ($query) {
                return $query->where('tipo', $this->buscarTipo);
            })
            ->when($this->buscarCategoria, function ($query) {
                return $query->where('categoria_id', $this->buscarCategoria);
            })
            ->when($this->buscarMarca, function ($query) {
                return $query->where('marca_id', $this->buscarMarca);
            })
            ->when($this->buscarActivo !== '', function ($query) {
                return $query->where('activo', $this->buscarActivo === 'true');
            })
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.stock.productos.index', compact('productos'));
    }
}
