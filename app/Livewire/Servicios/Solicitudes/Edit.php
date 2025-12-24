<?php

namespace App\Livewire\Servicios\Solicitudes;

use App\Models\Servicios\Cliente;
use App\Models\Servicios\SolicitudServicio;
use App\Models\Stock\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public SolicitudServicio $solicitud;

    public $fecha;
    public $cliente_id;
    public $producto_id;
    public $tipo_servicio;
    public $prioridad;
    public $estado;
    public $observaciones;

    // Para búsqueda de clientes
    public $buscarCliente = '';
    public $clientesEncontrados = [];
    public $mostrarListaClientes = false;

    // Para búsqueda de productos
    public $buscarProducto = '';
    public $productosEncontrados = [];
    public $mostrarListaProductos = false;

    public function mount(SolicitudServicio $solicitud)
    {
        $this->solicitud = $solicitud;
        $this->fecha = $solicitud->fecha->format('Y-m-d');
        $this->cliente_id = $solicitud->cliente_id;
        $this->producto_id = $solicitud->producto_id;
        $this->tipo_servicio = $solicitud->tipo_servicio;
        $this->prioridad = $solicitud->prioridad;
        $this->estado = $solicitud->estado;
        $this->observaciones = $solicitud->observaciones;
        $this->buscarCliente = $solicitud->cliente->nombre ?? '';
        $this->buscarProducto = $solicitud->producto->nombre ?? '';
    }

    protected function rules()
    {
        return [
            'fecha' => 'required|date',
            'cliente_id' => ['required', Rule::exists(Cliente::class, 'id')],
            'producto_id' => ['required', Rule::exists(Producto::class, 'id')],
            'tipo_servicio' => 'required|in:mantenimiento,reparacion,diagnostico',
            'prioridad' => 'required|in:baja,media,alta',
            'estado' => 'required|in:pendiente,en_proceso,completado',
            'observaciones' => 'nullable|string',
        ];
    }

    protected $messages = [
        'fecha.required' => 'La fecha es obligatoria.',
        'cliente_id.required' => 'Debe seleccionar un cliente.',
        'producto_id.required' => 'Debe seleccionar un producto/equipo.',
        'tipo_servicio.required' => 'Debe seleccionar el tipo de servicio.',
        'prioridad.required' => 'Debe seleccionar la prioridad.',
        'estado.required' => 'Debe seleccionar el estado.',
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
            $this->mostrarListaClientes = false;
        }
    }

    public function updatedBuscarProducto()
    {
        if (strlen($this->buscarProducto) >= 2) {
            $this->productosEncontrados = Producto::where('nombre', 'ILIKE', "%{$this->buscarProducto}%")
                ->orWhere('codigo', 'ILIKE', "%{$this->buscarProducto}%")
                ->where('activo', true)
                ->limit(10)
                ->get();
            $this->mostrarListaProductos = true;
        } else {
            $this->productosEncontrados = [];
            $this->mostrarListaProductos = false;
        }
    }

    public function seleccionarProducto($productoId)
    {
        $producto = Producto::find($productoId);
        if ($producto) {
            $this->producto_id = $producto->id;
            $this->buscarProducto = $producto->nombre;
            $this->mostrarListaProductos = false;
        }
    }

    public function actualizar()
    {
        $this->validate();

        try {
            $this->solicitud->update([
                'fecha' => $this->fecha,
                'cliente_id' => $this->cliente_id,
                'producto_id' => $this->producto_id,
                'tipo_servicio' => $this->tipo_servicio,
                'prioridad' => $this->prioridad,
                'estado' => $this->estado,
                'observaciones' => $this->observaciones,
                'actualizadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Solicitud actualizada correctamente!');
            $this->redirectRoute('servicios.solicitudes.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la solicitud: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.servicios.solicitudes.edit');
    }
}
