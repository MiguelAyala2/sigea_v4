<?php

namespace App\Livewire\Servicios\TiposServicio;

use App\Models\Servicios\TipoServicio;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    public $tipoServicioId;
    public $codigo = '';
    public $descripcion = '';
    public $costo = 0;
    public $activo = true;

    public function mount($id)
    {
        $this->tipoServicioId = $id;
        $tipoServicio = TipoServicio::findOrFail($id);

        $this->codigo = $tipoServicio->codigo;
        $this->descripcion = $tipoServicio->descripcion;
        $this->costo = $tipoServicio->costo;
        $this->activo = $tipoServicio->activo;
    }

    protected function rules()
    {
        return [
            'codigo' => 'required|string|max:20|unique:servicios.TIPOS_SERVICIO,codigo,' . $this->tipoServicioId,
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

    public function actualizar()
    {
        $this->validate();

        try {
            $tipoServicio = TipoServicio::findOrFail($this->tipoServicioId);

            $tipoServicio->update([
                'codigo' => strtoupper($this->codigo),
                'descripcion' => strtoupper($this->descripcion),
                'costo' => $this->costo,
                'activo' => $this->activo,
                'actualizadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Tipo de Servicio actualizado correctamente!');
            $this->redirectRoute('servicios.tipos-servicio.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el tipo de servicio: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.servicios.tipos-servicio.edit');
    }
}
