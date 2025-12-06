<?php

namespace App\Livewire\Empresa\Empresa;

use App\Models\Empresa\Empresa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    #[Validate('required|string|max:255')]
    public $razon_social = '';
    
    #[Validate('required|string|max:20')]
    public $ruc = '';
    
    #[Validate('required|string|max:1')]
    public $dv = '';
    
    #[Validate('required|string|max:255')]
    public $direccion = '';
    
    #[Validate('required|string|max:100')]
    public $ciudad = '';
    
    #[Validate('required|string|max:100')]
    public $departamento = '';
    
    #[Validate('nullable|string|max:20')]
    public $telefono = '';
    
    #[Validate('nullable|email|max:255')]
    public $email = '';

    public $empresa;

    public function mount()
    {
        // Cargar datos existentes si hay una empresa
        $this->empresa = Empresa::actual();
        
        if ($this->empresa) {
            $this->razon_social = $this->empresa->razon_social ?? '';
            $this->ruc = $this->empresa->ruc ?? '';
            $this->dv = $this->empresa->dv ?? '';
            $this->direccion = $this->empresa->direccion ?? '';
            $this->ciudad = $this->empresa->ciudad ?? '';
            $this->departamento = $this->empresa->departamento ?? '';
            $this->telefono = $this->empresa->telefono ?? '';
            $this->email = $this->empresa->email ?? '';
        }
    }

    public function updatedRuc()
    {
        // Auto-calcular DV cuando se actualiza el RUC
        if ($this->ruc) {
            $this->dv = Empresa::calcularDV($this->ruc);
        }
    }

    public function guardar()
    {
        $this->validate();

        try {
            $datos = [
                'razon_social' => $this->razon_social,
                'ruc' => $this->ruc,
                'dv' => $this->dv,
                'direccion' => $this->direccion,
                'ciudad' => $this->ciudad,
                'departamento' => $this->departamento,
                'telefono' => $this->telefono,
                'email' => $this->email,
                'actualizadoPor' => Auth::id(),
            ];

            if ($this->empresa) {
                // Actualizar empresa existente
                $this->empresa->update($datos);
                session()->flash('success', 'Datos de la empresa actualizados correctamente.');
            } else {
                // Crear nueva empresa
                $datos['creadoPor'] = Auth::id();
                $datos['activo'] = true;
                Empresa::create($datos);
                session()->flash('success', 'Empresa creada correctamente.');
            }

            $this->redirectRoute('empresa.empresa.index');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar los datos: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.empresa.empresa.edit');
    }
}
