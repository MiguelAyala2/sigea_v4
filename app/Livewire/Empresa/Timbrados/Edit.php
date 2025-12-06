<?php

namespace App\Livewire\Empresa\Timbrados;

use App\Models\Empresa\Timbrado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public $timbradoId;
    public $numero_actual;

    #[Validate]
    public $numero_timbrado = '';
    public $fecha_inicio_vigencia = '';
    public $fecha_fin_vigencia = '';
    public $tipo_documento = 'factura';
    public $numero_desde = 1;
    public $numero_hasta = '';
    public $es_electronico = false;
    public $cdc_ambiente = '';

    public function mount(Timbrado $timbrado)
    {
        $this->timbradoId = $timbrado->id;
        $this->numero_actual = $timbrado->numero_actual;
        $this->numero_timbrado = $timbrado->numero_timbrado;
        $this->fecha_inicio_vigencia = $timbrado->fecha_inicio_vigencia->format('Y-m-d');
        $this->fecha_fin_vigencia = $timbrado->fecha_fin_vigencia->format('Y-m-d');
        $this->tipo_documento = $timbrado->tipo_documento;
        $this->numero_desde = $timbrado->numero_desde;
        $this->numero_hasta = $timbrado->numero_hasta;
        $this->es_electronico = $timbrado->es_electronico;
        $this->cdc_ambiente = $timbrado->cdc_ambiente;
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
        'cdc_ambiente.required' => 'El ambiente CDC es obligatorio para timbrados electrónicos.',
    ];

    public function guardar()
    {
        $this->validate();

        // No permitir cambiar numero_desde si ya se usó
        $timbrado = Timbrado::findOrFail($this->timbradoId);
        if ($this->numero_actual > 0) {
            $this->numero_desde = $timbrado->numero_desde;
        }

        $timbrado->update([
            'numero_timbrado' => $this->numero_timbrado,
            'fecha_inicio_vigencia' => $this->fecha_inicio_vigencia,
            'fecha_fin_vigencia' => $this->fecha_fin_vigencia,
            'tipo_documento' => $this->tipo_documento,
            'numero_desde' => $this->numero_desde,
            'numero_hasta' => $this->numero_hasta,
            'es_electronico' => $this->es_electronico,
            'cdc_ambiente' => $this->es_electronico ? $this->cdc_ambiente : null,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Timbrado actualizado correctamente!');
        $this->redirectRoute('empresa.timbrados.index');
    }

    public function render()
    {
        return view('livewire.empresa.timbrados.edit', [
            'tiposDocumento' => Timbrado::TIPOS_DOCUMENTO,
            'ambientes' => Timbrado::AMBIENTES,
        ]);
    }
}
