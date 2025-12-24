<?php

namespace App\Livewire\Servicios\Descuentos;

use App\Models\Servicios\Descuento;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    public $descuentoId;
    public $codigo;
    public $descripcion = '';
    public $tipo_descuento = '';
    public $valor = '';
    public $aplicable_a = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $observaciones = '';

    protected function rules()
    {
        return [
            'descripcion' => 'required|string|max:255',
            'tipo_descuento' => 'required|in:porcentaje,monto_fijo',
            'valor' => 'required|numeric|min:0',
            'aplicable_a' => 'required|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'observaciones' => 'nullable|string',
        ];
    }

    protected $messages = [
        'descripcion.required' => 'La descripción es obligatoria.',
        'tipo_descuento.required' => 'Debe seleccionar el tipo de descuento.',
        'valor.required' => 'El valor es obligatorio.',
        'valor.min' => 'El valor debe ser mayor o igual a 0.',
        'aplicable_a.required' => 'Debe indicar a qué es aplicable el descuento.',
        'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
    ];

    public function mount($id)
    {
        $descuento = Descuento::findOrFail($id);

        $this->descuentoId = $descuento->id;
        $this->codigo = $descuento->codigo;
        $this->descripcion = $descuento->descripcion;
        $this->tipo_descuento = $descuento->tipo_descuento;
        $this->valor = $descuento->valor;
        $this->aplicable_a = $descuento->aplicable_a;
        $this->fecha_inicio = $descuento->fecha_inicio?->format('Y-m-d') ?? '';
        $this->fecha_fin = $descuento->fecha_fin?->format('Y-m-d') ?? '';
        $this->observaciones = $descuento->observaciones;
    }

    public function actualizar()
    {
        $this->validate();

        $descuento = Descuento::findOrFail($this->descuentoId);
        $descuento->update([
            'descripcion' => strtoupper($this->descripcion),
            'tipo_descuento' => $this->tipo_descuento,
            'valor' => $this->valor,
            'aplicable_a' => strtoupper($this->aplicable_a),
            'fecha_inicio' => $this->fecha_inicio ?: null,
            'fecha_fin' => $this->fecha_fin ?: null,
            'observaciones' => strtoupper($this->observaciones),
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Descuento actualizado correctamente!');
        $this->redirectRoute('servicios.descuentos.index');
    }

    public function render()
    {
        return view('livewire.servicios.descuentos.edit');
    }
}
