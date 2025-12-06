<?php

namespace App\Livewire\Empresa\Sucursales;

use App\Models\Empresa\Sucursal;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public $sucursal;
    
    #[Validate('required|string|max:10')]
    public $codigo_establecimiento = '';
    
    #[Validate('required|string|max:100')]
    public $nombre = '';
    
    #[Validate('required|string|max:255')]
    public $direccion = '';
    
    #[Validate('required|string|max:50')]
    public $departamento = '';
    
    #[Validate('required|string|max:50')]
    public $ciudad = '';
    
    #[Validate('nullable|string|max:30')]
    public $telefono = '';
    
    #[Validate('nullable|email|max:100')]
    public $email = '';
    
    #[Validate('boolean')]
    public $es_casa_central = false;

    public function mount($sucursal)
    {
        $this->sucursal = Sucursal::findOrFail($sucursal);
        
        $this->codigo_establecimiento = $this->sucursal->codigo_establecimiento;
        $this->nombre = $this->sucursal->nombre;
        $this->direccion = $this->sucursal->direccion;
        $this->departamento = $this->sucursal->departamento;
        $this->ciudad = $this->sucursal->ciudad;
        $this->telefono = $this->sucursal->telefono;
        $this->email = $this->sucursal->email;
        $this->es_casa_central = $this->sucursal->es_casa_central;
    }

    protected function rules()
    {
        return [
            'codigo_establecimiento' => 'required|string|max:10|unique:empresa.SUCURSALES,codigo_establecimiento,' . $this->sucursal->id,
            'nombre' => 'required|string|max:100',
            'direccion' => 'required|string|max:255',
            'departamento' => 'required|string|max:50',
            'ciudad' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'es_casa_central' => 'boolean',
        ];
    }

    public function guardar()
    {
        $this->validate();

        // Si esta sucursal será casa central, marcar todas las demás como no
        if ($this->es_casa_central && !$this->sucursal->es_casa_central) {
            Sucursal::where('empresa_id', $this->sucursal->empresa_id)
                ->where('id', '!=', $this->sucursal->id)
                ->update(['es_casa_central' => false]);
        }

        try {
            $this->sucursal->update([
                'codigo_establecimiento' => $this->codigo_establecimiento,
                'nombre' => strtoupper($this->nombre),
                'direccion' => strtoupper($this->direccion),
                'departamento' => strtoupper($this->departamento),
                'ciudad' => strtoupper($this->ciudad),
                'telefono' => $this->telefono,
                'email' => $this->email,
                'es_casa_central' => $this->es_casa_central,
                'actualizadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Sucursal actualizada correctamente.');
            $this->redirectRoute('empresa.sucursales.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la sucursal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.empresa.sucursales.edit');
    }
}
