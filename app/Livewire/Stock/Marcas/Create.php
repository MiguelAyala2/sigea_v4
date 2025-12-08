<?php

namespace App\Livewire\Stock\Marcas;

use App\Models\Stock\Marca;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Create extends Component
{
    #[Validate]
    public $nombre = '';
    public $descripcion = '';

    protected function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Marca::class, 'nombre')->whereNull('deleted_at')
            ],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.unique' => 'Este nombre ya está registrado.',
    ];

    public function guardar()
    {
        $this->validate();

        Marca::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'activo' => true,
            'creadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Marca creada correctamente!');
        return redirect()->route('stock.marcas.index');
    }

    public function render()
    {
        return view('livewire.stock.marcas.create');
    }
}