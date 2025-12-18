<?php

namespace App\Livewire\Compras;

use App\Models\Compras\OrdenCompra;
use App\Models\Compras\OrdenCompraDetalle;
use App\Models\Compras\Presupuesto;
use App\Models\Compras\Proveedor;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class OrdenCompraForm extends Component
{
    // Cabecera
    public $ordenId;
    public $numero_orden;
    public $fecha_orden;
    public $fecha_entrega_esperada;
    public $proveedor_id;
    public $sucursal_id = 1;
    public $deposito_id = 1;
    public $presupuesto_id;
    public $condicion_pago = 'CONTADO';
    public $plazo_pago = '';
    public $tipo_orden = 'NORMAL';
    public $observaciones;
    public $condiciones_especiales;
    public $direccion_entrega;
    public $contacto_recepcion;
    public $telefono_recepcion;
    public $descuento_global = 0;
    public $flete = 0;

    // Búsqueda de presupuesto
    public $search_presupuesto = '';
    public $presupuestos_encontrados = [];
    public $mostrar_presupuestos = false;
    public $presupuesto_seleccionado = null;
    public $proveedor_seleccionado = null;

    // Detalles
    public $detalles = [];

    // Edición de detalle
    public $editando_index = null;
    public $cantidad_ordenada_edit = 0;
    public $precio_unitario_edit = 0;

    protected function rules()
    {
        return [
            'fecha_orden' => 'required|date',
            'fecha_entrega_esperada' => 'nullable|date|after_or_equal:fecha_orden',
            'proveedor_id' => 'required|exists:App\Models\Compras\Proveedor,id',
            'condicion_pago' => 'required|in:CONTADO,7_DIAS,15_DIAS,30_DIAS,60_DIAS,90_DIAS',
            'tipo_orden' => 'required|in:NORMAL,URGENTE,SERVICIO',
            'observaciones' => 'nullable|string',
        ];
    }

    public function mount($ordenId = null, $presupuestoId = null)
    {
        $this->fecha_orden = now()->format('Y-m-d');

        if ($presupuestoId) {
            $this->cargarDesdePresupuesto($presupuestoId);
        }

        if ($ordenId) {
            $this->ordenId = $ordenId;
            $this->cargarOrden();
        }
    }

    public function cargarDesdePresupuesto($presupuestoId)
    {
        $presupuesto = Presupuesto::with(['detalles', 'proveedor', 'pedidoCompra'])->findOrFail($presupuestoId);

        $this->presupuesto_id = $presupuesto->id;
        $this->proveedor_id = $presupuesto->proveedor_id;

        // Determinar condición de pago y plazo
        if ($presupuesto->condicion_pago === 'CONTADO') {
            $this->condicion_pago = 'CONTADO';
            $this->plazo_pago = '';
        } else {
            $this->condicion_pago = 'CREDITO';
            $this->plazo_pago = $presupuesto->condicion_pago;
        }

        $this->fecha_entrega_esperada = now()->addDays($presupuesto->dias_entrega ?? 0)->format('Y-m-d');

        $this->presupuesto_seleccionado = (object) [
            'id' => $presupuesto->id,
            'numero_presupuesto' => $presupuesto->numero_presupuesto,
            'proveedor_nombre' => $presupuesto->proveedor->nombre_fantasia,
        ];

        // Cargar datos del proveedor
        $this->proveedor_seleccionado = $presupuesto->proveedor;

        $this->search_presupuesto = $presupuesto->numero_presupuesto;

        // Cargar detalles desde el presupuesto
        $this->detalles = $presupuesto->detalles->map(function ($detalle) {
            $producto = DB::table('stock.PRODUCTOS')->where('id', $detalle->producto_id)->first();

            return [
                'producto_id' => $detalle->producto_id,
                'producto_codigo' => $producto->codigo ?? '',
                'producto_nombre' => $producto->nombre ?? 'Producto #' . $detalle->producto_id,
                'cantidad_ordenada' => $detalle->cantidad_cotizada,
                'precio_unitario' => $detalle->precio_unitario,
                'iva_porcentaje' => $detalle->iva_porcentaje,
                'marca' => $detalle->marca_ofrecida ?? '',
                'presupuesto_detalle_id' => $detalle->id,
            ];
        })->toArray();
    }

    public function cargarOrden()
    {
        $orden = OrdenCompra::with(['detalles', 'proveedor', 'presupuesto'])->findOrFail($this->ordenId);

        $this->numero_orden = $orden->numero_orden;
        $this->fecha_orden = $orden->fecha_orden->format('Y-m-d');
        $this->fecha_entrega_esperada = $orden->fecha_entrega_esperada?->format('Y-m-d');
        $this->proveedor_id = $orden->proveedor_id;
        $this->sucursal_id = $orden->sucursal_id;
        $this->deposito_id = $orden->deposito_id;
        $this->presupuesto_id = $orden->presupuesto_id;
        $this->condicion_pago = $orden->condicion_pago;
        $this->tipo_orden = $orden->tipo_orden;
        $this->descuento_global = $orden->descuento_global;
        $this->flete = $orden->flete;
        $this->direccion_entrega = $orden->direccion_entrega;
        $this->contacto_recepcion = $orden->contacto_recepcion;
        $this->telefono_recepcion = $orden->telefono_recepcion;
        $this->observaciones = $orden->observaciones;
        $this->condiciones_especiales = $orden->condiciones_especiales;

        // Cargar detalles
        $this->detalles = $orden->detalles->map(function ($detalle) {
            $producto = DB::table('stock.PRODUCTOS')->where('id', $detalle->producto_id)->first();

            return [
                'id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_codigo' => $producto->codigo ?? '',
                'producto_nombre' => $producto->nombre ?? 'Producto #' . $detalle->producto_id,
                'cantidad_ordenada' => $detalle->cantidad_ordenada,
                'precio_unitario' => $detalle->precio_unitario,
                'iva_porcentaje' => $detalle->iva_porcentaje,
                'marca' => $detalle->marca ?? '',
            ];
        })->toArray();
    }

    public function updatedSearchPresupuesto()
    {
        if (strlen($this->search_presupuesto) < 2) {
            $this->presupuestos_encontrados = [];
            $this->mostrar_presupuestos = false;
            return;
        }

        // Solo mostrar presupuestos APROBADOS que no tengan orden de compra
        $this->presupuestos_encontrados = Presupuesto::with('proveedor')
            ->where('estado', 'APROBADO')
            ->whereDoesntHave('ordenesCompra') // Que no tengan orden de compra ya generada
            ->where(function($query) {
                $query->where('numero_presupuesto', 'ILIKE', '%' . $this->search_presupuesto . '%')
                      ->orWhereHas('proveedor', function($q) {
                          $q->where('nombre_fantasia', 'ILIKE', '%' . $this->search_presupuesto . '%')
                            ->orWhere('razon_social', 'ILIKE', '%' . $this->search_presupuesto . '%');
                      });
            })
            ->limit(10)
            ->get()
            ->map(function($presupuesto) {
                return [
                    'id' => $presupuesto->id,
                    'numero_presupuesto' => $presupuesto->numero_presupuesto,
                    'proveedor_nombre' => $presupuesto->proveedor->nombre_fantasia,
                    'total' => $presupuesto->total,
                    'fecha_solicitud' => $presupuesto->fecha_solicitud->format('d/m/Y'),
                    'estado' => $presupuesto->estado,
                ];
            })
            ->toArray();

        $this->mostrar_presupuestos = count($this->presupuestos_encontrados) > 0;
    }

    public function seleccionarPresupuesto($presupuestoId)
    {
        $this->cargarDesdePresupuesto($presupuestoId);
        $this->mostrar_presupuestos = false;
    }

    public function editarDetalle($index)
    {
        if (!isset($this->detalles[$index])) {
            return;
        }

        $detalle = $this->detalles[$index];

        $this->editando_index = $index;
        $this->cantidad_ordenada_edit = $detalle['cantidad_ordenada'] ?? 0;
        $this->precio_unitario_edit = $detalle['precio_unitario'] ?? 0;
    }

    public function guardarEdicionDetalle()
    {
        if ($this->editando_index === null) {
            return;
        }

        $this->detalles[$this->editando_index]['cantidad_ordenada'] = $this->cantidad_ordenada_edit;
        $this->detalles[$this->editando_index]['precio_unitario'] = $this->precio_unitario_edit;

        $this->cancelarEdicion();
    }

    public function cancelarEdicion()
    {
        $this->reset(['editando_index', 'cantidad_ordenada_edit', 'precio_unitario_edit']);
    }

    public function eliminarDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    public function calcularSubtotal($detalle)
    {
        $cantidad = $detalle['cantidad_ordenada'] ?? 0;
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

        $total = $subtotal + $total_iva + ($this->flete ?? 0) - ($this->descuento_global ?? 0);

        return [
            'subtotal' => $subtotal,
            'total_iva' => $total_iva,
            'total' => $total,
        ];
    }

    public function guardar()
    {
        $this->validate();

        if (empty($this->detalles)) {
            session()->flash('error', 'Debe agregar al menos un producto a la orden');
            return;
        }

        DB::beginTransaction();

        try {
            $totales = $this->calcularTotal();

            $orden = OrdenCompra::updateOrCreate(
                ['id' => $this->ordenId],
                [
                    'numero_orden' => $this->numero_orden ?: $this->generarNumeroOrden(),
                    'fecha_orden' => $this->fecha_orden,
                    'fecha_entrega_esperada' => $this->fecha_entrega_esperada,
                    'proveedor_id' => $this->proveedor_id,
                    'sucursal_id' => $this->sucursal_id,
                    'deposito_id' => $this->deposito_id,
                    'presupuesto_id' => $this->presupuesto_id,
                    'condicion_pago' => $this->condicion_pago,
                    'tipo_orden' => $this->tipo_orden,
                    'subtotal' => $totales['subtotal'],
                    'total_iva' => $totales['total_iva'],
                    'descuento_global' => $this->descuento_global ?? 0,
                    'flete' => $this->flete ?? 0,
                    'total' => $totales['total'],
                    'estado' => 'PENDIENTE',
                    'direccion_entrega' => $this->direccion_entrega,
                    'contacto_recepcion' => $this->contacto_recepcion,
                    'telefono_recepcion' => $this->telefono_recepcion,
                    'observaciones' => $this->observaciones,
                    'condiciones_especiales' => $this->condiciones_especiales,
                    'creadoPor' => $this->ordenId ? null : auth()->id(),
                    'actualizadoPor' => auth()->id(),
                ]
            );

            // Eliminar detalles anteriores si es edición
            if ($this->ordenId) {
                $orden->detalles()->delete();
            }

            // Crear nuevos detalles
            foreach ($this->detalles as $detalle) {
                $subtotal = $this->calcularSubtotal($detalle);
                $iva_monto = $this->calcularIva($detalle);

                OrdenCompraDetalle::create([
                    'orden_compra_id' => $orden->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad_ordenada' => $detalle['cantidad_ordenada'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'iva_porcentaje' => $detalle['iva_porcentaje'],
                    'subtotal' => $subtotal,
                    'iva_monto' => $iva_monto,
                    'total' => $subtotal + $iva_monto,
                    'marca' => $detalle['marca'] ?? null,
                    'estado' => 'PENDIENTE',
                    'creadoPor' => auth()->id(),
                ]);
            }

            DB::commit();

            session()->flash('success', 'Orden de compra guardada correctamente');
            return redirect()->route('compras.ordenes.show', $orden->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar la orden: ' . $e->getMessage());
        }
    }

    private function generarNumeroOrden()
    {
        $ultimo = OrdenCompra::orderBy('id', 'desc')->first();
        $numero = $ultimo ? (int)substr($ultimo->numero_orden, -6) + 1 : 1;
        return 'OC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        $proveedores = Proveedor::where('activo', true)->get(['id', 'nombre_fantasia', 'razon_social']);

        return view('livewire.compras.orden-compra-form', [
            'proveedores' => $proveedores,
        ]);
    }
}
