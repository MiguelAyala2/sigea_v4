<?php

namespace App\Livewire\Compras;

use App\Models\Compras\Presupuesto;
use App\Models\Compras\PresupuestoDetalle;
use App\Models\Compras\PedidoCompra;
use App\Models\Compras\Proveedor;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PresupuestoForm extends Component
{
    public $presupuestoId;
    public $numero_presupuesto;
    public $fecha_solicitud;
    public $fecha_vencimiento;
    public $condicion_pago = 'CONTADO';
    public $plazo_pago = '';
    public $dias_entrega = 0;
    public $observaciones;

    // Búsqueda de proveedor
    public $search_proveedor = '';
    public $proveedores_encontrados = [];
    public $mostrar_proveedores = false;
    public $proveedor_seleccionado = null;

    // Búsqueda de pedido de compra
    public $search_pedido = '';
    public $pedidos_encontrados = [];
    public $mostrar_pedidos = false;
    public $pedido_seleccionado = null;

    public $detalles = [];

    // Campos para editar detalle individual
    public $editando_index = null;
    public $precio_unitario_edit = 0;
    public $iva_porcentaje_edit = 10;
    public $dias_entrega_item_edit = null;
    public $marca_ofrecida_edit = '';

    protected function rules()
    {
        $rules = [
            'fecha_solicitud' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_solicitud',
            'condicion_pago' => 'required|in:CONTADO,CREDITO',
            'dias_entrega' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string',
        ];

        // Solo validar plazo_pago si condicion_pago es CREDITO
        if ($this->condicion_pago === 'CREDITO') {
            $rules['plazo_pago'] = 'required|in:7_DIAS,15_DIAS,30_DIAS,60_DIAS,90_DIAS';
        }

        return $rules;
    }

    public function mount($presupuestoId = null)
    {
        $this->fecha_solicitud = now()->format('Y-m-d');

        if ($presupuestoId) {
            $this->presupuestoId = $presupuestoId;
            $this->cargarPresupuesto();
        }
    }

    public function cargarPresupuesto()
    {
        $presupuesto = Presupuesto::with(['detalles', 'proveedor', 'pedidoCompra'])->findOrFail($this->presupuestoId);

        $this->numero_presupuesto = $presupuesto->numero_presupuesto;
        $this->fecha_solicitud = $presupuesto->fecha_solicitud->format('Y-m-d');
        $this->fecha_vencimiento = $presupuesto->fecha_vencimiento?->format('Y-m-d');

        // Determinar condición de pago y plazo
        if ($presupuesto->condicion_pago === 'CONTADO') {
            $this->condicion_pago = 'CONTADO';
            $this->plazo_pago = '';
        } else {
            $this->condicion_pago = 'CREDITO';
            $this->plazo_pago = $presupuesto->condicion_pago;
        }

        $this->dias_entrega = $presupuesto->dias_entrega;
        $this->observaciones = $presupuesto->observaciones;

        // Cargar proveedor
        if ($presupuesto->proveedor) {
            $this->proveedor_seleccionado = (object) [
                'id' => $presupuesto->proveedor->id,
                'razon_social' => $presupuesto->proveedor->razon_social,
                'nombre_fantasia' => $presupuesto->proveedor->nombre_fantasia,
            ];
            $this->search_proveedor = $presupuesto->proveedor->nombre_fantasia;
        }

        // Cargar pedido de compra
        if ($presupuesto->pedidoCompra) {
            $this->pedido_seleccionado = (object) [
                'id' => $presupuesto->pedidoCompra->id,
                'numero_pedido' => $presupuesto->pedidoCompra->numero_pedido,
            ];
            $this->search_pedido = $presupuesto->pedidoCompra->numero_pedido;
        }

        // Cargar detalles
        $this->detalles = $presupuesto->detalles->map(function ($detalle) {
            $producto = DB::table('stock.PRODUCTOS')->where('id', $detalle->producto_id)->first();

            return [
                'id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $producto->nombre ?? 'Producto #' . $detalle->producto_id,
                'producto_codigo' => $producto->codigo ?? '',
                'cantidad_cotizada' => $detalle->cantidad_cotizada,
                'precio_unitario' => $detalle->precio_unitario,
                'iva_porcentaje' => $detalle->iva_porcentaje,
                'dias_entrega_item' => $detalle->dias_entrega_item,
                'marca_ofrecida' => $detalle->marca_ofrecida,
                'pedido_compra_detalle_id' => $detalle->pedido_compra_detalle_id,
            ];
        })->toArray();
    }

    public function updatedSearchProveedor()
    {
        if (strlen($this->search_proveedor) < 2) {
            $this->proveedores_encontrados = [];
            $this->mostrar_proveedores = false;
            return;
        }

        $this->proveedores_encontrados = Proveedor::where('activo', true)
            ->where(function($query) {
                $query->where('razon_social', 'ILIKE', '%' . $this->search_proveedor . '%')
                      ->orWhere('nombre_fantasia', 'ILIKE', '%' . $this->search_proveedor . '%')
                      ->orWhere('ruc', 'ILIKE', '%' . $this->search_proveedor . '%');
            })
            ->limit(10)
            ->get(['id', 'razon_social', 'nombre_fantasia', 'ruc'])
            ->toArray();

        $this->mostrar_proveedores = count($this->proveedores_encontrados) > 0;
    }

    public function seleccionarProveedor($proveedorId)
    {
        $proveedor = Proveedor::find($proveedorId);

        if ($proveedor) {
            $this->proveedor_seleccionado = (object) [
                'id' => $proveedor->id,
                'razon_social' => $proveedor->razon_social,
                'nombre_fantasia' => $proveedor->nombre_fantasia,
            ];
            $this->search_proveedor = $proveedor->nombre_fantasia;
            $this->mostrar_proveedores = false;
        }
    }

    public function updatedSearchPedido()
    {
        if (strlen($this->search_pedido) < 2) {
            $this->pedidos_encontrados = [];
            $this->mostrar_pedidos = false;
            return;
        }

        // Solo mostrar pedidos APROBADOS
        $this->pedidos_encontrados = PedidoCompra::where('activo', true)
            ->where('estado', 'APROBADO')
            ->where('numero_pedido', 'ILIKE', '%' . $this->search_pedido . '%')
            ->limit(10)
            ->get(['id', 'numero_pedido', 'fecha_pedido', 'tipo_pedido', 'total_estimado'])
            ->map(function($pedido) {
                return [
                    'id' => $pedido->id,
                    'numero_pedido' => $pedido->numero_pedido,
                    'fecha_pedido' => $pedido->fecha_pedido->format('d/m/Y'),
                    'tipo_pedido' => $pedido->tipo_pedido,
                    'total_estimado' => $pedido->total_estimado,
                ];
            })
            ->toArray();

        $this->mostrar_pedidos = count($this->pedidos_encontrados) > 0;
    }

    public function seleccionarPedido($pedidoId)
    {
        $pedido = PedidoCompra::with('detalles')->find($pedidoId);

        if ($pedido) {
            $this->pedido_seleccionado = (object) [
                'id' => $pedido->id,
                'numero_pedido' => $pedido->numero_pedido,
            ];
            $this->search_pedido = $pedido->numero_pedido;
            $this->mostrar_pedidos = false;

            // Cargar productos del pedido en los detalles
            $this->cargarProductosDePedido($pedido);
        }
    }

    private function cargarProductosDePedido($pedido)
    {
        $this->detalles = [];

        foreach ($pedido->detalles as $detalle) {
            $producto = DB::table('stock.PRODUCTOS')->where('id', $detalle->producto_id)->first();

            $this->detalles[] = [
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $producto->nombre ?? 'Producto #' . $detalle->producto_id,
                'producto_codigo' => $producto->codigo ?? '',
                'cantidad_cotizada' => $detalle->cantidad_aprobada ?? $detalle->cantidad_solicitada,
                'precio_unitario' => $detalle->precio_estimado ?? 0,
                'iva_porcentaje' => 10,
                'dias_entrega_item' => null,
                'marca_ofrecida' => '',
                'pedido_compra_detalle_id' => $detalle->id,
            ];
        }
    }

    public function editarDetalle($index)
    {
        if (!isset($this->detalles[$index])) {
            session()->flash('error_producto', 'Producto no encontrado');
            return;
        }

        $detalle = $this->detalles[$index];

        $this->editando_index = $index;
        $this->precio_unitario_edit = $detalle['precio_unitario'] ?? 0;
        $this->iva_porcentaje_edit = $detalle['iva_porcentaje'] ?? 10;
        $this->dias_entrega_item_edit = $detalle['dias_entrega_item'] ?? null;
        $this->marca_ofrecida_edit = $detalle['marca_ofrecida'] ?? '';
    }

    public function guardarEdicionDetalle()
    {
        if ($this->editando_index === null) {
            return;
        }

        $this->detalles[$this->editando_index]['precio_unitario'] = $this->precio_unitario_edit;
        $this->detalles[$this->editando_index]['iva_porcentaje'] = $this->iva_porcentaje_edit;
        $this->detalles[$this->editando_index]['dias_entrega_item'] = $this->dias_entrega_item_edit;
        $this->detalles[$this->editando_index]['marca_ofrecida'] = $this->marca_ofrecida_edit;

        $this->cancelarEdicion();
    }

    public function cancelarEdicion()
    {
        $this->reset(['editando_index', 'precio_unitario_edit', 'iva_porcentaje_edit', 'dias_entrega_item_edit', 'marca_ofrecida_edit']);
    }

    public function eliminarDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    public function calcularSubtotal($detalle)
    {
        $cantidad = $detalle['cantidad_cotizada'] ?? 0;
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

        foreach ($this->detalles as $detalle) {
            $subtotal += $this->calcularSubtotal($detalle);
            $total_iva += $this->calcularIva($detalle);
        }

        $total = $subtotal + $total_iva + ($this->flete ?? 0) - ($this->descuento_general ?? 0);

        return [
            'subtotal' => $subtotal,
            'total_iva' => $total_iva,
            'total' => $total,
        ];
    }

    public function guardar()
    {
        $this->validate();

        if (!$this->proveedor_seleccionado) {
            session()->flash('error', 'Debe seleccionar un proveedor');
            return;
        }

        if (empty($this->detalles)) {
            session()->flash('error', 'Debe agregar al menos un producto al presupuesto');
            return;
        }

        DB::beginTransaction();

        try {
            $totales = $this->calcularTotal();

            $presupuesto = Presupuesto::updateOrCreate(
                ['id' => $this->presupuestoId],
                [
                    'numero_presupuesto' => $this->numero_presupuesto ?: $this->generarNumeroPresupuesto(),
                    'pedido_compra_id' => $this->pedido_seleccionado->id ?? null,
                    'proveedor_id' => $this->proveedor_seleccionado->id,
                    'fecha_solicitud' => $this->fecha_solicitud,
                    'fecha_vencimiento' => $this->fecha_vencimiento,
                    'condicion_pago' => $this->condicion_pago === 'CONTADO' ? 'CONTADO' : $this->plazo_pago,
                    'dias_entrega' => $this->dias_entrega,
                    'subtotal' => $totales['subtotal'],
                    'total_iva' => $totales['total_iva'],
                    'total' => $totales['total'],
                    'estado' => 'PENDIENTE',
                    'observaciones' => $this->observaciones,
                    'solicitadoPor' => auth()->id(),
                    'creadoPor' => $this->presupuestoId ? null : auth()->id(),
                    'actualizadoPor' => auth()->id(),
                ]
            );

            // Eliminar detalles anteriores si es edición
            if ($this->presupuestoId) {
                $presupuesto->detalles()->delete();
            }

            // Crear nuevos detalles
            foreach ($this->detalles as $detalle) {
                $subtotal = $this->calcularSubtotal($detalle);
                $iva_monto = $this->calcularIva($detalle);

                PresupuestoDetalle::create([
                    'presupuesto_id' => $presupuesto->id,
                    'producto_id' => $detalle['producto_id'],
                    'pedido_compra_detalle_id' => $detalle['pedido_compra_detalle_id'] ?? null,
                    'cantidad_cotizada' => $detalle['cantidad_cotizada'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'iva_porcentaje' => $detalle['iva_porcentaje'],
                    'subtotal' => $subtotal,
                    'iva_monto' => $iva_monto,
                    'total' => $subtotal + $iva_monto,
                    'dias_entrega_item' => $detalle['dias_entrega_item'],
                    'marca_ofrecida' => $detalle['marca_ofrecida'],
                    'creadoPor' => auth()->id(),
                ]);
            }

            DB::commit();

            session()->flash('success', 'Presupuesto guardado correctamente');
            return redirect()->route('compras.presupuestos.show', $presupuesto->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar el presupuesto: ' . $e->getMessage());
        }
    }

    private function generarNumeroPresupuesto()
    {
        $ultimo = Presupuesto::orderBy('id', 'desc')->first();
        $numero = $ultimo ? (int)substr($ultimo->numero_presupuesto, -6) + 1 : 1;
        return 'PRES-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.compras.presupuesto-form');
    }
}
