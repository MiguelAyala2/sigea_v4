<?php

namespace App\Livewire\Servicios\Solicitudes;

use App\Models\Servicios\Cliente;
use App\Models\Servicios\SolicitudServicio;
use App\Models\Stock\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $fecha;
    public $cliente_id = '';
    public $producto_id = '';
    public $tipo_servicio = '';
    public $prioridad = '';
    public $observaciones = '';

    // Para búsqueda de clientes
    public $buscarCliente = '';
    public $clientesEncontrados = [];
    public $mostrarListaClientes = false;

    // Para búsqueda de productos
    public $buscarProducto = '';
    public $productosEncontrados = [];
    public $mostrarListaProductos = false;

    public function mount()
    {
        $this->fecha = date('Y-m-d');
    }

    protected function rules()
    {
        return [
            'fecha' => 'required|date',
            'cliente_id' => ['required', Rule::exists(Cliente::class, 'id')],
            'producto_id' => ['required', Rule::exists(Producto::class, 'id')],
            'tipo_servicio' => 'required|in:mantenimiento,reparacion,diagnostico',
            'prioridad' => 'required|in:baja,media,alta',
            'observaciones' => 'nullable|string',
        ];
    }

    protected $messages = [
        'fecha.required' => 'La fecha es obligatoria.',
        'cliente_id.required' => 'Debe seleccionar un cliente.',
        'cliente_id.exists' => 'El cliente seleccionado no existe.',
        'producto_id.required' => 'Debe seleccionar un producto/equipo.',
        'producto_id.exists' => 'El producto seleccionado no existe.',
        'tipo_servicio.required' => 'Debe seleccionar el tipo de servicio.',
        'prioridad.required' => 'Debe seleccionar la prioridad.',
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

    public function guardar()
    {
        $this->validate();

        try {
            SolicitudServicio::create([
                'numero_solicitud' => SolicitudServicio::generarNumeroSolicitud(),
                'fecha' => $this->fecha,
                'cliente_id' => $this->cliente_id,
                'producto_id' => $this->producto_id,
                'tipo_servicio' => $this->tipo_servicio,
                'prioridad' => $this->prioridad,
                'estado' => 'pendiente',
                'observaciones' => $this->observaciones,
                'activo' => true,
                'creadoPor' => Auth::id(),
            ]);

            session()->flash('success', 'Solicitud de servicio creada correctamente!');
            $this->redirectRoute('servicios.solicitudes.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la solicitud: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.servicios.solicitudes.create');
    }
}
