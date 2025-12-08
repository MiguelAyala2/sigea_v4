<?php

namespace App\Livewire\Stock\Categorias;

use App\Models\Stock\Categoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public $categoriaId;
    public $codigo = '';
    public $nombre = '';
    public $descripcion = '';
    public $parent_id = null;
    public $nivel = 1;

    public function mount(Categoria $categoria)
    {
        $this->categoriaId = $categoria->id;
        $this->codigo = $categoria->codigo;
        $this->nombre = $categoria->nombre;
        $this->descripcion = $categoria->descripcion;
        $this->parent_id = $categoria->parent_id;
        $this->nivel = $categoria->nivel;
    }

    protected function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Categoria::class, 'nombre')
                    ->ignore($this->categoriaId)
                    ->whereNull('deleted_at')
            ],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'parent_id' => [
                'nullable',
                Rule::exists(Categoria::class, 'id')->whereNull('deleted_at')
            ],
        ];
    }

    public function guardar()
    {
        $this->validate();

        $categoria = Categoria::findOrFail($this->categoriaId);

        // Verificar que no se esté seleccionando a sí misma o a sus descendientes como padre
        if ($this->parent_id) {
            $descendientes = $categoria->descendants()->pluck('id');
            if ($this->parent_id == $this->categoriaId || $descendientes->contains($this->parent_id)) {
                session()->flash('error', 'No se puede asignar una subcategoría como categoría padre.');
                return;
            }
        }

        $categoria->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'parent_id' => $this->parent_id,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Categoría actualizada correctamente!');
        return redirect()->route('stock.categorias.index');
    }

    public function render()
    {
        $categoriasDisponibles = Categoria::query()
            ->where('id', '!=', $this->categoriaId)
            ->where(function($query) {
                $query->whereNull('parent_id')
                    ->orWhere('nivel', '<', 3);
            })
            ->activas()
            ->ordenadas()
            ->get();

        return view('livewire.stock.categorias.edit', compact('categoriasDisponibles'));
    }
}
