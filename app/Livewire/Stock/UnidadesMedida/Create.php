<?php

namespace App\Livewire\Stock\UnidadesMedida;

use App\Models\Stock\UnidadMedida;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Create extends Component
{
    #[Validate]
    public $nombre = '';
    public $simbolo = '';
    public $permite_decimales = false;

    protected function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique(UnidadMedida::class, 'nombre')->whereNull('deleted_at')
            ],
            'simbolo' => ['required', 'string', 'max:10'],
            'permite_decimales' => ['boolean'],
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.unique' => 'Este nombre ya está registrado.',
        'simbolo.required' => 'El símbolo es obligatorio.',
    ];

    public function guardar()
    {
        $this->validate();

        UnidadMedida::create([
            'nombre' => $this->nombre,
            'simbolo' => $this->simbolo,
            'permite_decimales' => $this->permite_decimales,
            'activo' => true,
            'creadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Unidad de medida creada correctamente!');
        return redirect()->route('stock.unidades-medida.index');
    }

    public function render()
    {
        return view('livewire.stock.unidades-medida.create');
    }
}
