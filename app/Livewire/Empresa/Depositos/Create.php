<?php

namespace App\Livewire\Empresa\Depositos;

use App\Models\Empresa\Deposito;
use App\Models\Empresa\Sucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $sucursal_id = '';
    public $codigo = '';
    public $nombre = '';
    public $descripcion = '';
    public $es_principal = false;
    public $permite_venta = true;

    protected function rules()
    {
        return [
            'sucursal_id' => ['required', Rule::exists(Sucursal::class, 'id')],
            'codigo' => [
                'required',
                'string',
                'max:10',
                Rule::unique(Deposito::class, 'codigo')
                    ->when($this->sucursal_id, function ($rule) {
                        return $rule->where('sucursal_id', $this->sucursal_id);
                    })
            ],
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'es_principal' => 'boolean',
            'permite_venta' => 'boolean',
        ];
    }

    protected $messages = [
        'codigo.unique' => 'Ya existe un depósito con este código en la sucursal seleccionada.',
        'sucursal_id.required' => 'Debe seleccionar una sucursal.',
    ];

    public function guardar()
    {
        $this->validate();

        // Si este depósito será principal, marcar todos los demás de la sucursal como no principales
        if ($this->es_principal) {
            Deposito::where('sucursal_id', $this->sucursal_id)->update(['es_principal' => false]);
        }

        try {
            Deposito::create([
                'sucursal_id' => $this->sucursal_id,
                'codigo' => strtoupper($this->codigo),
                'nombre' => strtoupper($this->nombre),
                'descripcion' => $this->descripcion,
                'es_principal' => $this->es_principal,
                'permite_venta' => $this->permite_venta,
                'activo' => true,
                'creadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Depósito creado correctamente.');
            $this->redirectRoute('empresa.depositos.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el depósito: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.empresa.depositos.create', [
            'sucursales' => Sucursal::with('empresa')
                ->whereHas('empresa')
                ->soloActivos()
                ->orderBy('codigo_establecimiento')
                ->get(),
        ]);
    }
}
