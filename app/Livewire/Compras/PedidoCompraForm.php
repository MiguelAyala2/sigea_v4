<?php

namespace App\Livewire\Compras;

use App\Models\Compras\PedidoCompra;
use App\Models\Compras\PedidoCompraDetalle;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PedidoCompraForm extends Component
{
    public $pedidoId;
    public $numero_pedido;
    public $fecha_pedido;
    public $fecha_necesaria;
    public $sucursal_id = 1;
    public $deposito_destino_id;
    public $tipo_pedido = 'REPOSICION_STOCK';
    public $prioridad = 'NORMAL';
    public $observaciones;

    public $detalles = [];

    // Campos para agregar/editar detalle
    public $editando_index = null;
    public $search_producto = '';
    public $productos_encontrados = [];
    public $mostrar_resultados = false;
    public $producto_seleccionado = null;
    public $cantidad_solicitada = 1;
    public $precio_estimado = 0;

    protected function rules()
    {
        return [
            'fecha_pedido' => 'required|date',
            'fecha_necesaria' => 'nullable|date|after_or_equal:fecha_pedido',
            'sucursal_id' => 'required',
            'tipo_pedido' => 'required|in:REPOSICION_STOCK,COMPRA_DIRECTA,PROYECTO_ESPECIFICO,MANTENIMIENTO,INSUMOS',
            'prioridad' => 'required|in:NORMAL,URGENTE,CRITICA',
            'observaciones' => 'nullable|string',
        ];
    }

    public function mount($pedidoId = null)
    {
        $this->fecha_pedido = now()->format('Y-m-d');

        if ($pedidoId) {
            $this->pedidoId = $pedidoId;
            $this->cargarPedido();
        }
    }

    public function cargarPedido()
    {
        $pedido = PedidoCompra::with('detalles')->findOrFail($this->pedidoId);

        $this->numero_pedido = $pedido->numero_pedido;
        $this->fecha_pedido = $pedido->fecha_pedido->format('Y-m-d');
        $this->fecha_necesaria = $pedido->fecha_necesaria?->format('Y-m-d');
        $this->sucursal_id = $pedido->sucursal_id;
        $this->deposito_destino_id = $pedido->deposito_destino_id;
        $this->tipo_pedido = $pedido->tipo_pedido;
        $this->prioridad = $pedido->prioridad;
        $this->observaciones = $pedido->observaciones;

        $this->detalles = $pedido->detalles->map(function ($detalle) {
            // Obtener info del producto
            $producto = DB::table('stock.PRODUCTOS')->where('id', $detalle->producto_id)->first();

            return [
                'id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $producto->nombre ?? 'Producto #' . $detalle->producto_id,
                'cantidad_solicitada' => $detalle->cantidad_solicitada,
                'precio_estimado' => $detalle->precio_estimado,
                'stock_actual' => $detalle->stock_actual,
            ];
        })->toArray();
    }

    public function updatedSearchProducto()
    {
        if (strlen($this->search_producto) < 2) {
            $this->productos_encontrados = [];
            $this->mostrar_resultados = false;
            return;
        }

        // Buscar productos que coincidan
        $this->productos_encontrados = DB::table('stock.PRODUCTOS')
            ->where('activo', true)
            ->where(function($query) {
                $query->where('nombre', 'ILIKE', '%' . $this->search_producto . '%')
                      ->orWhere('codigo', 'ILIKE', '%' . $this->search_producto . '%')
                      ->orWhere('descripcion', 'ILIKE', '%' . $this->search_producto . '%');
            })
            ->limit(10)
            ->get()
            ->map(function($producto) {
                // Obtener stock actual
                $stock = DB::table('stock.STOCK')
                    ->where('producto_id', $producto->id)
                    ->sum('stock_actual');

                // Obtener precio de compra actual
                $precio = DB::table('stock.PRECIOS')
                    ->where('producto_id', $producto->id)
                    ->where('es_actual', true)
                    ->value('precio_compra');

                return [
                    'id' => $producto->id,
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'stock_actual' => $stock ?? 0,
                    'precio_compra' => $precio ?? 0,
                ];
            })
            ->toArray();

        $this->mostrar_resultados = count($this->productos_encontrados) > 0;
    }

    public function seleccionarProducto($productoId)
    {
        $producto = collect($this->productos_encontrados)->firstWhere('id', $productoId);

        if ($producto) {
            $this->producto_seleccionado = (object) $producto;
            $this->search_producto = $producto['nombre'];
            $this->precio_estimado = $producto['precio_compra'];
            $this->mostrar_resultados = false;
        }
    }

    public function agregarDetalle()
    {
        if (!$this->producto_seleccionado) {
            session()->flash('error_producto', 'Debe seleccionar un producto');
            return;
        }

        if ($this->cantidad_solicitada <= 0) {
            session()->flash('error_producto', 'La cantidad debe ser mayor a 0');
            return;
        }

        if ($this->editando_index !== null) {
            // Actualizar detalle existente
            $this->detalles[$this->editando_index] = [
                'producto_id' => $this->producto_seleccionado->id,
                'producto_nombre' => $this->producto_seleccionado->nombre,
                'cantidad_solicitada' => $this->cantidad_solicitada,
                'precio_estimado' => $this->precio_estimado,
                'stock_actual' => $this->producto_seleccionado->stock_actual,
            ];
            $this->editando_index = null;
        } else {
            // Verificar si ya existe el producto
            $existe = false;
            foreach ($this->detalles as $index => $detalle) {
                if ($detalle['producto_id'] == $this->producto_seleccionado->id) {
                    $this->detalles[$index]['cantidad_solicitada'] += $this->cantidad_solicitada;
                    $existe = true;
                    break;
                }
            }

            if (!$existe) {
                $this->detalles[] = [
                    'producto_id' => $this->producto_seleccionado->id,
                    'producto_nombre' => $this->producto_seleccionado->nombre,
                    'cantidad_solicitada' => $this->cantidad_solicitada,
                    'precio_estimado' => $this->precio_estimado,
                    'stock_actual' => $this->producto_seleccionado->stock_actual,
                ];
            }
        }

        $this->limpiarFormularioDetalle();
    }

    public function editarDetalle($index)
    {
        $detalle = $this->detalles[$index];

        $this->editando_index = $index;
        $this->search_producto = $detalle['producto_nombre'];
        $this->cantidad_solicitada = $detalle['cantidad_solicitada'];
        $this->precio_estimado = $detalle['precio_estimado'];

        // Cargar producto
        $producto = DB::table('stock.PRODUCTOS')->where('id', $detalle['producto_id'])->first();
        if ($producto) {
            $this->producto_seleccionado = (object) [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
                'stock_actual' => $detalle['stock_actual'],
            ];
        }
    }

    public function cancelarEdicion()
    {
        $this->limpiarFormularioDetalle();
    }

    public function eliminarDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    private function limpiarFormularioDetalle()
    {
        $this->reset(['search_producto', 'producto_seleccionado', 'cantidad_solicitada', 'precio_estimado', 'editando_index', 'productos_encontrados', 'mostrar_resultados']);
        $this->cantidad_solicitada = 1;
    }

    public function calcularTotal()
    {
        $total = 0;
        foreach ($this->detalles as $detalle) {
            $total += $detalle['cantidad_solicitada'] * $detalle['precio_estimado'];
        }
        return $total;
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
            $pedido = PedidoCompra::updateOrCreate(
                ['id' => $this->pedidoId],
                [
                    'numero_pedido' => $this->numero_pedido ?: $this->generarNumeroPedido(),
                    'fecha_pedido' => $this->fecha_pedido,
                    'fecha_necesaria' => $this->fecha_necesaria,
                    'sucursal_id' => $this->sucursal_id,
                    'deposito_destino_id' => $this->deposito_destino_id,
                    'usuario_solicitante_id' => auth()->id(),
                    'tipo_pedido' => $this->tipo_pedido,
                    'prioridad' => $this->prioridad,
                    'estado' => 'PENDIENTE',
                    'observaciones' => $this->observaciones,
                    'urgente' => $this->prioridad == 'CRITICA',
                    'creadoPor' => $this->pedidoId ? null : auth()->id(),
                    'actualizadoPor' => auth()->id(),
                ]
            );

            // Eliminar detalles anteriores si es edición
            if ($this->pedidoId) {
                $pedido->detalles()->delete();
            }

            // Crear nuevos detalles
            foreach ($this->detalles as $detalle) {
                PedidoCompraDetalle::create([
                    'pedido_compra_id' => $pedido->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad_solicitada' => $detalle['cantidad_solicitada'],
                    'precio_estimado' => $detalle['precio_estimado'],
                    'stock_actual' => $detalle['stock_actual'] ?? 0,
                    'estado' => 'PENDIENTE',
                    'creadoPor' => auth()->id(),
                ]);
            }

            $pedido->calcularTotalEstimado();

            DB::commit();

            session()->flash('success', 'Pedido de compra guardado correctamente');
            return redirect()->route('compras.pedidos.show', $pedido->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar el pedido: ' . $e->getMessage());
        }
    }

    private function generarNumeroPedido()
    {
        $ultimo = PedidoCompra::orderBy('id', 'desc')->first();
        $numero = $ultimo ? (int)substr($ultimo->numero_pedido, -6) + 1 : 1;
        return 'PC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.compras.pedido-compra-form');
    }
}
