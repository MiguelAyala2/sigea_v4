<?php

namespace App\Livewire\Stock\Categorias;

use App\Models\Stock\Categoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Create extends Component
{
    #[Validate]
    public $nombre = '';
    public $descripcion = '';
    public $parent_id = null;

    protected function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Categoria::class, 'nombre')->whereNull('deleted_at')
            ],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'parent_id' => [
                'nullable',
                Rule::exists(Categoria::class, 'id')->whereNull('deleted_at')
            ],
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.unique' => 'Este nombre ya está registrado.',
        'parent_id.exists' => 'La categoría padre no existe.',
    ];

    public function guardar()
    {
        $this->validate();

        Categoria::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'parent_id' => $this->parent_id,
            'activo' => true,
            'creadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Categoría creada correctamente!');
        return redirect()->route('stock.categorias.index');
    }

    public function render()
    {
        $categoriasDisponibles = Categoria::query()
            ->whereNull('parent_id')
            ->orWhere('nivel', '<', 3)
            ->activas()
            ->ordenadas()
            ->get();

        return view('livewire.stock.categorias.create', compact('categoriasDisponibles'));
    }
}
