<?php

namespace App\Livewire\Ventas;

use App\Models\Ventas\Factura;
use App\Models\Ventas\FacturaDetalle;
use App\Models\Ventas\FacturaFormaPago;
use App\Models\Ventas\PedidoCliente;
use App\Models\Ventas\AperturaCaja;
use App\Models\Servicios\Cliente;
use App\Models\Servicios\OrdenServicio;
use App\Models\Stock\Producto;
use App\Models\Empresa\Timbrado;
use App\Models\Empresa\PuntoExpedicion;
use App\Models\Empresa\Sucursal;
use App\Models\Empresa\Deposito;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FacturaForm extends Component
{
    // Cabecera
    public $facturaId;
    public $factura;
    public $numero_factura;
    public $fecha_emision;
    public $fecha_vencimiento;
    public $cliente_id;
    public $pedido_cliente_id;
    public $cotizacion_id;
    public $timbrado_id;
    public $punto_expedicion_id;
    public $sucursal_id;
    public $deposito_id;
    public $vendedor_id;
    public $condicion_pago = 'CONTADO';
    public $cantidad_cuotas = 1;
    public $cuotas = [];
    public $es_electronica = false;
    public $observaciones;

    // Tipo de facturación
    public $tipo_facturacion = 'PEDIDOS'; // PEDIDOS o SERVICIOS
    public $search_pedido = '';
    public $pedidos_encontrados = [];
    public $mostrar_busqueda_pedido = false;

    // Búsquedas
    public $search_cliente = '';
    public $clientes_encontrados = [];
    public $search_producto = '';
    public $productos_encontrados = [];
    public $search_orden_servicio = '';
    public $ordenes_encontradas = [];
    public $mostrar_busqueda_cliente = false;
    public $mostrar_busqueda_producto = false;
    public $mostrar_busqueda_orden = false;

    // Detalles
    public $detalles = [];
    public $detalles_servicios = [];
    public $detalles_repuestos = [];
    public $detalle_temp = [
        'producto_id' => null,
        'producto_nombre' => '',
        'cantidad' => 1,
        'precio_unitario' => 0,
        'descuento_porcentaje' => 0,
        'iva_porcentaje' => 10,
    ];

    // Totales por sección
    public $subtotal_productos = 0;
    public $iva_productos = 0;
    public $total_productos = 0;
    public $subtotal_servicios = 0;
    public $descuento_servicios = 0;
    public $total_servicios = 0;
    public $subtotal_repuestos = 0;
    public $descuento_repuestos = 0;
    public $total_repuestos = 0;

    // Formas de pago
    public $formas_pago = [];
    public $forma_pago_temp = [
        'forma_pago' => 'EFECTIVO',
        'monto' => 0,
        'referencia' => '',
    ];

    // Totales y cálculos
    public $subtotal = 0;
    public $iva_10 = 0;
    public $iva_5 = 0;
    public $exenta = 0;
    public $total_iva = 0;
    public $descuento_global = 0;
    public $flete = 0;
    public $total = 0;
    public $total_pagado = 0;
    public $saldo_pendiente = 0;

    // Listas
    public $timbrados = [];
    public $puntos_expedicion = [];
    public $sucursales = [];
    public $depositos = [];
    public $cliente_seleccionado;

    protected $rules = [
        'fecha_emision' => 'required|date',
        'cliente_id' => 'required',
        'timbrado_id' => 'required',
        'punto_expedicion_id' => 'required',
        'sucursal_id' => 'required',
        'deposito_id' => 'required',
        'condicion_pago' => 'required|in:CONTADO,CREDITO',
        'cantidad_cuotas' => 'required_if:condicion_pago,CREDITO|integer|min:1|max:6',
    ];

    public function mount($facturaId = null, $pedidoId = null, $cotizacionId = null)
    {
        $this->facturaId = $facturaId;
        $this->pedido_cliente_id = $pedidoId;
        $this->cotizacion_id = $cotizacionId;
        $this->fecha_emision = now()->format('Y-m-d');
        $this->vendedor_id = Auth::id();
        $this->condicion_pago = 'CONTADO'; // Siempre al contado

        // Cargar listas
        $this->cargarListas();

        // Si hay factura, cargar datos
        if ($this->facturaId) {
            $this->cargarFactura();
        }

        // Si hay pedido, cargar desde pedido
        if ($this->pedido_cliente_id) {
            $this->cargarDesdePedido();
        }

        // Si hay cotización, cargar desde cotización
        if ($this->cotizacion_id) {
            $this->cargarDesdeCotizacion();
        }
    }

    public function cargarListas()
    {
        // Buscar apertura de caja activa del usuario
        $aperturaActiva = AperturaCaja::where('usuario_id', Auth::id())
            ->where('estado', 'ABIERTA')
            ->latest('fecha_apertura')
            ->latest('hora_apertura')
            ->first();

        if ($aperturaActiva) {
            // Obtener datos de la apertura
            $this->punto_expedicion_id = $aperturaActiva->punto_expedicion_id;
            $puntoExpedicion = $aperturaActiva->puntoExpedicion;

            // Obtener sucursal y depósito del punto de expedición
            $this->sucursal_id = $puntoExpedicion->sucursal_id;

            // Obtener timbrado vigente para factura del punto de expedición
            $this->timbrado_id = Timbrado::where('tipo_documento', 'factura')
                ->where('activo', true)
                ->vigentes()
                ->first()?->id;
        }

        // Cargar listas completas para uso interno
        $this->timbrados = Timbrado::where('tipo_documento', 'factura')
            ->where('activo', true)
            ->vigentes()
            ->get();

        $this->puntos_expedicion = PuntoExpedicion::where('activo', true)->get();
        $this->sucursales = Sucursal::where('activo', true)->get();
        $this->depositos = Deposito::where('activo', true)->get();

        // Si no hay apertura activa, seleccionar primer valor por defecto
        if (!$aperturaActiva) {
            if ($this->timbrados->count() > 0 && !$this->timbrado_id) {
                $this->timbrado_id = $this->timbrados->first()->id;
            }
            if ($this->puntos_expedicion->count() > 0 && !$this->punto_expedicion_id) {
                $this->punto_expedicion_id = $this->puntos_expedicion->first()->id;
            }
            if ($this->sucursales->count() > 0 && !$this->sucursal_id) {
                $this->sucursal_id = $this->sucursales->first()->id;
            }
        }

        // Deposito siempre se asigna
        if ($this->depositos->count() > 0 && !$this->deposito_id) {
            $this->deposito_id = $this->depositos->first()->id;
        }
    }

    public function cargarFactura()
    {
        $this->factura = Factura::with(['detalles.producto', 'formasPago', 'cliente'])->findOrFail($this->facturaId);

        // Cargar cabecera
        $this->numero_factura = $this->factura->numero_factura;
        $this->fecha_emision = $this->factura->fecha_emision->format('Y-m-d');
        $this->fecha_vencimiento = $this->factura->fecha_vencimiento?->format('Y-m-d');
        $this->cliente_id = $this->factura->cliente_id;
        $this->timbrado_id = $this->factura->timbrado_id;
        $this->punto_expedicion_id = $this->factura->punto_expedicion_id;
        $this->sucursal_id = $this->factura->sucursal_id;
        $this->deposito_id = $this->factura->deposito_id;
        $this->vendedor_id = $this->factura->vendedor_id;
        $this->condicion_pago = $this->factura->condicion_pago;
        $this->cantidad_cuotas = $this->factura->cantidad_cuotas;
        $this->es_electronica = $this->factura->es_electronica;
        $this->descuento_global = $this->factura->descuento_global;
        $this->flete = $this->factura->flete;
        $this->observaciones = $this->factura->observaciones;

        // Cargar cliente
        $this->cliente_seleccionado = $this->factura->cliente;
        $this->search_cliente = $this->factura->cliente->nombre;

        // Cargar detalles
        foreach ($this->factura->detalles as $detalle) {
            $this->detalles[] = [
                'id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $detalle->producto->descripcion,
                'producto_codigo' => $detalle->producto->codigo,
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'descuento_porcentaje' => $detalle->descuento_porcentaje,
                'descuento_monto' => $detalle->descuento_monto,
                'iva_porcentaje' => $detalle->iva_porcentaje,
                'subtotal' => $detalle->subtotal,
                'iva_monto' => $detalle->iva_monto,
                'total' => $detalle->total,
            ];
        }

        // Cargar formas de pago
        foreach ($this->factura->formasPago as $fp) {
            $this->formas_pago[] = [
                'id' => $fp->id,
                'forma_pago' => $fp->forma_pago,
                'monto' => $fp->monto,
                'referencia' => $fp->referencia,
            ];
        }

        $this->calcularTotales();
    }

    public function cargarDesdePedido()
    {
        $pedido = PedidoCliente::with(['cliente', 'detalles.producto'])->findOrFail($this->pedido_cliente_id);

        // Validar estado del pedido
        if (!in_array($pedido->estado, ['CONFIRMADO', 'LISTO_ENTREGAR', 'COMPLETAMENTE_ENTREGADO'])) {
            session()->flash('error', 'El pedido debe estar confirmado para generar factura.');
            return redirect()->route('ventas.pedidos.show', $pedido);
        }

        // Cargar cliente
        $this->cliente_id = $pedido->cliente_id;
        $this->cliente_seleccionado = $pedido->cliente;
        $this->search_cliente = $pedido->cliente->nombre;

        // Cargar detalles
        foreach ($pedido->detalles as $detalle) {
            $this->detalles[] = [
                'pedido_detalle_id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $detalle->producto->descripcion,
                'producto_codigo' => $detalle->producto->codigo,
                'cantidad' => $detalle->cantidad_solicitada,
                'precio_unitario' => $detalle->precio_unitario,
                'descuento_porcentaje' => $detalle->descuento_porcentaje,
                'descuento_monto' => $detalle->descuento_monto,
                'iva_porcentaje' => $detalle->iva_porcentaje,
                'subtotal' => $detalle->subtotal,
                'iva_monto' => $detalle->iva_monto,
                'total' => $detalle->total,
            ];
        }

        $this->calcularTotales();
    }

    public function cargarDesdeCotizacion()
    {
        // TODO: Implementar cuando exista el modelo Cotizacion
        session()->flash('info', 'Carga desde cotización en desarrollo.');
    }

    public function updatedSearchCliente()
    {
        if (strlen($this->search_cliente) >= 2) {
            $this->clientes_encontrados = Cliente::where('activo', true)
                ->where(function ($query) {
                    $query->where('nombre', 'ilike', '%' . $this->search_cliente . '%')
                        ->orWhere('documento', 'ilike', '%' . $this->search_cliente . '%');
                })
                ->limit(10)
                ->get();
            $this->mostrar_busqueda_cliente = true;
        } else {
            $this->clientes_encontrados = [];
            $this->mostrar_busqueda_cliente = false;
        }
    }

    public function seleccionarCliente($clienteId)
    {
        $this->cliente_id = $clienteId;
        $this->cliente_seleccionado = Cliente::find($clienteId);
        $this->search_cliente = $this->cliente_seleccionado->nombre;
        $this->clientes_encontrados = [];
        $this->mostrar_busqueda_cliente = false;

        // Si está en modo PEDIDOS, cargar pedidos del cliente automáticamente
        if ($this->tipo_facturacion === 'PEDIDOS') {
            $this->cargarPedidosCliente();
        }

        // Si está en modo SERVICIOS, cargar órdenes del cliente automáticamente
        if ($this->tipo_facturacion === 'SERVICIOS') {
            $this->cargarOrdenesCliente();
        }
    }

    public function cargarPedidosCliente()
    {
        if ($this->cliente_id) {
            $this->pedidos_encontrados = PedidoCliente::with(['detalles.producto', 'cliente'])
                ->where('cliente_id', $this->cliente_id)
                ->whereIn('estado', ['CONFIRMADO', 'LISTO_ENTREGAR', 'COMPLETAMENTE_ENTREGADO'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($pedido) {
                    $pedido->codigo_pedido = $pedido->numero_pedido;
                    $pedido->monto_total = $pedido->total;
                    $pedido->cliente_nombre = $pedido->cliente->nombre ?? 'N/A';
                    return $pedido;
                });
            $this->mostrar_busqueda_pedido = true;
        }
    }

    public function cargarOrdenesCliente()
    {
        if ($this->cliente_id) {
            $this->ordenes_encontradas = OrdenServicio::with([
                'presupuesto.diagnostico.tiposServicio.tipoServicio',
                'presupuesto.diagnostico.repuestos.producto.precioActual',
                'presupuesto.diagnostico.cliente'
            ])
                ->whereHas('presupuesto.diagnostico', function ($query) {
                    $query->where('cliente_id', $this->cliente_id);
                })
                ->whereIn('estado', ['en_proceso', 'finalizada', 'entregada'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($orden) {
                    $orden->codigo_orden = $orden->codigo;
                    $orden->monto_total = $orden->presupuesto->monto_total ?? 0;
                    $orden->cliente_nombre = $orden->presupuesto->diagnostico->cliente->nombre ?? 'N/A';
                    return $orden;
                });
            $this->mostrar_busqueda_orden = true;
        }
    }

    public function updatedTipoFacturacion()
    {
        // Limpiar búsquedas y detalles al cambiar tipo de facturación
        $this->detalles = [];
        $this->detalles_servicios = [];
        $this->detalles_repuestos = [];
        $this->search_pedido = '';
        $this->pedidos_encontrados = [];
        $this->mostrar_busqueda_pedido = false;
        $this->search_orden_servicio = '';
        $this->ordenes_encontradas = [];
        $this->mostrar_busqueda_orden = false;
        $this->search_producto = '';
        $this->productos_encontrados = [];
        $this->mostrar_busqueda_producto = false;
        $this->calcularTotales();

        // Si ya hay un cliente seleccionado, cargar pedidos u órdenes según el tipo
        if ($this->cliente_id) {
            if ($this->tipo_facturacion === 'PEDIDOS') {
                $this->cargarPedidosCliente();
            } elseif ($this->tipo_facturacion === 'SERVICIOS') {
                $this->cargarOrdenesCliente();
            }
        }
    }

    public function updatedSearchPedido()
    {
        if (strlen($this->search_pedido) >= 2) {
            $this->pedidos_encontrados = PedidoCliente::with(['detalles.producto', 'cliente'])
                ->whereIn('estado', ['CONFIRMADO', 'LISTO_ENTREGAR', 'COMPLETAMENTE_ENTREGADO'])
                ->where(function ($query) {
                    $query->where('numero_pedido', 'ilike', '%' . $this->search_pedido . '%')
                        ->orWhereHas('cliente', function($q) {
                            $q->where('nombre', 'ilike', '%' . $this->search_pedido . '%')
                              ->orWhere('documento', 'ilike', '%' . $this->search_pedido . '%');
                        });
                })
                ->limit(10)
                ->get()
                ->map(function($pedido) {
                    $pedido->codigo_pedido = $pedido->numero_pedido;
                    $pedido->monto_total = $pedido->total;
                    $pedido->cliente_nombre = $pedido->cliente->nombre ?? 'N/A';
                    return $pedido;
                });
            $this->mostrar_busqueda_pedido = true;
        } else {
            $this->pedidos_encontrados = [];
            $this->mostrar_busqueda_pedido = false;
        }
    }

    public function updatedDetalles($value, $key)
    {
        // Cuando cambia cualquier valor en detalles (ej: detalles.0.cantidad)
        // Extraer el índice del detalle que cambió
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            $index = $parts[0];
            $this->actualizarDetalle($index);
        }
    }

    public function updatedSearchProducto()
    {
        if (strlen($this->search_producto) >= 2) {
            $this->productos_encontrados = Producto::with(['precioActual', 'stock'])
                ->where('activo', true)
                ->where(function ($query) {
                    $query->where('descripcion', 'ilike', '%' . $this->search_producto . '%')
                        ->orWhere('codigo', 'ilike', '%' . $this->search_producto . '%')
                        ->orWhere('nombre', 'ilike', '%' . $this->search_producto . '%');
                })
                ->limit(10)
                ->get()
                ->map(function($producto) {
                    // Agregar precio de venta
                    $producto->precio_venta = $producto->precioActual?->precio_venta ?? 0;

                    // Calcular stock total (suma de stock_actual en todos los depósitos)
                    $producto->stock_total = $producto->stock->sum('stock_actual') ?? 0;

                    return $producto;
                });
            $this->mostrar_busqueda_producto = true;
        } else {
            $this->productos_encontrados = [];
            $this->mostrar_busqueda_producto = false;
        }
    }

    public function updatedSearchOrdenServicio()
    {
        if (strlen($this->search_orden_servicio) >= 2) {
            $this->ordenes_encontradas = OrdenServicio::with([
                'presupuesto.diagnostico.tiposServicio.tipoServicio',
                'presupuesto.diagnostico.repuestos.producto.precioActual',
                'presupuesto.diagnostico.cliente'
            ])
                ->whereIn('estado', ['en_proceso', 'finalizada', 'entregada'])
                ->where(function ($query) {
                    $query->where('codigo', 'ilike', '%' . $this->search_orden_servicio . '%')
                        ->orWhereHas('presupuesto.diagnostico.cliente', function($q) {
                            $q->where('nombre', 'ilike', '%' . $this->search_orden_servicio . '%')
                              ->orWhere('documento', 'ilike', '%' . $this->search_orden_servicio . '%');
                        });
                })
                ->limit(10)
                ->get()
                ->map(function($orden) {
                    $orden->codigo_orden = $orden->codigo;
                    $orden->monto_total = $orden->presupuesto->monto_total ?? 0;
                    $orden->cliente_nombre = $orden->presupuesto->diagnostico->cliente->nombre ?? 'N/A';
                    return $orden;
                });
            $this->mostrar_busqueda_orden = true;
        } else {
            $this->ordenes_encontradas = [];
            $this->mostrar_busqueda_orden = false;
        }
    }

    public function agregarProducto($productoId)
    {
        $producto = Producto::with('precioActual')->find($productoId);

        if (!$producto) {
            session()->flash('error', 'Producto no encontrado.');
            return;
        }

        // Verificar si ya está en la lista
        foreach ($this->detalles as $detalle) {
            if ($detalle['producto_id'] == $productoId) {
                session()->flash('error', 'El producto ya está en la lista.');
                return;
            }
        }

        // Obtener precio de venta del precio actual
        $precioVenta = $producto->precioActual?->precio_venta ?? 0;

        $this->detalles[] = [
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre ?? $producto->descripcion,
            'producto_codigo' => $producto->codigo,
            'cantidad' => 1,
            'precio_unitario' => $precioVenta,
            'descuento_porcentaje' => 0,
            'descuento_monto' => 0,
            'iva_porcentaje' => 10, // Siempre IVA 10%
            'subtotal' => 0,
            'iva_monto' => 0,
            'total' => 0,
        ];

        $this->search_producto = '';
        $this->productos_encontrados = [];
        $this->mostrar_busqueda_producto = false;

        $this->actualizarDetalle(count($this->detalles) - 1);
    }

    public function eliminarDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
        $this->calcularTotales();
    }

    public function seleccionarPedido($pedidoId)
    {
        $pedido = PedidoCliente::with(['detalles.producto', 'cliente'])->find($pedidoId);

        if (!$pedido) {
            session()->flash('error', 'Pedido no encontrado.');
            return;
        }

        // Actualizar cliente seleccionado
        $this->cliente_id = $pedido->cliente_id;
        $this->cliente_seleccionado = $pedido->cliente;
        $this->search_cliente = $pedido->cliente->nombre;

        // Limpiar detalles anteriores
        $this->detalles = [];

        // Cargar detalles del pedido
        foreach ($pedido->detalles as $detalle) {
            $this->detalles[] = [
                'pedido_detalle_id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $detalle->producto->descripcion ?? $detalle->producto->nombre,
                'producto_codigo' => $detalle->producto->codigo,
                'cantidad' => $detalle->cantidad_solicitada,
                'precio_unitario' => $detalle->precio_unitario,
                'descuento_porcentaje' => $detalle->descuento_porcentaje,
                'descuento_monto' => $detalle->descuento_monto,
                'iva_porcentaje' => $detalle->iva_porcentaje,
                'subtotal' => $detalle->subtotal,
                'iva_monto' => $detalle->iva_monto,
                'total' => $detalle->total,
            ];
        }

        $this->pedido_cliente_id = $pedido->id;
        $this->search_pedido = '';
        $this->pedidos_encontrados = [];
        $this->mostrar_busqueda_pedido = false;

        $this->calcularTotales();

        session()->flash('success', 'Pedido cargado correctamente.');
    }

    public function agregarOrdenServicio($ordenId)
    {
        $orden = OrdenServicio::with([
            'presupuesto.diagnostico.tiposServicio.tipoServicio',
            'presupuesto.diagnostico.repuestos.producto.precioActual',
            'presupuesto.diagnostico.cliente'
        ])->find($ordenId);

        if (!$orden || !$orden->presupuesto) {
            session()->flash('error', 'Orden de servicio no encontrada.');
            return;
        }

        // Actualizar cliente seleccionado
        $diagnostico = $orden->presupuesto->diagnostico;
        if ($diagnostico && $diagnostico->cliente) {
            $this->cliente_id = $diagnostico->cliente_id;
            $this->cliente_seleccionado = $diagnostico->cliente;
            $this->search_cliente = $diagnostico->cliente->nombre;
        }

        // Limpiar detalles anteriores
        $this->detalles_servicios = [];
        $this->detalles_repuestos = [];

        // Cargar tipos de servicio
        if ($diagnostico && $diagnostico->tiposServicio) {
            foreach ($diagnostico->tiposServicio as $ts) {
                // El subtotal NO incluye IVA, calculamos IVA como 10% del subtotal
                $subtotalSinIva = $ts->subtotal;
                $ivaMonto = $subtotalSinIva * 0.10;

                // Cargar tipoServicio directamente si no está cargado
                if (!$ts->relationLoaded('tipoServicio')) {
                    $ts->load('tipoServicio');
                }

                $codigo = $ts->tipoServicio->codigo ?? 'N/A';
                $descripcion = $ts->tipoServicio->descripcion ?? 'N/A';

                $this->detalles_servicios[] = [
                    'codigo' => $codigo,
                    'tipo_servicio' => $descripcion,
                    'cantidad' => $ts->cantidad,
                    'precio_unitario' => $ts->costo_unitario,
                    'subtotal' => $subtotalSinIva,
                    'iva_monto' => $ivaMonto,
                ];
            }
        }

        // Cargar repuestos
        if ($diagnostico && $diagnostico->repuestos) {
            foreach ($diagnostico->repuestos as $rep) {
                // Usar el costo del repuesto desde el diagnóstico
                $subtotalSinIva = $rep->cantidad * $rep->costo;
                $ivaMonto = $subtotalSinIva * 0.10;

                $this->detalles_repuestos[] = [
                    'producto_id' => $rep->producto_id,
                    'codigo' => $rep->producto->codigo ?? 'N/A',
                    'repuesto' => $rep->producto->descripcion ?? $rep->producto->nombre ?? 'N/A',
                    'cantidad' => $rep->cantidad,
                    'precio_unitario' => $rep->costo,
                    'subtotal' => $subtotalSinIva,
                    'iva_monto' => $ivaMonto,
                ];
            }
        }

        // Aplicar descuentos si existen
        $this->descuento_servicios = $orden->presupuesto->descuento_promocion ?? 0;
        $this->descuento_repuestos = $orden->presupuesto->descuento_descuento ?? 0;

        $this->search_orden_servicio = '';
        $this->ordenes_encontradas = [];
        $this->mostrar_busqueda_orden = false;

        $this->calcularTotalesServicios();
        $this->calcularTotalesRepuestos();
        $this->calcularTotales();

        session()->flash('success', 'Orden de servicio agregada correctamente.');
    }

    public function eliminarServicio($index)
    {
        unset($this->detalles_servicios[$index]);
        $this->detalles_servicios = array_values($this->detalles_servicios);
        $this->calcularTotalesServicios();
        $this->calcularTotales();
    }

    public function eliminarRepuesto($index)
    {
        unset($this->detalles_repuestos[$index]);
        $this->detalles_repuestos = array_values($this->detalles_repuestos);
        $this->calcularTotalesRepuestos();
        $this->calcularTotales();
    }

    public function actualizarDetalle($index)
    {
        $detalle = &$this->detalles[$index];

        // El precio unitario YA incluye IVA (precio con IVA)
        $precioConIva = $detalle['precio_unitario'];

        // Calcular subtotal (precio * cantidad)
        $subtotalConIva = $detalle['cantidad'] * $precioConIva;

        // Aplicar descuento si existe
        if ($detalle['descuento_porcentaje'] > 0) {
            $detalle['descuento_monto'] = ($subtotalConIva * $detalle['descuento_porcentaje']) / 100;
            $subtotalConIva -= $detalle['descuento_monto'];
        } else {
            $detalle['descuento_monto'] = 0;
        }

        // Extraer el IVA que ya está incluido en el precio
        // Fórmula: IVA = Total con IVA * (IVA% / (100 + IVA%))
        $ivaDecimal = $detalle['iva_porcentaje'] / 100;
        $detalle['iva_monto'] = $subtotalConIva * ($ivaDecimal / (1 + $ivaDecimal));

        // El total es el subtotal con IVA (ya incluido)
        $detalle['total'] = $subtotalConIva;

        // El subtotal sin IVA
        $detalle['subtotal'] = $subtotalConIva - $detalle['iva_monto'];

        $this->calcularTotales();
    }

    public function calcularTotalesServicios()
    {
        $this->subtotal_servicios = 0;

        foreach ($this->detalles_servicios as $servicio) {
            $this->subtotal_servicios += $servicio['subtotal'];
        }

        // Total de servicios = subtotal - descuento (sin agregar IVA aquí)
        $this->total_servicios = $this->subtotal_servicios - $this->descuento_servicios;
    }

    public function calcularTotalesRepuestos()
    {
        $this->subtotal_repuestos = 0;

        foreach ($this->detalles_repuestos as $repuesto) {
            $this->subtotal_repuestos += $repuesto['subtotal'];
        }

        // Total de repuestos = subtotal - descuento (sin agregar IVA aquí)
        $this->total_repuestos = $this->subtotal_repuestos - $this->descuento_repuestos;
    }

    public function calcularTotales()
    {
        // Calcular totales de productos (desde pedidos)
        // Los precios YA INCLUYEN IVA, por lo tanto subtotal = total
        $subtotal_productos = 0;
        $iva_productos = 0;
        foreach ($this->detalles as $detalle) {
            $subtotal_productos += $detalle['subtotal']; // El subtotal ya incluye IVA
            $iva_productos += $detalle['iva_monto']; // IVA es solo informativo
        }

        // TOTAL GENERAL = suma de subtotales (los precios ya incluyen IVA)
        $this->total = $subtotal_productos + $this->total_servicios + $this->total_repuestos;

        // Subtotal General = Total (porque el IVA ya está incluido)
        $this->subtotal = $this->total;

        // IVA 10% = suma de todos los IVA informativos
        $iva_servicios_repuestos = ($this->total_servicios + $this->total_repuestos) * 0.10;
        $this->iva_10 = $iva_productos + $iva_servicios_repuestos;

        // IVA 5% y Exenta = 0
        $this->iva_5 = 0;
        $this->exenta = 0;

        // Calcular total pagado
        $this->total_pagado = array_sum(array_column($this->formas_pago, 'monto'));
        $this->saldo_pendiente = $this->total - $this->total_pagado;

        // Recalcular cuotas si es crédito
        if ($this->condicion_pago === 'CREDITO' && $this->cantidad_cuotas > 0) {
            $this->calcularCuotas();
        }
    }

    public function updatedCondicionPago()
    {
        if ($this->condicion_pago === 'CREDITO') {
            $this->cantidad_cuotas = 1;
            $this->formas_pago = []; // Limpiar formas de pago si cambia a crédito
            $this->calcularCuotas();
        } else {
            $this->cuotas = [];
            $this->fecha_vencimiento = null;
        }
        $this->calcularTotales();
    }

    public function updatedCantidadCuotas()
    {
        if ($this->condicion_pago === 'CREDITO' && $this->cantidad_cuotas > 0 && $this->cantidad_cuotas <= 6) {
            $this->calcularCuotas();
        }
    }

    public function calcularCuotas()
    {
        if ($this->total <= 0 || $this->cantidad_cuotas <= 0) {
            $this->cuotas = [];
            return;
        }

        $this->cuotas = [];
        $montoPorCuota = round($this->total / $this->cantidad_cuotas, 2);
        $fechaBase = $this->fecha_emision ? \Carbon\Carbon::parse($this->fecha_emision) : now();

        for ($i = 1; $i <= $this->cantidad_cuotas; $i++) {
            // Primera cuota vence el mismo día de facturación, las siguientes cada 30 días
            if ($i == 1) {
                $fechaVencimiento = $fechaBase->copy();
            } else {
                $fechaVencimiento = $fechaBase->copy()->addDays(($i - 1) * 30);
            }

            // Ajustar la última cuota para que cuadre exactamente
            $monto = $i == $this->cantidad_cuotas
                ? $this->total - ($montoPorCuota * ($this->cantidad_cuotas - 1))
                : $montoPorCuota;

            $this->cuotas[] = [
                'numero_cuota' => $i,
                'monto' => $monto,
                'fecha_vencimiento' => $fechaVencimiento->format('Y-m-d'),
            ];
        }

        // Actualizar la fecha de vencimiento de la factura (última cuota)
        if (count($this->cuotas) > 0) {
            $this->fecha_vencimiento = $this->cuotas[count($this->cuotas) - 1]['fecha_vencimiento'];
        }
    }

    public function agregarFormaPago()
    {
        if ($this->forma_pago_temp['monto'] <= 0) {
            session()->flash('error', 'El monto debe ser mayor a 0.');
            return;
        }

        $this->formas_pago[] = [
            'forma_pago' => $this->forma_pago_temp['forma_pago'],
            'monto' => $this->forma_pago_temp['monto'],
            'referencia' => $this->forma_pago_temp['referencia'],
        ];

        // Resetear
        $this->forma_pago_temp = [
            'forma_pago' => 'EFECTIVO',
            'monto' => 0,
            'referencia' => '',
        ];

        $this->calcularTotales();
    }

    public function eliminarFormaPago($index)
    {
        unset($this->formas_pago[$index]);
        $this->formas_pago = array_values($this->formas_pago);
        $this->calcularTotales();
    }

    public function guardar()
    {
        $this->validate();

        // Validaciones adicionales
        $tieneDetalles = count($this->detalles) > 0 || count($this->detalles_servicios) > 0 || count($this->detalles_repuestos) > 0;

        if (!$tieneDetalles) {
            session()->flash('error', 'Debe agregar al menos un producto o servicio.');
            return;
        }

        // Validar formas de pago solo si es CONTADO
        if ($this->condicion_pago === 'CONTADO') {
            if ($this->total_pagado < $this->total) {
                session()->flash('error', 'El total pagado debe ser igual al total de la factura para facturas al contado.');
                return;
            }
        }

        // Validar cuotas si es CREDITO
        if ($this->condicion_pago === 'CREDITO') {
            if ($this->cantidad_cuotas < 1 || $this->cantidad_cuotas > 6) {
                session()->flash('error', 'La cantidad de cuotas debe estar entre 1 y 6.');
                return;
            }
        }

        DB::beginTransaction();
        try {
            // Crear o actualizar factura
            if ($this->facturaId) {
                $factura = Factura::findOrFail($this->facturaId);

                // Solo se pueden editar facturas en BORRADOR
                if ($factura->estado !== 'BORRADOR') {
                    throw new \Exception('Solo se pueden editar facturas en estado BORRADOR.');
                }
            } else {
                $factura = new Factura();

                // Generar número de factura
                $timbrado = Timbrado::findOrFail($this->timbrado_id);
                $factura->numero_factura = $timbrado->obtenerSiguienteNumero();
                $factura->numero_timbrado = $timbrado->numero_timbrado;
            }

            // Datos de cabecera
            $factura->fecha_emision = $this->fecha_emision;
            $factura->fecha_vencimiento = $this->fecha_vencimiento;
            $factura->cliente_id = $this->cliente_id;
            $factura->pedido_cliente_id = $this->pedido_cliente_id;
            $factura->cotizacion_id = $this->cotizacion_id;
            $factura->timbrado_id = $this->timbrado_id;
            $factura->punto_expedicion_id = $this->punto_expedicion_id;
            $factura->sucursal_id = $this->sucursal_id;
            $factura->deposito_id = $this->deposito_id;
            $factura->vendedor_id = $this->vendedor_id;
            $factura->condicion_pago = $this->condicion_pago;
            $factura->cantidad_cuotas = $this->cantidad_cuotas;
            $factura->es_electronica = $this->es_electronica;
            $factura->descuento_global = $this->descuento_global;
            $factura->flete = $this->flete;
            $factura->observaciones = $this->observaciones;
            $factura->estado = 'BORRADOR';

            if (!$this->facturaId) {
                $factura->creado_por = Auth::id();
            }
            $factura->actualizado_por = Auth::id();

            $factura->save();

            // Eliminar detalles anteriores si está editando
            if ($this->facturaId) {
                $factura->detalles()->delete();
                $factura->servicios()->delete();
                $factura->formasPago()->delete();
            }

            // Guardar detalles de productos
            foreach ($this->detalles as $detalle) {
                $facturaDetalle = new FacturaDetalle();
                $facturaDetalle->factura_id = $factura->id;
                $facturaDetalle->pedido_detalle_id = $detalle['pedido_detalle_id'] ?? null;
                $facturaDetalle->producto_id = $detalle['producto_id'];
                $facturaDetalle->cantidad = $detalle['cantidad'];
                $facturaDetalle->precio_unitario = $detalle['precio_unitario'];
                $facturaDetalle->descuento_porcentaje = $detalle['descuento_porcentaje'];
                $facturaDetalle->iva_porcentaje = $detalle['iva_porcentaje'];
                $facturaDetalle->save();
            }

            // Guardar detalles de servicios (tipos de servicio)
            foreach ($this->detalles_servicios as $servicio) {
                $facturaServicio = new \App\Models\Ventas\FacturaServicio();
                $facturaServicio->factura_id = $factura->id;
                $facturaServicio->codigo = $servicio['codigo'];
                $facturaServicio->descripcion = $servicio['tipo_servicio'];
                $facturaServicio->cantidad = $servicio['cantidad'];
                $facturaServicio->precio_unitario = $servicio['precio_unitario'];
                $facturaServicio->subtotal = $servicio['subtotal'];
                $facturaServicio->iva_porcentaje = 10;
                $facturaServicio->iva_monto = $servicio['iva_monto'];
                $facturaServicio->total = $servicio['subtotal'] + $servicio['iva_monto'];
                $facturaServicio->save();
            }

            // Guardar detalles de repuestos (como productos)
            foreach ($this->detalles_repuestos as $repuesto) {
                $facturaDetalle = new FacturaDetalle();
                $facturaDetalle->factura_id = $factura->id;
                $facturaDetalle->producto_id = $repuesto['producto_id'] ?? null;
                $facturaDetalle->cantidad = $repuesto['cantidad'];
                $facturaDetalle->precio_unitario = $repuesto['precio_unitario'];
                $facturaDetalle->descuento_porcentaje = 0;
                $facturaDetalle->iva_porcentaje = 10;
                $facturaDetalle->save();
            }

            // Guardar formas de pago
            foreach ($this->formas_pago as $fp) {
                $formaPago = new FacturaFormaPago();
                $formaPago->factura_id = $factura->id;
                $formaPago->forma_pago = $fp['forma_pago'];
                $formaPago->monto = $fp['monto'];
                $formaPago->referencia = $fp['referencia'];
                $formaPago->save();
            }

            // Guardar cuotas si es crédito
            if ($this->condicion_pago === 'CREDITO' && count($this->cuotas) > 0) {
                foreach ($this->cuotas as $cuota) {
                    \App\Models\Ventas\FacturaCuota::create([
                        'factura_id' => $factura->id,
                        'numero_cuota' => $cuota['numero_cuota'],
                        'monto' => $cuota['monto'],
                        'fecha_vencimiento' => $cuota['fecha_vencimiento'],
                        'monto_pagado' => 0,
                        'saldo_pendiente' => $cuota['monto'],
                        'estado' => 'PENDIENTE',
                        'activo' => true,
                    ]);
                }
            }

            // Recalcular totales
            $factura->calcularTotales();

            // Nota: Las cuentas por cobrar y movimientos de caja se crean al emitir la factura, no al guardarla en borrador

            DB::commit();

            session()->flash('success', $this->facturaId ? 'Factura actualizada correctamente.' : 'Factura creada correctamente.');
            return redirect()->route('ventas.facturas.show', $factura);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar la factura: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ventas.factura-form');
    }
}
