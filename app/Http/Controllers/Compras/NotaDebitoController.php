<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\NotaDebito;
use App\Models\Compras\Proveedor;
use App\Models\Compras\Compra;
use App\Models\Compras\CuentaPorPagar;
use App\Models\Stock\Producto;
use Illuminate\Http\Request;

class NotaDebitoController extends Controller
{
    public function index()
    {
        $notasDebito = NotaDebito::with(['proveedor', 'empresa', 'creador'])
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        return view('compras.notas-debito.index', compact('notasDebito'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->get();
        $compras = Compra::where('estado', 'APROBADO')->get();
        $productos = Producto::where('activo', true)->get();

        // Generar número automático para la nota de débito
        $ultimaNotaDebito = NotaDebito::orderBy('id', 'desc')->first();
        $numeroSecuencial = $ultimaNotaDebito ? (intval(substr($ultimaNotaDebito->numero, -5)) + 1) : 1;
        $numeroAutomatico = 'ND-001-001-' . str_pad($numeroSecuencial, 5, '0', STR_PAD_LEFT);

        return view('compras.notas-debito.create', compact('proveedores', 'compras', 'productos', 'numeroAutomatico'));
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
                    $exists = \DB::table('compras.notas_debito')->where('numero', $value)->exists();
                    if ($exists) {
                        $fail('El número de nota de débito ya existe.');
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

        $notaDebito = NotaDebito::create([
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
            $notaDebito->detalles()->create([
                'producto_id' => $detalle['producto_id'] ?? null,
                'descripcion' => $detalle['descripcion'],
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
                'descuento' => $detalle['descuento'] ?? 0,
            ]);
        }

        foreach ($notaDebito->detalles as $detalle) {
            $detalle->calcularTotales();
        }

        $notaDebito->recalcularTotales();

        // Crear registro en cuentas_por_pagar con monto POSITIVO (aumenta la deuda)
        CuentaPorPagar::create([
            'compra_id' => $notaDebito->compra_id,
            'proveedor_id' => $notaDebito->proveedor_id,
            'numero_documento' => $notaDebito->numero,
            'timbrado' => null,
            'tipo' => 'NOTA_DEBITO',
            'fecha_emision' => $notaDebito->fecha,
            'fecha_vencimiento' => $notaDebito->fecha,
            'condicion_pago' => 'CONTADO',
            'monto_total' => abs($notaDebito->total), // POSITIVO para aumentar deuda
            'monto_pagado' => 0,
            'saldo_pendiente' => abs($notaDebito->total), // POSITIVO para aumentar saldo
            'moneda' => 'PYG',
            'estado' => 'APLICADA',
            'observaciones' => "Nota de Débito #{$notaDebito->numero} - {$notaDebito->motivo}",
            'creadoPor' => auth()->id(),
        ]);

        return redirect()->route('compras.notas-debito.show', $notaDebito)
            ->with('success', 'Nota de débito creada exitosamente');
    }

    public function show(NotaDebito $notaDebito)
    {
        $notaDebito->load(['proveedor', 'empresa', 'compra', 'detalles.producto', 'creador']);

        return view('compras.notas-debito.show', compact('notaDebito'));
    }

    public function edit(NotaDebito $notaDebito)
    {
        if ($notaDebito->estado !== 'borrador') {
            return redirect()->route('compras.notas-debito.show', $notaDebito)
                ->with('error', 'Solo se pueden editar notas de débito en estado borrador');
        }

        $proveedores = Proveedor::where('activo', true)->get();
        $compras = Compra::where('estado', 'APROBADO')->get();
        $productos = Producto::where('activo', true)->get();

        return view('compras.notas-debito.edit', compact('notaDebito', 'proveedores', 'compras', 'productos'));
    }

    public function update(Request $request, NotaDebito $notaDebito)
    {
        if ($notaDebito->estado !== 'borrador') {
            return redirect()->route('compras.notas-debito.show', $notaDebito)
                ->with('error', 'Solo se pueden editar notas de débito en estado borrador');
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'numero_factura_afectada' => 'nullable|string|max:50',
            'motivo' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        $notaDebito->update($validated);

        return redirect()->route('compras.notas-debito.show', $notaDebito)
            ->with('success', 'Nota de débito actualizada exitosamente');
    }

    public function aplicar(NotaDebito $notaDebito)
    {
        if ($notaDebito->estado !== 'borrador') {
            return redirect()->route('compras.notas-debito.show', $notaDebito)
                ->with('error', 'La nota de débito ya ha sido procesada');
        }

        $notaDebito->aplicar();

        return redirect()->route('compras.notas-debito.show', $notaDebito)
            ->with('success', 'Nota de débito aplicada exitosamente');
    }

    public function anular(NotaDebito $notaDebito)
    {
        if ($notaDebito->estado === 'anulada') {
            return redirect()->route('compras.notas-debito.show', $notaDebito)
                ->with('error', 'La nota de débito ya está anulada');
        }

        $notaDebito->anular();

        return redirect()->route('compras.notas-debito.show', $notaDebito)
            ->with('success', 'Nota de débito anulada exitosamente');
    }

    public function destroy(NotaDebito $notaDebito)
    {
        if ($notaDebito->estado !== 'borrador') {
            return redirect()->route('compras.notas-debito.index')
                ->with('error', 'Solo se pueden eliminar notas de débito en estado borrador');
        }

        $notaDebito->delete();

        return redirect()->route('compras.notas-debito.index')
            ->with('success', 'Nota de débito eliminada exitosamente');
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
}
