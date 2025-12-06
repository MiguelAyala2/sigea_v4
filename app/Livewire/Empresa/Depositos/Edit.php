<?php

namespace App\Livewire\Empresa\Depositos;

use App\Models\Empresa\Deposito;
use App\Models\Empresa\Sucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public $deposito;
    public $sucursal_id = '';
    public $codigo = '';
    public $nombre = '';
    public $descripcion = '';
    public $es_principal = false;
    public $permite_venta = true;

    public function mount($deposito)
    {
        $this->deposito = Deposito::findOrFail($deposito);
        
        $this->sucursal_id = $this->deposito->sucursal_id;
        $this->codigo = $this->deposito->codigo;
        $this->nombre = $this->deposito->nombre;
        $this->descripcion = $this->deposito->descripcion;
        $this->es_principal = $this->deposito->es_principal;
        $this->permite_venta = $this->deposito->permite_venta;
    }

    protected function rules()
    {
        return [
            'sucursal_id' => ['required', Rule::exists(Sucursal::class, 'id')],
            'codigo' => [
                'required',
                'string',
                'max:10',
                Rule::unique(Deposito::class, 'codigo')
                    ->ignore($this->deposito->id)
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
        if ($this->es_principal && !$this->deposito->es_principal) {
            Deposito::where('sucursal_id', $this->sucursal_id)
                ->where('id', '!=', $this->deposito->id)
                ->update(['es_principal' => false]);
        }

        try {
            $this->deposito->update([
                'sucursal_id' => $this->sucursal_id,
                'codigo' => strtoupper($this->codigo),
                'nombre' => strtoupper($this->nombre),
                'descripcion' => $this->descripcion,
                'es_principal' => $this->es_principal,
                'permite_venta' => $this->permite_venta,
                'actualizadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Depósito actualizado correctamente.');
            $this->redirectRoute('empresa.depositos.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el depósito: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.empresa.depositos.edit', [
            'sucursales' => Sucursal::with('empresa')
                ->whereHas('empresa')
                ->soloActivos()
                ->orderBy('codigo_establecimiento')
                ->get(),
        ]);
    }
}
