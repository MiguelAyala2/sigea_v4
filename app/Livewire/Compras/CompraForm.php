<?php

namespace App\Livewire\Compras;

use App\Models\Compras\Compra;
use App\Models\Compras\CompraDetalle;
use App\Models\Compras\OrdenCompra;
use App\Models\Compras\Proveedor;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CompraForm extends Component
{
    // Cabecera de la compra
    public $compraId;
    public $proveedor_id;
    public $orden_compra_id;
    public $numero_factura;
    public $timbrado;
    public $fecha_emision;
    public $condicion_pago = '';
    public $tipo_factura = 'CONTADO';
    public $observaciones;

    // Búsqueda de proveedor
    public $search_proveedor = '';
    public $proveedores_encontrados = [];
    public $mostrar_proveedores = false;
    public $proveedor_seleccionado = null;

    // Órdenes de compra del proveedor
    public $ordenes_disponibles = [];
    public $mostrar_ordenes = false;
    public $orden_seleccionada = null;

    // Detalles
    public $detalles = [];

    // Totales calculados
    public $subtotal = 0;
    public $iva_10 = 0;
    public $iva_5 = 0;
    public $exenta = 0;
    public $total_iva = 0;
    public $total = 0;

    protected function rules()
    {
        $rules = [
            'proveedor_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = \Illuminate\Support\Facades\DB::table('compras.proveedores')
                        ->where('id', $value)
                        ->exists();

                    if (!$exists) {
                        $fail('El proveedor seleccionado no es válido.');
                    }
                },
            ],
            'numero_factura' => 'required|string|max:50',
            'timbrado' => 'nullable|string|max:20',
            'fecha_emision' => 'required|date',
            'tipo_factura' => 'required|in:CONTADO,CREDITO',
        ];

        // Solo validar condicion_pago si tipo_factura es CREDITO
        if ($this->tipo_factura === 'CREDITO') {
            $rules['condicion_pago'] = 'required|in:7_DIAS,15_DIAS,30_DIAS,60_DIAS,90_DIAS';
        }

        return $rules;
    }

    public function mount($compraId = null)
    {
        $this->fecha_emision = now()->format('Y-m-d');

        if ($compraId) {
            $this->compraId = $compraId;
            $this->cargarCompra();
        }
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
                $query->where('nombre_fantasia', 'ILIKE', '%' . $this->search_proveedor . '%')
                      ->orWhere('razon_social', 'ILIKE', '%' . $this->search_proveedor . '%')
                      ->orWhere('ruc', 'ILIKE', '%' . $this->search_proveedor . '%');
            })
            ->limit(10)
            ->get(['id', 'nombre_fantasia', 'razon_social', 'ruc'])
            ->toArray();

        $this->mostrar_proveedores = count($this->proveedores_encontrados) > 0;
    }

    public function seleccionarProveedor($proveedorId)
    {
        $proveedor = Proveedor::find($proveedorId);

        if (!$proveedor) {
            return;
        }

        $this->proveedor_id = $proveedor->id;
        $this->proveedor_seleccionado = $proveedor;
        $this->search_proveedor = $proveedor->nombre_fantasia;
        $this->mostrar_proveedores = false;

        // Cargar órdenes de compra aprobadas del proveedor
        $this->cargarOrdenesProveedor();
    }

    public function cargarOrdenesProveedor()
    {
        if (!$this->proveedor_id) {
            return;
        }

        // Buscar órdenes APROBADAS que no tengan factura registrada
        $this->ordenes_disponibles = OrdenCompra::where('proveedor_id', $this->proveedor_id)
            ->where('estado', 'APROBADO')
            ->whereDoesntHave('compras') // Que no tengan factura ya registrada
            ->with('detalles')
            ->get()
            ->map(function($orden) {
                return [
                    'id' => $orden->id,
                    'numero_orden' => $orden->numero_orden,
                    'fecha_orden' => $orden->fecha_orden->format('d/m/Y'),
                    'total' => $orden->total,
                    'estado' => $orden->estado,
                    'cantidad_items' => $orden->detalles->count(),
                ];
            })
            ->toArray();

        $this->mostrar_ordenes = count($this->ordenes_disponibles) > 0;

        // Si solo hay una orden, seleccionarla automáticamente
        if (count($this->ordenes_disponibles) === 1) {
            $this->seleccionarOrden($this->ordenes_disponibles[0]['id']);
        }
    }

    public function seleccionarOrden($ordenId)
    {
        $orden = OrdenCompra::with(['detalles', 'proveedor'])->find($ordenId);

        if (!$orden) {
            return;
        }

        $this->orden_compra_id = $orden->id;
        $this->orden_seleccionada = (object) [
            'id' => $orden->id,
            'numero_orden' => $orden->numero_orden,
            'total' => $orden->total,
        ];

        // Determinar tipo de factura según condición de pago de la orden
        if ($orden->condicion_pago === 'CONTADO') {
            $this->tipo_factura = 'CONTADO';
            $this->condicion_pago = '';
        } else {
            $this->tipo_factura = 'CREDITO';
            $this->condicion_pago = $orden->condicion_pago;
        }

        // Cargar detalles desde la orden de compra
        $this->detalles = $orden->detalles->map(function ($detalle) {
            $producto = DB::table('stock.PRODUCTOS')->where('id', $detalle->producto_id)->first();

            return [
                'orden_compra_detalle_id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_codigo' => $producto->codigo ?? '',
                'producto_nombre' => $producto->nombre ?? 'Producto #' . $detalle->producto_id,
                'cantidad' => $detalle->cantidad_ordenada,
                'precio_unitario' => $detalle->precio_unitario,
                'iva_porcentaje' => $detalle->iva_porcentaje,
                'subtotal' => $detalle->subtotal,
                'iva_monto' => $detalle->iva_monto,
                'total' => $detalle->total,
            ];
        })->toArray();

        $this->calcularTotales();
        $this->mostrar_ordenes = false;
    }

    public function calcularTotales()
    {
        $this->subtotal = 0;
        $this->iva_10 = 0;
        $this->iva_5 = 0;
        $this->exenta = 0;
        $this->total_iva = 0;

        foreach ($this->detalles as $detalle) {
            $this->subtotal += $detalle['subtotal'];

            if ($detalle['iva_porcentaje'] == 10) {
                $this->iva_10 += $detalle['iva_monto'];
            } elseif ($detalle['iva_porcentaje'] == 5) {
                $this->iva_5 += $detalle['iva_monto'];
            } else {
                $this->exenta += $detalle['subtotal'];
            }

            $this->total_iva += $detalle['iva_monto'];
        }

        $this->total = $this->subtotal + $this->total_iva;
    }

    public function guardar()
    {
        $this->validate();

        if (empty($this->detalles)) {
            session()->flash('error', 'Debe seleccionar una orden de compra con productos');
            return;
        }

        DB::beginTransaction();

        try {
            $compra = Compra::create([
                'proveedor_id' => $this->proveedor_id,
                'sucursal_id' => 1,
                'deposito_id' => 1,
                'orden_compra_id' => $this->orden_compra_id,
                'numero_factura' => $this->numero_factura,
                'timbrado' => $this->timbrado,
                'fecha_emision' => $this->fecha_emision,
                'condicion_pago' => $this->tipo_factura === 'CONTADO' ? 'CONTADO' : $this->condicion_pago,
                'tipo_factura' => $this->tipo_factura,
                'subtotal' => $this->subtotal,
                'iva_10' => $this->iva_10,
                'iva_5' => $this->iva_5,
                'exenta' => $this->exenta,
                'total_iva' => $this->total_iva,
                'total' => $this->total,
                'estado' => 'PENDIENTE',
                'observaciones' => $this->observaciones,
                'creadoPor' => auth()->id(),
            ]);

            // Crear detalles
            foreach ($this->detalles as $detalle) {
                CompraDetalle::create([
                    'compra_id' => $compra->id,
                    'orden_compra_detalle_id' => $detalle['orden_compra_detalle_id'],
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['subtotal'],
                    'iva_porcentaje' => $detalle['iva_porcentaje'],
                    'iva_monto' => $detalle['iva_monto'],
                    'total' => $detalle['total'],
                    'creadoPor' => auth()->id(),
                ]);
            }

            DB::commit();

            session()->flash('success', 'Compra registrada correctamente');
            return redirect()->route('compras.compras.show', $compra->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar la compra: ' . $e->getMessage());
        }
    }

    public function cargarCompra()
    {
        $compra = Compra::with(['detalles', 'proveedor', 'ordenCompra'])->findOrFail($this->compraId);

        $this->proveedor_id = $compra->proveedor_id;
        $this->proveedor_seleccionado = $compra->proveedor;
        $this->search_proveedor = $compra->proveedor->nombre_fantasia;
        $this->orden_compra_id = $compra->orden_compra_id;
        $this->numero_factura = $compra->numero_factura;
        $this->timbrado = $compra->timbrado;
        $this->fecha_emision = $compra->fecha_emision->format('Y-m-d');
        $this->tipo_factura = $compra->tipo_factura;
        $this->condicion_pago = $compra->tipo_factura === 'CREDITO' ? $compra->condicion_pago : '';
        $this->observaciones = $compra->observaciones;

        if ($compra->ordenCompra) {
            $this->orden_seleccionada = (object) [
                'id' => $compra->ordenCompra->id,
                'numero_orden' => $compra->ordenCompra->numero_orden,
                'total' => $compra->ordenCompra->total,
            ];
        }

        // Cargar detalles
        $this->detalles = $compra->detalles->map(function ($detalle) {
            $producto = DB::table('stock.PRODUCTOS')->where('id', $detalle->producto_id)->first();

            return [
                'id' => $detalle->id,
                'orden_compra_detalle_id' => $detalle->orden_compra_detalle_id,
                'producto_id' => $detalle->producto_id,
                'producto_codigo' => $producto->codigo ?? '',
                'producto_nombre' => $producto->nombre ?? 'Producto #' . $detalle->producto_id,
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'iva_porcentaje' => $detalle->iva_porcentaje,
                'subtotal' => $detalle->subtotal,
                'iva_monto' => $detalle->iva_monto,
                'total' => $detalle->total,
            ];
        })->toArray();

        $this->calcularTotales();
    }

    public function render()
    {
        return view('livewire.compras.compra-form');
    }
}
