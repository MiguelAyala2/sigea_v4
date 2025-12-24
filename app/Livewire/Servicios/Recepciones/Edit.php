<?php

namespace App\Livewire\Servicios\Recepciones;

use App\Models\Servicios\Cliente;
use App\Models\Servicios\Recepcion;
use App\Models\Servicios\SolicitudServicio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Recepcion $recepcion;
    public $fecha_recepcion;
    public $cliente_id = '';
    public $solicitud_id = '';
    public $producto_id = '';
    public $contacto_cliente = '';

    // Datos del equipo
    public $tipo_equipo = '';
    public $marca = '';
    public $modelo = '';
    public $numero_serie = '';
    public $estado_recepcion = '';
    public $estado = '';

    // Detalles del servicio
    public $descripcion_problema = '';
    public $accesorios_recibidos = '';

    // Para búsqueda de clientes
    public $buscarCliente = '';
    public $clientesEncontrados = [];
    public $mostrarListaClientes = false;

    // Solicitudes del cliente seleccionado
    public $solicitudesCliente = [];
    public $solicitudSeleccionada = null;

    public function mount(Recepcion $recepcion)
    {
        $this->recepcion = $recepcion;
        $this->fecha_recepcion = $recepcion->fecha_recepcion->format('Y-m-d');
        $this->cliente_id = $recepcion->cliente_id;
        $this->solicitud_id = $recepcion->solicitud_id;
        $this->producto_id = $recepcion->producto_id;
        $this->contacto_cliente = $recepcion->contacto_cliente;
        $this->tipo_equipo = $recepcion->tipo_equipo;
        $this->marca = $recepcion->marca;
        $this->modelo = $recepcion->modelo;
        $this->numero_serie = $recepcion->numero_serie;
        $this->estado_recepcion = $recepcion->estado_recepcion;
        $this->estado = $recepcion->estado;
        $this->descripcion_problema = $recepcion->descripcion_problema;
        $this->accesorios_recibidos = $recepcion->accesorios_recibidos;

        // Cargar el cliente y sus solicitudes
        $cliente = Cliente::find($this->cliente_id);
        if ($cliente) {
            $this->buscarCliente = $cliente->nombre;
            $this->cargarSolicitudesCliente();

            // Cargar la solicitud seleccionada
            $this->solicitudSeleccionada = SolicitudServicio::with(['producto'])->find($this->solicitud_id);
        }
    }

    protected function rules()
    {
        return [
            'fecha_recepcion' => 'required|date',
            'cliente_id' => ['required', Rule::exists(Cliente::class, 'id')],
            'solicitud_id' => ['required', Rule::exists(SolicitudServicio::class, 'id')],
            'contacto_cliente' => 'nullable|string|max:255',
            'tipo_equipo' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'estado_recepcion' => 'required|in:bueno,regular,malo',
            'estado' => 'required|in:pendiente,en_proceso,completado',
            'descripcion_problema' => 'required|string',
            'accesorios_recibidos' => 'nullable|string',
        ];
    }

    protected $messages = [
        'fecha_recepcion.required' => 'La fecha de recepción es obligatoria.',
        'cliente_id.required' => 'Debe seleccionar un cliente.',
        'solicitud_id.required' => 'Debe seleccionar una solicitud.',
        'estado_recepcion.required' => 'Debe seleccionar el estado de recepción.',
        'estado.required' => 'Debe seleccionar el estado.',
        'descripcion_problema.required' => 'La descripción del problema es obligatoria.',
    ];

    public function updatedBuscarCliente()
    {
        if (strlen($this->buscarCliente) >= 2) {
            $this->clientesEncontrados = Cliente::where('nombre', 'ILIKE', "%{$this->buscarCliente}%")
                ->orWhere('documento', 'ILIKE', "%{$this->buscarCliente}%")
                ->where('activo', true)
                ->limit(10)
                ->get();
            $this->mostrarListaClientes = true;
        } else {
            $this->clientesEncontrados = [];
            $this->mostrarListaClientes = false;
        }
    }

    public function seleccionarCliente($clienteId)
    {
        $cliente = Cliente::find($clienteId);
        if ($cliente) {
            $this->cliente_id = $cliente->id;
            $this->buscarCliente = $cliente->nombre;
            $this->contacto_cliente = $cliente->telefono ?? $cliente->celular ?? $cliente->email;
            $this->mostrarListaClientes = false;

            // Cargar solicitudes del cliente
            $this->cargarSolicitudesCliente();
        }
    }

    public function cargarSolicitudesCliente()
    {
        if ($this->cliente_id) {
            $this->solicitudesCliente = SolicitudServicio::where('cliente_id', $this->cliente_id)
                ->where('activo', true)
                ->with(['producto'])
                ->orderBy('fecha', 'desc')
                ->get();
        }
    }

    public function updatedSolicitudId($value)
    {
        if ($value) {
            $this->solicitudSeleccionada = SolicitudServicio::with(['producto'])->find($value);

            if ($this->solicitudSeleccionada) {
                // Cargar datos de la solicitud
                $this->producto_id = $this->solicitudSeleccionada->producto_id;
            }
        } else {
            $this->solicitudSeleccionada = null;
        }
    }

    public function actualizar()
    {
        $this->validate();

        try {
            $this->recepcion->update([
                'fecha_recepcion' => $this->fecha_recepcion,
                'solicitud_id' => $this->solicitud_id,
                'cliente_id' => $this->cliente_id,
                'producto_id' => $this->producto_id,
                'contacto_cliente' => $this->contacto_cliente,
                'tipo_equipo' => $this->tipo_equipo,
                'marca' => $this->marca,
                'modelo' => $this->modelo,
                'numero_serie' => $this->numero_serie,
                'estado_recepcion' => $this->estado_recepcion,
                'estado' => $this->estado,
                'descripcion_problema' => strtoupper($this->descripcion_problema),
                'accesorios_recibidos' => strtoupper($this->accesorios_recibidos),
                'actualizadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Recepción actualizada correctamente!');
            $this->redirectRoute('servicios.recepciones.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la recepción: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.servicios.recepciones.edit');
    }
}
