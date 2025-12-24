<?php

namespace App\Livewire\Servicios\Diagnosticos;

use App\Models\Servicios\Cliente;
use App\Models\Servicios\Diagnostico;
use App\Models\Servicios\DiagnosticoRepuesto;
use App\Models\Servicios\DiagnosticoTipoServicio;
use App\Models\Servicios\Recepcion;
use App\Models\Servicios\SolicitudServicio;
use App\Models\Servicios\TipoServicio;
use App\Models\Stock\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $fecha_diagnostico;
    public $cliente_id = '';
    public $solicitud_id = '';
    public $recepcion_id = '';
    public $producto_id = '';

    // Datos del diagnóstico
    public $problema_detectado = '';
    public $solucion_propuesta = '';
    public $estado_diagnostico = '';
    public $estado = 'pendiente';
    public $observaciones = '';

    // Para búsqueda de clientes
    public $buscarCliente = '';
    public $clientesEncontrados = [];
    public $mostrarListaClientes = false;

    // Solicitudes y recepciones del cliente
    public $solicitudesCliente = [];
    public $recepcionesCliente = [];
    public $solicitudSeleccionada = null;
    public $recepcionSeleccionada = null;

    // Repuestos necesarios
    public $repuestos = [];
    public $buscarRepuesto = '';
    public $repuestosEncontrados = [];
    public $mostrarListaRepuestos = false;

    // Tipos de Servicio
    public $tiposServicio = [];
    public $buscarTipoServicio = '';
    public $tiposServicioEncontrados = [];
    public $mostrarListaTiposServicio = false;

    public function mount()
    {
        $this->fecha_diagnostico = date('Y-m-d');
    }

    protected function rules()
    {
        return [
            'fecha_diagnostico' => 'required|date',
            'cliente_id' => ['required', Rule::exists(Cliente::class, 'id')],
            'solicitud_id' => ['required', Rule::exists(SolicitudServicio::class, 'id')],
            'recepcion_id' => ['required', Rule::exists(Recepcion::class, 'id')],
            'problema_detectado' => 'required|string',
            'solucion_propuesta' => 'nullable|string',
            'estado_diagnostico' => 'required|in:reparable,no_reparable,requiere_repuestos',
            'estado' => 'required|in:pendiente,en_proceso,completado',
            'observaciones' => 'nullable|string',
        ];
    }

    protected $messages = [
        'fecha_diagnostico.required' => 'La fecha de diagnóstico es obligatoria.',
        'cliente_id.required' => 'Debe seleccionar un cliente.',
        'solicitud_id.required' => 'Debe seleccionar una solicitud.',
        'recepcion_id.required' => 'Debe seleccionar una recepción.',
        'problema_detectado.required' => 'El problema detectado es obligatorio.',
        'estado_diagnostico.required' => 'Debe seleccionar el estado del diagnóstico.',
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

            // Cargar solicitudes y recepciones del cliente
            $this->cargarSolicitudesYRecepciones();
        }
    }

    public function cargarSolicitudesYRecepciones()
    {
        if ($this->cliente_id) {
            // Cargar solicitudes del cliente
            $this->solicitudesCliente = SolicitudServicio::where('cliente_id', $this->cliente_id)
                ->where('activo', true)
                ->with(['producto'])
                ->orderBy('fecha', 'desc')
                ->get();

            // Resetear selección
            $this->solicitud_id = '';
            $this->recepcion_id = '';
            $this->recepcionesCliente = [];
            $this->solicitudSeleccionada = null;
            $this->recepcionSeleccionada = null;
        }
    }

    public function updatedSolicitudId($value)
    {
        if ($value) {
            $this->solicitudSeleccionada = SolicitudServicio::with(['producto'])->find($value);

            if ($this->solicitudSeleccionada) {
                // Cargar recepciones relacionadas a esta solicitud
                $this->recepcionesCliente = Recepcion::where('solicitud_id', $value)
                    ->where('activo', true)
                    ->with(['producto'])
                    ->orderBy('fecha_recepcion', 'desc')
                    ->get();

                $this->producto_id = $this->solicitudSeleccionada->producto_id;
                $this->recepcion_id = '';
                $this->recepcionSeleccionada = null;
            }
        } else {
            $this->solicitudSeleccionada = null;
            $this->recepcionesCliente = [];
            $this->recepcion_id = '';
            $this->recepcionSeleccionada = null;
        }
    }

    public function updatedRecepcionId($value)
    {
        if ($value) {
            $this->recepcionSeleccionada = Recepcion::with(['producto'])->find($value);

            if ($this->recepcionSeleccionada) {
                // Pre-llenar el problema detectado con la descripción del problema de la recepción
                $this->problema_detectado = $this->recepcionSeleccionada->descripcion_problema ?? '';
            }
        } else {
            $this->recepcionSeleccionada = null;
        }
    }

    // Manejo de repuestos
    public function updatedBuscarRepuesto()
    {
        if (strlen($this->buscarRepuesto) >= 2) {
            $this->repuestosEncontrados = Producto::where('nombre', 'ILIKE', "%{$this->buscarRepuesto}%")
                ->orWhere('codigo', 'ILIKE', "%{$this->buscarRepuesto}%")
                ->where('activo', true)
                ->limit(10)
                ->get();
            $this->mostrarListaRepuestos = true;
        } else {
            $this->repuestosEncontrados = [];
            $this->mostrarListaRepuestos = false;
        }
    }

    public function agregarRepuesto($productoId)
    {
        $producto = Producto::with(['precioActual', 'precios' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(1);
        }])->find($productoId);

        if ($producto) {
            // Verificar si ya está agregado
            $existe = collect($this->repuestos)->contains('producto_id', $producto->id);

            if (!$existe) {
                // Intentar obtener el precio de diferentes fuentes
                $precioUnitario = 0;

                // 1. Intentar precio actual
                if ($producto->precioActual) {
                    $precioUnitario = $producto->precioActual->precio_venta;
                }
                // 2. Si no hay precio actual, intentar el último precio registrado
                elseif ($producto->precios && $producto->precios->isNotEmpty()) {
                    $precioUnitario = $producto->precios->first()->precio_venta;
                }

                $this->repuestos[] = [
                    'producto_id' => $producto->id,
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'cantidad' => 1,
                    'costo_unitario' => $precioUnitario,
                ];
            }

            $this->buscarRepuesto = '';
            $this->mostrarListaRepuestos = false;
        }
    }

    public function eliminarRepuesto($index)
    {
        unset($this->repuestos[$index]);
        $this->repuestos = array_values($this->repuestos);
    }

    // Manejo de tipos de servicio
    public function updatedBuscarTipoServicio()
    {
        if (strlen($this->buscarTipoServicio) >= 2) {
            $this->tiposServicioEncontrados = TipoServicio::where('descripcion', 'ILIKE', "%{$this->buscarTipoServicio}%")
                ->orWhere('codigo', 'ILIKE', "%{$this->buscarTipoServicio}%")
                ->where('activo', true)
                ->limit(10)
                ->get();
            $this->mostrarListaTiposServicio = true;
        } else {
            $this->tiposServicioEncontrados = [];
            $this->mostrarListaTiposServicio = false;
        }
    }

    public function agregarTipoServicio($tipoServicioId)
    {
        $tipoServicio = TipoServicio::find($tipoServicioId);
        if ($tipoServicio) {
            // Verificar si ya está agregado
            $existe = collect($this->tiposServicio)->contains('tipo_servicio_id', $tipoServicio->id);

            if (!$existe) {
                $this->tiposServicio[] = [
                    'tipo_servicio_id' => $tipoServicio->id,
                    'codigo' => $tipoServicio->codigo,
                    'descripcion' => $tipoServicio->descripcion,
                    'cantidad' => 1,
                    'costo_unitario' => $tipoServicio->costo,
                ];
            }

            $this->buscarTipoServicio = '';
            $this->mostrarListaTiposServicio = false;
        }
    }

    public function eliminarTipoServicio($index)
    {
        unset($this->tiposServicio[$index]);
        $this->tiposServicio = array_values($this->tiposServicio);
    }

    public function calcularSubtotalTipoServicio($tipoServicio)
    {
        return $tipoServicio['cantidad'] * $tipoServicio['costo_unitario'];
    }

    public function calcularTotalTiposServicio()
    {
        $total = 0;
        foreach ($this->tiposServicio as $tipoServicio) {
            $total += $this->calcularSubtotalTipoServicio($tipoServicio);
        }
        return $total;
    }

    public function calcularSubtotal($repuesto)
    {
        return $repuesto['cantidad'] * $repuesto['costo_unitario'];
    }

    public function calcularTotalRepuestos()
    {
        $total = 0;
        foreach ($this->repuestos as $repuesto) {
            $total += $this->calcularSubtotal($repuesto);
        }
        return $total;
    }

    public function calcularTotalGeneral()
    {
        return $this->calcularTotalRepuestos() + $this->calcularTotalTiposServicio();
    }

    public function guardar()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $diagnostico = Diagnostico::create([
                'numero_diagnostico' => Diagnostico::generarNumeroDiagnostico(),
                'fecha_diagnostico' => $this->fecha_diagnostico,
                'solicitud_id' => $this->solicitud_id,
                'recepcion_id' => $this->recepcion_id,
                'cliente_id' => $this->cliente_id,
                'producto_id' => $this->producto_id,
                'problema_detectado' => strtoupper($this->problema_detectado),
                'solucion_propuesta' => strtoupper($this->solucion_propuesta),
                'estado_diagnostico' => $this->estado_diagnostico,
                'estado' => $this->estado,
                'observaciones' => strtoupper($this->observaciones),
                'activo' => true,
                'creadoPor' => Auth::id(),
            ]);

            // Guardar repuestos
            if (!empty($this->repuestos)) {
                foreach ($this->repuestos as $repuesto) {
                    DiagnosticoRepuesto::create([
                        'diagnostico_id' => $diagnostico->id,
                        'producto_id' => $repuesto['producto_id'],
                        'cantidad' => $repuesto['cantidad'],
                        'costo' => $repuesto['costo_unitario'],
                    ]);
                }
            }

            // Guardar tipos de servicio
            if (!empty($this->tiposServicio)) {
                foreach ($this->tiposServicio as $tipoServicio) {
                    $subtotal = $tipoServicio['cantidad'] * $tipoServicio['costo_unitario'];

                    DiagnosticoTipoServicio::create([
                        'diagnostico_id' => $diagnostico->id,
                        'tipo_servicio_id' => $tipoServicio['tipo_servicio_id'],
                        'cantidad' => $tipoServicio['cantidad'],
                        'costo_unitario' => $tipoServicio['costo_unitario'],
                        'subtotal' => $subtotal,
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', 'Diagnóstico registrado correctamente!');
            $this->redirectRoute('servicios.diagnosticos.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar el diagnóstico: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.servicios.diagnosticos.create');
    }
}
