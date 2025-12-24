<?php

namespace App\Livewire\Servicios\Clientes;

use App\Models\Servicios\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $tipo_cliente = '';
    public $documento = '';
    public $nombre = '';
    public $telefono = '';
    public $celular = '';
    public $email = '';
    public $direccion = '';
    public $observaciones = '';
    public $activo = true;

    protected function rules()
    {
        return [
            'tipo_cliente' => ['required', Rule::in(['fisica', 'juridica'])],
            'documento' => [
                'required',
                'string',
                'max:20',
                Rule::unique(Cliente::class, 'documento')
            ],
            'nombre' => 'required|string|max:200',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'activo' => 'boolean',
        ];
    }

    protected $messages = [
        'tipo_cliente.required' => 'Debe seleccionar un tipo de cliente.',
        'tipo_cliente.in' => 'El tipo de cliente seleccionado no es válido.',
        'documento.required' => 'El documento es obligatorio.',
        'documento.unique' => 'Ya existe un cliente con este documento.',
        'nombre.required' => 'El nombre/razón social es obligatorio.',
        'email.email' => 'El email debe ser una dirección válida.',
    ];

    public function guardar()
    {
        $this->validate();

        try {
            Cliente::create([
                'tipo_cliente' => $this->tipo_cliente,
                'documento' => strtoupper($this->documento),
                'nombre' => strtoupper($this->nombre),
                'telefono' => $this->telefono,
                'celular' => $this->celular,
                'email' => $this->email,
                'direccion' => $this->direccion,
                'observaciones' => $this->observaciones,
                'activo' => $this->activo,
                'creadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Cliente creado correctamente!');
            $this->redirectRoute('servicios.clientes.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el cliente: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.servicios.clientes.create');
    }
}
