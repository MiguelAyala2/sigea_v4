<?php

namespace App\Livewire\Servicios\Promociones;

use App\Models\Servicios\Promocion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $nombre = '';
    public $tipo = '';
    public $descuento = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $descripcion = '';

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:general,servicio,producto',
            'descuento' => 'required|numeric|min:0|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre de la promoción es obligatorio.',
        'tipo.required' => 'Debe seleccionar un tipo de promoción.',
        'descuento.required' => 'El descuento es obligatorio.',
        'descuento.min' => 'El descuento debe ser mayor o igual a 0.',
        'descuento.max' => 'El descuento no puede ser mayor a 100.',
        'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
        'fecha_fin.required' => 'La fecha de fin es obligatoria.',
        'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
    ];

    public function mount()
    {
        $this->fecha_inicio = date('Y-m-d');
        $this->fecha_fin = date('Y-m-d');
    }

    public function guardar()
    {
        $this->validate();

        Promocion::create([
            'codigo' => Promocion::generarCodigo(),
            'nombre' => strtoupper($this->nombre),
            'tipo' => $this->tipo,
            'descuento' => $this->descuento,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'descripcion' => strtoupper($this->descripcion),
            'activo' => true,
            'creadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Promoción registrada correctamente!');
        $this->redirectRoute('servicios.promociones.index');
    }

    public function render()
    {
        return view('livewire.servicios.promociones.create');
    }
}
