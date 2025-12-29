<?php

namespace App\Livewire\Ventas;

use App\Models\Ventas\PedidoCliente;
use App\Models\Ventas\PedidoClienteDetalle;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PedidoClienteForm extends Component
{
    // Cabecera
    public $pedidoId;
    public $numero_pedido;
    public $fecha_pedido;
    public $cliente_id;
    public $sucursal_id = 1;
    public $deposito_id = 1;
    public $vendedor_id;
    public $tipo_entrega = 'RETIRO_LOCAL';
    public $observaciones;
    public $condiciones_especiales;
    public $direccion_entrega;
    public $descuento_global = 0;
    public $flete = 0;

    // Datos para visualización (no modificables)
    public $empresa_nombre;
    public $sucursal_nombre;
    public $funcionario_nombre;

    // Búsqueda de productos
    public $search_producto = '';
    public $productos_encontrados = [];
    public $mostrar_productos = false;

    // Búsqueda de clientes
    public $search_cliente = '';
    public $clientes_encontrados = [];
    public $mostrar_clientes = false;
    public $cliente_seleccionado = null;

    // Detalles del pedido
    public $detalles = [];

    // Edición de detalle
    public $editando_index = null;
    public $cantidad_solicitada_edit = 0;
    public $precio_unitario_edit = 0;

    protected function rules()
    {
        return [
            'fecha_pedido' => 'required|date',
            'cliente_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('servicios.CLIENTES')
                        ->where('id', $value)
                        ->exists();

                    if (!$exists) {
                        $fail('El cliente seleccionado no es válido.');
                    }
                },
            ],
            'tipo_entrega' => 'required|in:RETIRO_LOCAL,DELIVERY,ENVIO_TRANSPORTE',
            'observaciones' => 'nullable|string',
        ];
    }

    public function mount($pedidoId = null)
    {
        $this->fecha_pedido = now()->format('Y-m-d');
        $this->vendedor_id = auth()->id();

        // Cargar datos de visualización
        $this->cargarDatosEmpresaSucursalFuncionario();

        if ($pedidoId) {
            $this->pedidoId = $pedidoId;
            $this->cargarPedido();
        }
    }

    public function cargarDatosEmpresaSucursalFuncionario()
    {
        // Cargar empresa (asumiendo que hay una tabla de configuración o empresa)
        // Por ahora lo dejamos hardcoded, puedes ajustarlo según tu estructura
        $this->empresa_nombre = config('app.name', 'Mi Empresa');

        // Cargar sucursal
        $sucursal = DB::table('SUCURSALES')->where('id', $this->sucursal_id)->first();
        $this->sucursal_nombre = $sucursal->nombre ?? 'Sucursal Principal';

        // Cargar funcionario (usuario actual)
        $usuario = auth()->user();
        $this->funcionario_nombre = $usuario->name ?? 'Usuario';
    }

    public function cargarPedido()
    {
        $pedido = PedidoCliente::with(['detalles', 'cliente'])->findOrFail($this->pedidoId);

        $this->numero_pedido = $pedido->numero_pedido;
        $this->fecha_pedido = $pedido->fecha_pedido->format('Y-m-d');
        $this->cliente_id = $pedido->cliente_id;
        $this->sucursal_id = $pedido->sucursal_id;
        $this->deposito_id = $pedido->deposito_id;
        $this->vendedor_id = $pedido->vendedor_id;
        $this->tipo_entrega = $pedido->tipo_entrega;
        $this->descuento_global = $pedido->descuento_global;
        $this->flete = $pedido->flete;
        $this->direccion_entrega = $pedido->direccion_entrega;
        $this->observaciones = $pedido->observaciones;
        $this->condiciones_especiales = $pedido->condiciones_especiales;

        // Cargar cliente
        $this->cliente_seleccionado = $pedido->cliente;
        $this->search_cliente = $pedido->cliente->nombre;

        // Cargar detalles
        $this->detalles = $pedido->detalles->map(function ($detalle) {
            $producto = DB::table('stock.PRODUCTOS')->where('id', $detalle->producto_id)->first();

            return [
                'id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_codigo' => $producto->codigo ?? '',
                'producto_nombre' => $producto->nombre ?? 'Producto #' . $detalle->producto_id,
                'cantidad_solicitada' => $detalle->cantidad_solicitada,
                'precio_unitario' => $detalle->precio_unitario,
                'iva_porcentaje' => $detalle->iva_porcentaje,
            ];
        })->toArray();
    }

    public function updatedSearchCliente()
    {
        if (strlen($this->search_cliente) < 2) {
            $this->clientes_encontrados = [];
            $this->mostrar_clientes = false;
            return;
        }

        $this->clientes_encontrados = DB::table('servicios.CLIENTES')
            ->where('activo', true)
            ->where(function($query) {
                $query->where('nombre', 'ILIKE', '%' . $this->search_cliente . '%')
                      ->orWhere('documento', 'ILIKE', '%' . $this->search_cliente . '%')
                      ->orWhere('email', 'ILIKE', '%' . $this->search_cliente . '%');
            })
            ->limit(10)
            ->get()
            ->map(function($cliente) {
                return [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'documento' => $cliente->documento,
                    'tipo_cliente' => $cliente->tipo_cliente,
                    'telefono' => $cliente->telefono,
                    'celular' => $cliente->celular,
                    'direccion' => $cliente->direccion,
                ];
            })
            ->toArray();

        $this->mostrar_clientes = count($this->clientes_encontrados) > 0;
    }

    public function seleccionarCliente($clienteId)
    {
        $cliente = DB::table('servicios.CLIENTES')->where('id', $clienteId)->first();

        if ($cliente) {
            $this->cliente_id = $cliente->id;
            $this->cliente_seleccionado = (object) [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'documento' => $cliente->documento,
                'tipo_cliente' => $cliente->tipo_cliente,
                'telefono' => $cliente->telefono,
                'celular' => $cliente->celular,
                'direccion' => $cliente->direccion,
            ];
            $this->search_cliente = $this->cliente_seleccionado->nombre;
            $this->mostrar_clientes = false;
        }
    }

    public function updatedSearchProducto()
    {
        if (strlen($this->search_producto) < 2) {
            $this->productos_encontrados = [];
            $this->mostrar_productos = false;
            return;
        }

        $this->productos_encontrados = DB::table('stock.PRODUCTOS')
            ->where('activo', true)
            ->where(function($query) {
                $query->where('nombre', 'ILIKE', '%' . $this->search_producto . '%')
                      ->orWhere('codigo', 'ILIKE', '%' . $this->search_producto . '%');
            })
            ->limit(10)
            ->get()
            ->map(function($producto) {
                // Obtener stock actual - sumando todas las cantidades del producto
                $stock_total = DB::table('stock.STOCK')
                    ->where('producto_id', $producto->id)
                    ->sum('stock_actual');

                // Obtener precio de venta
                $precio = DB::table('stock.PRECIOS')
                    ->where('producto_id', $producto->id)
                    ->where('es_actual', true)
                    ->first();

                return [
                    'id' => $producto->id,
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'stock_actual' => $stock_total ?? 0,
                    'precio_venta' => $precio->precio_venta ?? 0,
                    'iva_porcentaje' => $producto->iva ?? 10,
                ];
            })
            ->toArray();

        $this->mostrar_productos = count($this->productos_encontrados) > 0;
    }

    public function agregarProducto($productoId)
    {
        $producto = collect($this->productos_encontrados)->firstWhere('id', $productoId);

        if (!$producto) {
            return;
        }

        // Verificar si ya existe en los detalles
        $existe = collect($this->detalles)->firstWhere('producto_id', $productoId);

        if ($existe) {
            session()->flash('warning', 'El producto ya está agregado al pedido');
            return;
        }

        // Agregar al array de detalles
        $this->detalles[] = [
            'producto_id' => $producto['id'],
            'producto_codigo' => $producto['codigo'],
            'producto_nombre' => $producto['nombre'],
            'cantidad_solicitada' => 1,
            'precio_unitario' => $producto['precio_venta'],
            'iva_porcentaje' => $producto['iva_porcentaje'],
        ];

        // Limpiar búsqueda
        $this->search_producto = '';
        $this->productos_encontrados = [];
        $this->mostrar_productos = false;

        session()->flash('success', 'Producto agregado correctamente');
    }

    public function editarDetalle($index)
    {
        if (!isset($this->detalles[$index])) {
            return;
        }

        $detalle = $this->detalles[$index];

        $this->editando_index = $index;
        $this->cantidad_solicitada_edit = $detalle['cantidad_solicitada'] ?? 0;
        $this->precio_unitario_edit = $detalle['precio_unitario'] ?? 0;
    }

    public function guardarEdicionDetalle()
    {
        if ($this->editando_index === null) {
            return;
        }

        $this->detalles[$this->editando_index]['cantidad_solicitada'] = $this->cantidad_solicitada_edit;
        $this->detalles[$this->editando_index]['precio_unitario'] = $this->precio_unitario_edit;

        $this->cancelarEdicion();
    }

    public function cancelarEdicion()
    {
        $this->reset(['editando_index', 'cantidad_solicitada_edit', 'precio_unitario_edit']);
    }

    public function eliminarDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    public function calcularSubtotal($detalle)
    {
        $cantidad = $detalle['cantidad_solicitada'] ?? 0;
        $precio = $detalle['precio_unitario'] ?? 0;
        return $cantidad * $precio;
    }

    public function calcularIva($detalle)
    {
        $subtotal = $this->calcularSubtotal($detalle);
        $iva_porcentaje = $detalle['iva_porcentaje'] ?? 0;
        return $subtotal * ($iva_porcentaje / 100);
    }

    public function calcularTotal()
    {
        $subtotal = 0;
        $total_iva = 0;
        $iva_10 = 0;
        $iva_5 = 0;
        $exenta = 0;

        foreach ($this->detalles as $detalle) {
            $subtotal_detalle = $this->calcularSubtotal($detalle);
            $iva_detalle = $this->calcularIva($detalle);

            $subtotal += $subtotal_detalle;
            $total_iva += $iva_detalle;

            // Clasificar por tipo de IVA
            if ($detalle['iva_porcentaje'] == 10) {
                $iva_10 += $iva_detalle;
            } elseif ($detalle['iva_porcentaje'] == 5) {
                $iva_5 += $iva_detalle;
            } else {
                $exenta += $subtotal_detalle;
            }
        }

        $total = $subtotal + $total_iva + ($this->flete ?? 0) - ($this->descuento_global ?? 0);

        return [
            'subtotal' => $subtotal,
            'iva_10' => $iva_10,
            'iva_5' => $iva_5,
            'exenta' => $exenta,
            'total_iva' => $total_iva,
            'total' => $total,
        ];
    }

    public function guardar()
    {
        $this->validate();

        if (empty($this->detalles)) {
            session()->flash('error', 'Debe agregar al menos un producto al pedido');
            return;
        }

        DB::beginTransaction();

        try {
            $totales = $this->calcularTotal();

            $pedido = PedidoCliente::updateOrCreate(
                ['id' => $this->pedidoId],
                [
                    'numero_pedido' => $this->numero_pedido ?: PedidoCliente::generarNumeroPedido(),
                    'fecha_pedido' => $this->fecha_pedido,
                    'cliente_id' => $this->cliente_id,
                    'sucursal_id' => $this->sucursal_id,
                    'deposito_id' => $this->deposito_id,
                    'vendedor_id' => $this->vendedor_id,
                    'tipo_entrega' => $this->tipo_entrega,
                    'subtotal' => $totales['subtotal'],
                    'iva_10' => $totales['iva_10'],
                    'iva_5' => $totales['iva_5'],
                    'exenta' => $totales['exenta'],
                    'total_iva' => $totales['total_iva'],
                    'descuento_global' => $this->descuento_global ?? 0,
                    'flete' => $this->flete ?? 0,
                    'total' => $totales['total'],
                    'estado' => 'BORRADOR',
                    'direccion_entrega' => $this->direccion_entrega,
                    'observaciones' => $this->observaciones,
                    'condiciones_especiales' => $this->condiciones_especiales,
                    'creado_por' => $this->pedidoId ? null : auth()->id(),
                    'actualizado_por' => auth()->id(),
                ]
            );

            // Eliminar detalles anteriores si es edición
            if ($this->pedidoId) {
                $pedido->detalles()->delete();
            }

            // Crear nuevos detalles
            foreach ($this->detalles as $detalle) {
                $subtotal = $this->calcularSubtotal($detalle);
                $iva_monto = $this->calcularIva($detalle);

                PedidoClienteDetalle::create([
                    'pedido_cliente_id' => $pedido->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad_solicitada' => $detalle['cantidad_solicitada'],
                    'cantidad_pendiente' => $detalle['cantidad_solicitada'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'iva_porcentaje' => $detalle['iva_porcentaje'],
                    'subtotal' => $subtotal,
                    'iva_monto' => $iva_monto,
                    'total' => $subtotal + $iva_monto,
                    'estado' => 'PENDIENTE',
                ]);
            }

            DB::commit();

            session()->flash('success', 'Pedido de cliente guardado correctamente');
            return redirect()->route('ventas.pedidos.historial');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar el pedido: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ventas.pedido-cliente-form');
    }
}
