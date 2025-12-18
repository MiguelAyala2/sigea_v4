<?php

namespace App\Livewire\Compras;

use App\Models\Compras\Proveedor;
use Livewire\Component;

class ProveedorForm extends Component
{
    public ?Proveedor $proveedor = null;
    public $isEdit = false;

    // Campos del formulario
    public $razon_social = '';
    public $nombre_fantasia = '';
    public $ruc = '';
    public $dv = '';
    public $tipo_persona = 'FISICA';
    public $tipo_proveedor = 'PRODUCTOS';
    public $telefono = '';
    public $celular = '';
    public $email = '';
    public $sitio_web = '';
    public $direccion = '';
    public $ciudad = '';
    public $departamento = '';
    public $pais = 'Paraguay';
    public $contacto_nombre = '';
    public $contacto_cargo = '';
    public $contacto_telefono = '';
    public $contacto_email = '';
    public $dias_plazo_pago = 0;
    public $limite_credito = 0;
    public $descuento_habitual = 0;
    public $banco = '';
    public $tipo_cuenta = '';
    public $numero_cuenta = '';
    public $activo = true;
    public $es_nacional = true;
    public $contribuyente = true;
    public $observaciones = '';

    protected function rules()
    {
        $proveedorId = $this->isEdit ? $this->proveedor->id : null;

        return [
            'razon_social' => 'required|string|max:200',
            'nombre_fantasia' => 'nullable|string|max:200',
            'ruc' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($proveedorId) {
                    $query = Proveedor::where('ruc', $value);
                    if ($proveedorId) {
                        $query->where('id', '!=', $proveedorId);
                    }
                    if ($query->exists()) {
                        $fail('Este RUC ya está registrado');
                    }
                },
            ],
            'dv' => 'nullable|string|max:2',
            'tipo_persona' => 'required|in:FISICA,JURIDICA',
            'tipo_proveedor' => 'required|in:PRODUCTOS,SERVICIOS,AMBOS',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'sitio_web' => 'nullable|url|max:200',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'pais' => 'required|string|max:100',
            'contacto_nombre' => 'nullable|string|max:100',
            'contacto_cargo' => 'nullable|string|max:100',
            'contacto_telefono' => 'nullable|string|max:20',
            'contacto_email' => 'nullable|email|max:100',
            'dias_plazo_pago' => 'required|integer|min:0',
            'limite_credito' => 'required|numeric|min:0',
            'descuento_habitual' => 'required|numeric|min:0|max:100',
            'banco' => 'nullable|string|max:100',
            'tipo_cuenta' => 'nullable|string|max:50',
            'numero_cuenta' => 'nullable|string|max:50',
            'activo' => 'boolean',
            'es_nacional' => 'boolean',
            'contribuyente' => 'boolean',
            'observaciones' => 'nullable|string',
        ];
    }

    protected $messages = [
        'razon_social.required' => 'La razón social es obligatoria',
        'ruc.required' => 'El RUC es obligatorio',
        'ruc.unique' => 'Este RUC ya está registrado',
        'email.email' => 'El email debe ser válido',
        'sitio_web.url' => 'El sitio web debe ser una URL válida',
    ];

    public function mount(?Proveedor $proveedor = null)
    {
        if ($proveedor && $proveedor->exists) {
            $this->proveedor = $proveedor;
            $this->isEdit = true;
            $this->fill($proveedor->toArray());
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'razon_social' => $this->razon_social,
            'nombre_fantasia' => $this->nombre_fantasia,
            'ruc' => $this->ruc,
            'dv' => $this->dv,
            'tipo_persona' => $this->tipo_persona,
            'tipo_proveedor' => $this->tipo_proveedor,
            'telefono' => $this->telefono,
            'celular' => $this->celular,
            'email' => $this->email,
            'sitio_web' => $this->sitio_web,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'departamento' => $this->departamento,
            'pais' => $this->pais,
            'contacto_nombre' => $this->contacto_nombre,
            'contacto_cargo' => $this->contacto_cargo,
            'contacto_telefono' => $this->contacto_telefono,
            'contacto_email' => $this->contacto_email,
            'dias_plazo_pago' => $this->dias_plazo_pago,
            'limite_credito' => $this->limite_credito,
            'descuento_habitual' => $this->descuento_habitual,
            'banco' => $this->banco,
            'tipo_cuenta' => $this->tipo_cuenta,
            'numero_cuenta' => $this->numero_cuenta,
            'activo' => $this->activo,
            'es_nacional' => $this->es_nacional,
            'contribuyente' => $this->contribuyente,
            'observaciones' => $this->observaciones,
        ];

        if ($this->isEdit) {
            $data['actualizadoPor'] = auth()->id();
            $this->proveedor->update($data);
            session()->flash('success', 'Proveedor actualizado correctamente');
        } else {
            $data['creadoPor'] = auth()->id();
            Proveedor::create($data);
            session()->flash('success', 'Proveedor creado correctamente');
        }

        return redirect()->route('compras.proveedores.index');
    }

    public function render()
    {
        return view('livewire.compras.proveedor-form');
    }
}
