<?php

namespace App\Livewire\Empresa\Timbrados;

use App\Models\Empresa\Empresa;
use App\Models\Empresa\Timbrado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public $empresaId;

    #[Validate]
    public $numero_timbrado = '';
    public $fecha_inicio_vigencia = '';
    public $fecha_fin_vigencia = '';
    public $tipo_documento = 'factura';
    public $numero_desde = 1;
    public $numero_hasta = '';
    public $es_electronico = false;
    public $cdc_ambiente = '';

    public function mount()
    {
        $empresa = Empresa::actual();
        if (!$empresa) {
            session()->flash('error', 'Debe configurar primero los datos de la empresa.');
            $this->redirectRoute('empresa.empresa.index');
            return;
        }
        $this->empresaId = $empresa->id;
    }

    protected function rules()
    {
        return [
            'numero_timbrado' => ['required', 'string', 'max:15'],
            'fecha_inicio_vigencia' => ['required', 'date'],
            'fecha_fin_vigencia' => ['required', 'date', 'after:fecha_inicio_vigencia'],
            'tipo_documento' => ['required', Rule::in(array_keys(Timbrado::TIPOS_DOCUMENTO))],
            'numero_desde' => ['required', 'integer', 'min:1'],
            'numero_hasta' => ['required', 'integer', 'gt:numero_desde'],
            'es_electronico' => ['boolean'],
            'cdc_ambiente' => [
                Rule::requiredIf($this->es_electronico),
                'nullable',
                Rule::in(['produccion', 'test'])
            ],
        ];
    }

    protected $messages = [
        'numero_timbrado.required' => 'El número de timbrado es obligatorio.',
        'fecha_inicio_vigencia.required' => 'La fecha de inicio es obligatoria.',
        'fecha_fin_vigencia.required' => 'La fecha de fin es obligatoria.',
        'fecha_fin_vigencia.after' => 'La fecha de fin debe ser posterior a la de inicio.',
        'numero_hasta.gt' => 'El número hasta debe ser mayor al número desde.',
        'cdc_ambiente.required' => 'El ambiente CDC es obligatorio para timbrados electrónicos.',
    ];

    public function guardar()
    {
        $this->validate();

        Timbrado::create([
            'empresa_id' => $this->empresaId,
            'numero_timbrado' => $this->numero_timbrado,
            'fecha_inicio_vigencia' => $this->fecha_inicio_vigencia,
            'fecha_fin_vigencia' => $this->fecha_fin_vigencia,
            'tipo_documento' => $this->tipo_documento,
            'numero_desde' => $this->numero_desde,
            'numero_hasta' => $this->numero_hasta,
            'numero_actual' => $this->numero_desde - 1,
            'es_electronico' => $this->es_electronico,
            'cdc_ambiente' => $this->es_electronico ? $this->cdc_ambiente : null,
            'activo' => true,
            'creadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Timbrado creado correctamente!');
        $this->redirectRoute('empresa.timbrados.index');
    }

    public function render()
    {
        return view('livewire.empresa.timbrados.create', [
            'tiposDocumento' => Timbrado::TIPOS_DOCUMENTO,
            'ambientes' => Timbrado::AMBIENTES,
        ]);
    }
}