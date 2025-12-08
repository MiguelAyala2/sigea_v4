<?php

namespace App\Livewire\Stock\AtributosTipo;

use App\Models\Stock\AtributoTipo;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $nombre = '';
    public $unidad = '';
    public $descripcion = '';
    public $orden = 0;
    public $es_filtrable = false;
    public $es_requerido = false;
    public $activo = true;

    protected $rules = [
        'nombre' => 'required|min:3|max:100',
        'unidad' => 'nullable|max:50',
        'descripcion' => 'nullable',
        'orden' => 'required|integer|min:0',
        'es_filtrable' => 'boolean',
        'es_requerido' => 'boolean',
        'activo' => 'boolean',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio',
        'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
        'nombre.max' => 'El nombre no puede superar 100 caracteres',
        'unidad.max' => 'La unidad no puede superar 50 caracteres',
        'orden.required' => 'El orden es obligatorio',
        'orden.integer' => 'El orden debe ser un número entero',
        'orden.min' => 'El orden debe ser mayor o igual a 0',
    ];

    public function guardar()
    {
        $this->validate();

        try {
            AtributoTipo::create([
                'nombre' => $this->nombre,
                'unidad' => $this->unidad ?: null,
                'descripcion' => $this->descripcion ?: null,
                'orden' => $this->orden,
                'es_filtrable' => $this->es_filtrable,
                'es_requerido' => $this->es_requerido,
                'activo' => $this->activo,
                'creadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Atributo Tipo creado correctamente');
            return redirect()->route('stock.atributos-tipo.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el Atributo Tipo: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.stock.atributos-tipo.create');
    }
}
