<?php

namespace App\Livewire\Servicios\TiposServicio;

use App\Models\Servicios\TipoServicio;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $codigo = '';
    public $descripcion = '';
    public $costo = 0;
    public $activo = true;

    public function mount()
    {
        $this->codigo = TipoServicio::generarCodigo();
    }

    protected function rules()
    {
        return [
            'codigo' => 'required|string|max:20|unique:servicios.TIPOS_SERVICIO,codigo',
            'descripcion' => 'required|string|max:255',
            'costo' => 'required|numeric|min:0',
            'activo' => 'boolean',
        ];
    }

    protected $messages = [
        'codigo.required' => 'El código es obligatorio.',
        'codigo.unique' => 'Este código ya existe.',
        'descripcion.required' => 'La descripción es obligatoria.',
        'costo.required' => 'El costo es obligatorio.',
        'costo.numeric' => 'El costo debe ser un número.',
        'costo.min' => 'El costo debe ser mayor o igual a 0.',
    ];

    public function guardar()
    {
        $this->validate();

        try {
            TipoServicio::create([
                'codigo' => $this->codigo, // Ya viene generado automáticamente
                'descripcion' => strtoupper($this->descripcion),
                'costo' => $this->costo,
                'activo' => $this->activo,
                'creadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Tipo de Servicio registrado correctamente!');
            $this->redirectRoute('servicios.tipos-servicio.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el tipo de servicio: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.servicios.tipos-servicio.create');
    }
}
