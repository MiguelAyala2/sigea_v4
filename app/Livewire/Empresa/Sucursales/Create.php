<?php

namespace App\Livewire\Empresa\Sucursales;

use App\Models\Empresa\Empresa;
use App\Models\Empresa\Sucursal;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Create extends Component
{
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

    protected function rules()
    {
        return [
            'codigo_establecimiento' => 'required|string|max:10|unique:pgsql.empresa.SUCURSALES,codigo_establecimiento',
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

        $empresa = Empresa::actual();
        if (!$empresa) {
            session()->flash('error', 'Debe configurar primero los datos de la empresa.');
            return;
        }

        // Si esta sucursal será casa central, marcar todas las demás como no
        if ($this->es_casa_central) {
            Sucursal::where('empresa_id', $empresa->id)->update(['es_casa_central' => false]);
        }

        try {
            Sucursal::create([
                'empresa_id' => $empresa->id,
                'codigo_establecimiento' => $this->codigo_establecimiento,
                'nombre' => strtoupper($this->nombre),
                'direccion' => strtoupper($this->direccion),
                'departamento' => strtoupper($this->departamento),
                'ciudad' => strtoupper($this->ciudad),
                'telefono' => $this->telefono,
                'email' => $this->email,
                'es_casa_central' => $this->es_casa_central,
                'activo' => true,
                'creadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Sucursal creada correctamente.');
            $this->redirectRoute('empresa.sucursales.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la sucursal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.empresa.sucursales.create');
    }
}
