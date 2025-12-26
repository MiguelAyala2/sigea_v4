<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\NotaCredito;
use App\Models\Compras\Proveedor;
use App\Models\Compras\Compra;
use App\Models\Compras\CuentaPorPagar;
use App\Models\Stock\Producto;
use App\Models\Stock\Stock;
use App\Models\Stock\MovimientoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaCreditoController extends Controller
{
    public function index()
    {
        $notasCredito = NotaCredito::with(['proveedor', 'empresa', 'creador'])
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        return view('compras.notas-credito.index', compact('notasCredito'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->get();
        $compras = Compra::where('estado', 'APROBADO')->get();
        $productos = Producto::where('activo', true)->get();

        // Generar número automático para la nota de crédito
        $ultimaNotaCredito = NotaCredito::orderBy('id', 'desc')->first();
        $numeroSecuencial = $ultimaNotaCredito ? (intval(substr($ultimaNotaCredito->numero, -5)) + 1) : 1;
        $numeroAutomatico = 'NC-001-001-' . str_pad($numeroSecuencial, 5, '0', STR_PAD_LEFT);

        return view('compras.notas-credito.create', compact('proveedores', 'compras', 'productos', 'numeroAutomatico'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = \DB::table('compras.proveedores')->where('id', $value)->exists();
                    if (!$exists) {
                        $fail('El proveedor seleccionado no es válido.');
                    }
                },
            ],
            'compra_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $exists = \DB::table('compras.compras')->where('id', $value)->exists();
                        if (!$exists) {
                            $fail('La compra seleccionada no es válida.');
                        }
                    }
                },
            ],
            'numero' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $exists = \DB::table('compras.notas_credito')->where('numero', $value)->exists();
                    if ($exists) {
                        $fail('El número de nota de crédito ya existe.');
                    }
                },
            ],
            'fecha' => 'required|date',
            'numero_factura_afectada' => 'nullable|string|max:50',
            'motivo' => 'required|string',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $exists = \DB::table('stock.PRODUCTOS')->where('id', $value)->exists();
                        if (!$exists) {
                            $fail('El producto seleccionado no es válido.');
                        }
                    }
                },
            ],
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
        ]);

        $notaCredito = NotaCredito::create([
            'proveedor_id' => $validated['proveedor_id'],
            'compra_id' => $validated['compra_id'] ?? null,
            'empresa_id' => session('empresa_id', 1),
            'numero' => $validated['numero'],
            'fecha' => $validated['fecha'],
            'numero_factura_afectada' => $validated['numero_factura_afectada'] ?? null,
            'motivo' => $validated['motivo'],
            'observaciones' => $validated['observaciones'] ?? null,
            'estado' => 'borrador',
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['detalles'] as $detalle) {
            $notaCredito->detalles()->create([
                'producto_id' => $detalle['producto_id'] ?? null,
                'descripcion' => $detalle['descripcion'],
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
                'descuento' => $detalle['descuento'] ?? 0,
            ]);
        }

        foreach ($notaCredito->detalles as $detalle) {
            $detalle->calcularTotales();
        }

        $notaCredito->recalcularTotales();

        // Crear registro en cuentas_por_pagar con monto NEGATIVO
        CuentaPorPagar::create([
            'compra_id' => $notaCredito->compra_id,
            'proveedor_id' => $notaCredito->proveedor_id,
            'numero_documento' => $notaCredito->numero,
            'timbrado' => null,
            'tipo' => 'NOTA_CREDITO',
            'fecha_emision' => $notaCredito->fecha,
            'fecha_vencimiento' => $notaCredito->fecha,
            'condicion_pago' => 'CONTADO',
            'monto_total' => -abs($notaCredito->total),
            'monto_pagado' => 0,
            'saldo_pendiente' => -abs($notaCredito->total),
            'moneda' => 'PYG',
            'estado' => 'APLICADA',
            'observaciones' => "Nota de Crédito #{$notaCredito->numero} - {$notaCredito->motivo}",
            'creadoPor' => auth()->id(),
        ]);

        // Disminuir stock por devolución de mercadería
        $this->disminuirStock($notaCredito);

        return redirect()->route('compras.notas-credito.show', $notaCredito)
            ->with('success', 'Nota de crédito creada exitosamente. Stock actualizado.');
    }

    public function show(NotaCredito $notaCredito)
    {
        $notaCredito->load(['proveedor', 'empresa', 'compra', 'detalles.producto', 'creador']);

        return view('compras.notas-credito.show', compact('notaCredito'));
    }

    public function edit(NotaCredito $notaCredito)
    {
        if ($notaCredito->estado !== 'borrador') {
            return redirect()->route('compras.notas-credito.show', $notaCredito)
                ->with('error', 'Solo se pueden editar notas de crédito en estado borrador');
        }

        $proveedores = Proveedor::where('activo', true)->get();
        $compras = Compra::where('estado', 'APROBADO')->get();
        $productos = Producto::where('activo', true)->get();

        return view('compras.notas-credito.edit', compact('notaCredito', 'proveedores', 'compras', 'productos'));
    }

    public function update(Request $request, NotaCredito $notaCredito)
    {
        if ($notaCredito->estado !== 'borrador') {
            return redirect()->route('compras.notas-credito.show', $notaCredito)
                ->with('error', 'Solo se pueden editar notas de crédito en estado borrador');
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'numero_factura_afectada' => 'nullable|string|max:50',
            'motivo' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        $notaCredito->update($validated);

        return redirect()->route('compras.notas-credito.show', $notaCredito)
            ->with('success', 'Nota de crédito actualizada exitosamente');
    }

    public function aplicar(NotaCredito $notaCredito)
    {
        if ($notaCredito->estado !== 'borrador') {
            return redirect()->route('compras.notas-credito.show', $notaCredito)
                ->with('error', 'La nota de crédito ya ha sido procesada');
        }

        $notaCredito->aplicar();

        return redirect()->route('compras.notas-credito.show', $notaCredito)
            ->with('success', 'Nota de crédito aplicada exitosamente');
    }

    public function anular(NotaCredito $notaCredito)
    {
        if ($notaCredito->estado === 'anulada') {
            return redirect()->route('compras.notas-credito.show', $notaCredito)
                ->with('error', 'La nota de crédito ya está anulada');
        }

        $notaCredito->anular();

        return redirect()->route('compras.notas-credito.show', $notaCredito)
            ->with('success', 'Nota de crédito anulada exitosamente');
    }

    public function destroy(NotaCredito $notaCredito)
    {
        if ($notaCredito->estado !== 'borrador') {
            return redirect()->route('compras.notas-credito.index')
                ->with('error', 'Solo se pueden eliminar notas de crédito en estado borrador');
        }

        // Revertir el stock antes de eliminar
        $this->revertirStock($notaCredito);

        // Eliminar el registro de cuentas_por_pagar asociado
        CuentaPorPagar::where('numero_documento', $notaCredito->numero)
            ->where('tipo', 'NOTA_CREDITO')
            ->delete();

        $notaCredito->delete();

        return redirect()->route('compras.notas-credito.index')
            ->with('success', 'Nota de crédito eliminada exitosamente. Stock revertido.');
    }

    /**
     * Obtener compras por proveedor y fecha (AJAX)
     */
    public function getComprasByProveedorFecha(Request $request)
    {
        $proveedorId = $request->input('proveedor_id');
        $fecha = $request->input('fecha');

        if (!$proveedorId || !$fecha) {
            return response()->json([]);
        }

        $compras = Compra::where('proveedor_id', $proveedorId)
            ->where('estado', 'APROBADO')
            ->whereDate('fecha_emision', $fecha)
            ->orderBy('fecha_emision', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($compra) {
                return [
                    'id' => $compra->id,
                    'numero_factura' => $compra->numero_factura,
                    'timbrado' => $compra->timbrado,
                    'fecha_emision' => $compra->fecha_emision->format('d/m/Y'),
                    'total' => number_format($compra->total, 0, ',', '.'),
                    'tipo_factura' => $compra->tipo_factura,
                    'condicion_pago' => $compra->condicion_pago,
                ];
            });

        return response()->json($compras);
    }

    /**
     * Obtener detalles de una compra específica (AJAX)
     */
    public function getDetallesCompra($compraId)
    {
        $compra = Compra::with(['detalles.producto'])->find($compraId);

        if (!$compra) {
            return response()->json(['error' => 'Compra no encontrada'], 404);
        }

        $detalles = $compra->detalles->map(function ($detalle) {
            return [
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $detalle->producto ? $detalle->producto->nombre : 'Producto no encontrado',
                'descripcion' => $detalle->descripcion ?? ($detalle->producto ? $detalle->producto->nombre : ''),
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'descuento' => $detalle->descuento ?? 0,
                'iva_porcentaje' => $detalle->iva_porcentaje ?? 10,
            ];
        });

        return response()->json($detalles);
    }

    /**
     * Disminuir stock por devolución de mercadería (Nota de Crédito)
     */
    private function disminuirStock(NotaCredito $notaCredito)
    {
        $notaCredito->load('detalles', 'compra');

        // Obtener el depósito de la compra original
        $depositoId = $notaCredito->compra?->deposito_id ?? 1;

        foreach ($notaCredito->detalles as $detalle) {
            // Solo procesar si tiene producto_id
            if (!$detalle->producto_id) {
                continue;
            }

            // Buscar el registro de stock
            $stock = Stock::where('producto_id', $detalle->producto_id)
                ->where('deposito_id', $depositoId)
                ->first();

            if (!$stock) {
                // Si no existe stock, crear con cantidad negativa (esto indica inconsistencia)
                $stock = Stock::create([
                    'producto_id' => $detalle->producto_id,
                    'deposito_id' => $depositoId,
                    'stock_actual' => -$detalle->cantidad,
                    'stock_minimo' => 0,
                    'stock_maximo' => 0,
                    'creadoPor' => auth()->id(),
                ]);

                $stockAnterior = 0;
                $nuevoStock = -$detalle->cantidad;
            } else {
                // Stock existe, disminuir la cantidad
                $stockAnterior = $stock->stock_actual;
                $nuevoStock = $stockAnterior - $detalle->cantidad;

                $stock->update([
                    'stock_actual' => $nuevoStock,
                    'actualizadoPor' => auth()->id(),
                ]);
            }

            // Registrar movimiento en kardex
            MovimientoStock::create([
                'producto_id' => $detalle->producto_id,
                'deposito_id' => $depositoId,
                'usuario_id' => auth()->id(),
                'tipo' => 'SALIDA_DEVOLUCION_PROVEEDOR',
                'cantidad' => $detalle->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $nuevoStock,
                'costo_unitario' => $detalle->precio_unitario,
                'costo_total' => $detalle->precio_unitario * $detalle->cantidad,
                'documento_tipo' => 'NOTA_CREDITO',
                'documento_id' => $notaCredito->id,
                'motivo' => "Devolución a proveedor - Nota de Crédito #{$notaCredito->numero}",
                'fecha_movimiento' => $notaCredito->fecha ?? now(),
            ]);
        }
    }

    /**
     * Revertir stock al anular nota de crédito
     */
    private function revertirStock(NotaCredito $notaCredito)
    {
        $notaCredito->load('detalles', 'compra');

        // Obtener el depósito de la compra original
        $depositoId = $notaCredito->compra?->deposito_id ?? 1;

        foreach ($notaCredito->detalles as $detalle) {
            // Solo procesar si tiene producto_id
            if (!$detalle->producto_id) {
                continue;
            }

            // Buscar el registro de stock
            $stock = Stock::where('producto_id', $detalle->producto_id)
                ->where('deposito_id', $depositoId)
                ->first();

            if ($stock) {
                $stockAnterior = $stock->stock_actual;
                $nuevoStock = $stockAnterior + $detalle->cantidad; // Devolver al stock

                $stock->update([
                    'stock_actual' => $nuevoStock,
                    'actualizadoPor' => auth()->id(),
                ]);

                // Registrar movimiento de reversión en kardex
                MovimientoStock::create([
                    'producto_id' => $detalle->producto_id,
                    'deposito_id' => $depositoId,
                    'usuario_id' => auth()->id(),
                    'tipo' => 'ENTRADA_AJUSTE',
                    'cantidad' => $detalle->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_posterior' => $nuevoStock,
                    'costo_unitario' => $detalle->precio_unitario,
                    'costo_total' => $detalle->precio_unitario * $detalle->cantidad,
                    'documento_tipo' => 'NOTA_CREDITO_ANULADA',
                    'documento_id' => $notaCredito->id,
                    'motivo' => "Reversión por anulación de Nota de Crédito #{$notaCredito->numero}",
                    'fecha_movimiento' => now(),
                ]);
            }
        }
    }
}
