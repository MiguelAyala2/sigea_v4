<?php

namespace App\Livewire\Empresa\PuntosExpedicion;

use App\Models\Empresa\PuntoExpedicion;
use App\Models\Empresa\Sucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $sucursal_id = '';
    public $codigo = '';
    public $nombre = '';
    public $tipo = 'caja';

    protected function rules()
    {
        return [
            'sucursal_id' => ['required', Rule::exists(Sucursal::class, 'id')],
            'codigo' => [
                'required',
                'string',
                'max:10',
                Rule::unique(PuntoExpedicion::class, 'codigo')
                    ->when($this->sucursal_id, function ($rule) {
                        return $rule->where('sucursal_id', $this->sucursal_id);
                    })
            ],
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:caja,terminal,web',
        ];
    }

    protected $messages = [
        'codigo.unique' => 'Ya existe un punto de expedición con este código en la sucursal seleccionada.',
        'sucursal_id.required' => 'Debe seleccionar una sucursal.',
    ];

    public function guardar()
    {
        $this->validate();

        try {
            PuntoExpedicion::create([
                'sucursal_id' => $this->sucursal_id,
                'codigo' => strtoupper($this->codigo),
                'nombre' => strtoupper($this->nombre),
                'tipo' => $this->tipo,
                'activo' => true,
                'creadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Punto de expedición creado correctamente.');
            $this->redirectRoute('empresa.puntos-expedicion.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el punto de expedición: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.empresa.puntos-expedicion.create', [
            'sucursales' => Sucursal::with('empresa')
                ->whereHas('empresa')
                ->soloActivos()
                ->orderBy('codigo_establecimiento')
                ->get(),
            'tipos' => PuntoExpedicion::TIPOS,
        ]);
    }
}
