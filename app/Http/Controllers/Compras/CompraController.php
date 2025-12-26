<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\Compra;
use App\Models\Compras\AprobacionFlujo;
use App\Models\Compras\Proveedor;
use App\Models\Compras\CuentaPorPagar;
use App\Models\Empresa\Empresa;
use App\Models\Stock\Stock;
use App\Models\Stock\MovimientoStock;
use App\Models\Stock\Precio;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ComprasExport;

class CompraController extends Controller
{
    /**
     * Exportar lista de compras a PDF
     */
    public function exportarPDF()
    {
        $compras = Compra::with(['proveedor', 'creadoPorUsuario'])
            ->orderBy('fecha_emision', 'desc')
            ->get();

        $empresa = Empresa::first();

        $pdf = PDF::loadView('compras.compras.pdf', [
            'compras' => $compras,
            'empresa' => $empresa,
            'fecha' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('compras-facturas-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Exportar lista de compras a Excel
     */
    public function exportarExcel()
    {
        return Excel::download(
            new ComprasExport(),
            'compras-facturas-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Imprimir PDF individual de una compra
     */
    public function imprimirPDF($id)
    {
        $compra = Compra::with([
            'detalles',
            'proveedor',
            'ordenCompra',
            'ordenCompra.presupuesto',
            'ordenCompra.presupuesto.pedidoCompra',
            'ordenCompra.pedidoCompra',
            'creadoPorUsuario',
            'actualizadoPorUsuario'
        ])->findOrFail($id);

        $empresa = Empresa::first();

        $pdf = PDF::loadView('compras.compras.pdf-individual', [
            'compra' => $compra,
            'empresa' => $empresa,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('compra-' . $compra->numero_factura . '.pdf');
    }

    /**
     * Aprobar una compra
     */
    public function aprobar(Compra $compra)
    {
        // Validar que la compra esté pendiente
        if ($compra->estado !== 'PENDIENTE') {
            return back()->with('error', 'La compra no está pendiente de aprobación.');
        }

        // Verificar permisos
        if (!auth()->user()->can('compras.compras.aprobar')) {
            return back()->with('error', 'No tiene permiso para aprobar compras.');
        }

        DB::beginTransaction();

        try {
            // Actualizar estado
            $compra->update([
                'estado' => 'APROBADO',
                'actualizadoPor' => auth()->id(),
            ]);

            // Registrar aprobación en el flujo
            AprobacionFlujo::create([
                'documento_tipo' => 'COMPRA',
                'documento_id' => $compra->id,
                'estado' => 'APROBADO',
                'usuario_aprobador_id' => auth()->id(),
                'fecha_aprobacion' => now(),
                'comentarios' => 'Aprobado desde el controlador',
                'creadoPor' => auth()->id(),
            ]);

            // Crear Cuenta por Pagar (para CRÉDITO y CONTADO)
            $this->crearCuentaPorPagar($compra);

            // Actualizar Stock y Precios
            $this->actualizarStockYPrecios($compra);

            DB::commit();

            return back()->with('success', 'Compra aprobada correctamente. Stock y cuenta por pagar actualizados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Anular una compra
     */
    public function anular(Compra $compra)
    {
        // Validar que se pueda anular
        if ($compra->recepcion_completa) {
            return back()->with('error', 'No se puede anular una compra con recepción completa.');
        }

        // Actualizar estado
        $compra->update([
            'estado' => 'ANULADA',
            'actualizadoPor' => auth()->id(),
        ]);

        return back()->with('success', 'Compra anulada correctamente.');
    }

    /**
     * Duplicar una compra
     */
    public function duplicar(Compra $compra)
    {
        try {
            // Crear nueva compra como borrador
            $nuevaCompra = $compra->replicate();
            $nuevaCompra->estado = 'PENDIENTE';
            $nuevaCompra->numero_factura = $this->generarNumeroFactura();
            $nuevaCompra->fecha_emision = now();
            $nuevaCompra->fecha_vencimiento = now()->addDays(30);
            $nuevaCompra->creadoPor = auth()->id();
            $nuevaCompra->save();

            // Duplicar detalles
            foreach ($compra->detalles as $detalle) {
                $nuevoDetalle = $detalle->replicate();
                $nuevoDetalle->compra_id = $nuevaCompra->id;
                $nuevoDetalle->creadoPor = auth()->id();
                $nuevoDetalle->save();
            }

            return redirect()->route('compras.compras.edit', $nuevaCompra)
                ->with('success', 'Compra duplicada correctamente.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error al duplicar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Aprobar documento en flujo de aprobación
     */
    public function aprobarDocumento(AprobacionFlujo $aprobacion)
    {
        // Validar que la aprobación esté pendiente y sea del usuario actual
        if ($aprobacion->estado !== 'PENDIENTE') {
            return back()->with('error', 'Esta aprobación ya fue procesada.');
        }

        if ($aprobacion->usuario_aprobador_id !== auth()->id()) {
            return back()->with('error', 'No tiene permiso para aprobar este documento.');
        }

        // Aprobar
        $aprobacion->aprobar();

        // Actualizar estado del documento si todas las aprobaciones están completas
        $this->actualizarEstadoDocumento($aprobacion);

        return back()->with('success', 'Documento aprobado correctamente.');
    }

    /**
     * Rechazar documento en flujo de aprobación
     */
    public function rechazarDocumento(Request $request, AprobacionFlujo $aprobacion)
    {
        $request->validate([
            'observaciones' => 'required|string|min:10|max:500',
        ]);

        // Validar que la aprobación esté pendiente y sea del usuario actual
        if ($aprobacion->estado !== 'PENDIENTE') {
            return back()->with('error', 'Esta aprobación ya fue procesada.');
        }

        if ($aprobacion->usuario_aprobador_id !== auth()->id()) {
            return back()->with('error', 'No tiene permiso para rechazar este documento.');
        }

        // Rechazar
        $aprobacion->rechazar($request->observaciones);

        // Actualizar estado del documento a RECHAZADO
        $this->rechazarDocumentoCompleto($aprobacion);

        return back()->with('success', 'Documento rechazado correctamente.');
    }

    /**
     * Buscar compras para select2
     */
    public function buscarCompras(Request $request)
    {
        $search = $request->get('q');
        
        $compras = Compra::with('proveedor')
            ->when($search, function ($query, $search) {
                $query->where('numero_factura', 'like', "%{$search}%")
                    ->orWhereHas('proveedor', function ($q) use ($search) {
                        $q->where('razon_social', 'like', "%{$search}%")
                          ->orWhere('ruc', 'like', "%{$search}%");
                    });
            })
            ->where('estado', '!=', 'ANULADA')
            ->orderBy('fecha_emision', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($compra) {
                return [
                    'id' => $compra->id,
                    'text' => "{$compra->numero_factura} - " .
                             ($compra->proveedor->razon_social ?? 'N/A') . " - " .
                             "Gs. " . number_format($compra->total, 0, ',', '.') . " - " .
                             $compra->fecha_emision->format('d/m/Y'),
                    'proveedor' => $compra->proveedor->razon_social ?? 'N/A',
                    'total' => $compra->total,
                ];
            });

        return response()->json(['results' => $compras]);
    }

    /**
     * Buscar proveedores para select2
     */
    public function buscarProveedores(Request $request)
    {
        $search = $request->get('q');
        
        $proveedores = Proveedor::where('activo', true)
            ->when($search, function ($query, $search) {
                $query->where('razon_social', 'like', "%{$search}%")
                    ->orWhere('ruc', 'like', "%{$search}%");
            })
            ->orderBy('razon_social')
            ->limit(20)
            ->get()
            ->map(function ($proveedor) {
                return [
                    'id' => $proveedor->id,
                    'text' => "{$proveedor->razon_social} ({$proveedor->ruc})",
                ];
            });

        return response()->json(['results' => $proveedores]);
    }

    /**
     * Obtener detalles de una compra
     */
    public function getDetallesCompra(Compra $compra)
    {
        $detalles = $compra->detalles()
            ->with('producto')
            ->get()
            ->map(function ($detalle) {
                return [
                    'id' => $detalle->id,
                    'producto_id' => $detalle->producto_id,
                    'producto_nombre' => $detalle->producto->nombre ?? 'N/A',
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'iva_porcentaje' => $detalle->iva_porcentaje,
                    'total' => $detalle->total,
                ];
            });

        return response()->json([
            'compra' => [
                'id' => $compra->id,
                'numero_factura' => $compra->numero_factura,
                'proveedor' => $compra->proveedor->razon_social ?? 'N/A',
                'total' => $compra->total,
            ],
            'detalles' => $detalles,
        ]);
    }

    /**
     * Obtener estadísticas para dashboard
     */
    public function getEstadisticasDashboard(Request $request)
    {
        $periodo = $request->get('periodo', 'mes');
        
        switch ($periodo) {
            case 'mes':
                $fechaInicio = now()->startOfMonth();
                $fechaFin = now()->endOfMonth();
                break;
            case 'trimestre':
                $fechaInicio = now()->startOfQuarter();
                $fechaFin = now()->endOfQuarter();
                break;
            case 'año':
                $fechaInicio = now()->startOfYear();
                $fechaFin = now()->endOfYear();
                break;
            default:
                $fechaInicio = now()->startOfMonth();
                $fechaFin = now()->endOfMonth();
        }

        $compras = Compra::whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->where('estado', 'APROBADO')
            ->get();

        $estadisticas = [
            'totalCompras' => $compras->count(),
            'totalMonto' => $compras->sum('total'),
            'comprasCredito' => $compras->where('tipo_factura', 'CREDITO')->sum('total'),
            'comprasContado' => $compras->where('tipo_factura', 'CONTADO')->sum('total'),
            'comprasPorMes' => $this->getComprasPorMes($fechaInicio, $fechaFin),
            'topProveedores' => $this->getTopProveedores($fechaInicio, $fechaFin),
        ];

        return response()->json($estadisticas);
    }

    // ==================== MÉTODOS PRIVADOS ====================

    private function generarNumeroFactura(): string
    {
        $ultimaCompra = Compra::where('numero_factura', 'like', 'DUP-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimaCompra) {
            $numero = (int) str_replace('DUP-', '', $ultimaCompra->numero_factura);
            return 'DUP-' . str_pad($numero + 1, 6, '0', STR_PAD_LEFT);
        }

        return 'DUP-000001';
    }

    private function actualizarEstadoDocumento(AprobacionFlujo $aprobacion)
    {
        // Obtener todas las aprobaciones pendientes para este documento
        $aprobacionesPendientes = AprobacionFlujo::where('documento_tipo', $aprobacion->documento_tipo)
            ->where('documento_id', $aprobacion->documento_id)
            ->where('estado', 'PENDIENTE')
            ->count();

        // Si no hay aprobaciones pendientes, marcar documento como APROBADO
        if ($aprobacionesPendientes === 0) {
            $modelClass = $this->getModelClass($aprobacion->documento_tipo);
            if ($modelClass) {
                $modelClass::find($aprobacion->documento_id)
                    ->update(['estado' => 'APROBADO']);
            }
        }
    }

    private function rechazarDocumentoCompleto(AprobacionFlujo $aprobacion)
    {
        $modelClass = $this->getModelClass($aprobacion->documento_tipo);
        if ($modelClass) {
            $modelClass::find($aprobacion->documento_id)
                ->update(['estado' => 'RECHAZADO']);
        }
    }

    private function getModelClass(string $documentoTipo): ?string
    {
        return match($documentoTipo) {
            'COMPRA' => Compra::class,
            'PEDIDO_COMPRA' => 'App\Models\Pedidos\PedidoCompra',
            'ORDEN_COMPRA' => 'App\Models\Ordenes\OrdenCompra',
            default => null,
        };
    }

    private function getComprasPorMes(Carbon $fechaInicio, Carbon $fechaFin): array
    {
        $compras = Compra::whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->where('estado', 'APROBADO')
            ->selectRaw("DATE_TRUNC('month', fecha_emision) as mes, SUM(total) as total")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return $compras->map(function ($item) {
            return [
                'mes' => Carbon::parse($item->mes)->format('M Y'),
                'total' => (float) $item->total
            ];
        })->toArray();
    }

    private function getTopProveedores(Carbon $fechaInicio, Carbon $fechaFin, int $limit = 5): array
    {
        $compras = Compra::whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->where('estado', 'APROBADO')
            ->with('proveedor')
            ->get()
            ->groupBy('proveedor_id')
            ->map(function ($compras, $proveedorId) {
                return [
                    'proveedor' => $compras->first()->proveedor->razon_social ?? 'N/A',
                    'total' => $compras->sum('total'),
                    'cantidad' => $compras->count()
                ];
            })
            ->sortByDesc('total')
            ->take($limit)
            ->values()
            ->toArray();

        return $compras;
    }

    /**
     * Crea cuenta por pagar al aprobar la compra
     */
    private function crearCuentaPorPagar(Compra $compra)
    {
        // Verificar si ya existe
        $cuentaExistente = CuentaPorPagar::where('compra_id', $compra->id)->first();
        if ($cuentaExistente) {
            return;
        }

        // Calcular fecha de vencimiento
        $fechaVencimiento = $compra->fecha_vencimiento;

        if (!$fechaVencimiento && $compra->tipo_factura === 'CREDITO') {
            $diasVencimiento = match($compra->condicion_pago) {
                '7_DIAS' => 7,
                '15_DIAS' => 15,
                '30_DIAS' => 30,
                '60_DIAS' => 60,
                '90_DIAS' => 90,
                default => 30,
            };
            $fechaVencimiento = $compra->fecha_emision->addDays($diasVencimiento);
        } else if (!$fechaVencimiento) {
            // Si es CONTADO y no tiene fecha, usar fecha de emisión
            $fechaVencimiento = $compra->fecha_emision;
        }

        CuentaPorPagar::create([
            'compra_id' => $compra->id,
            'proveedor_id' => $compra->proveedor_id,
            'numero_documento' => $compra->numero_factura,
            'timbrado' => $compra->timbrado,
            'tipo' => $compra->tipo_factura,
            'fecha_emision' => $compra->fecha_emision,
            'fecha_vencimiento' => $fechaVencimiento,
            'condicion_pago' => $compra->condicion_pago,
            'monto_total' => $compra->total,
            'monto_pagado' => 0,
            'saldo_pendiente' => $compra->total,
            'moneda' => 'PYG',
            'estado' => 'PENDIENTE',
            'observaciones' => 'Generada automáticamente al aprobar factura ' . $compra->numero_factura,
            'creadoPor' => auth()->id(),
        ]);
    }

    /**
     * Actualiza stock y precios al aprobar la compra
     */
    private function actualizarStockYPrecios(Compra $compra)
    {
        $compra->load('detalles');

        foreach ($compra->detalles as $detalle) {
            // Obtener o crear registro de stock
            $stock = Stock::firstOrCreate(
                [
                    'producto_id' => $detalle->producto_id,
                    'deposito_id' => $compra->deposito_id ?? 1,
                ],
                [
                    'stock_actual' => 0,
                    'stock_minimo' => 0,
                    'stock_maximo' => 0,
                    'creadoPor' => auth()->id(),
                ]
            );

            $stockAnterior = $stock->stock_actual;
            $nuevoStock = $stockAnterior + $detalle->cantidad;

            // Actualizar stock
            $stock->update([
                'stock_actual' => $nuevoStock,
                'actualizadoPor' => auth()->id(),
            ]);

            // Registrar movimiento en kardex
            MovimientoStock::create([
                'producto_id' => $detalle->producto_id,
                'deposito_id' => $compra->deposito_id ?? 1,
                'usuario_id' => auth()->id(),
                'tipo' => 'ENTRADA_COMPRA',
                'cantidad' => $detalle->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $nuevoStock,
                'costo_unitario' => $detalle->precio_unitario,
                'costo_total' => $detalle->precio_unitario * $detalle->cantidad,
                'documento_tipo' => 'COMPRA',
                'documento_id' => $compra->id,
                'motivo' => "Entrada por compra #{$compra->numero_factura}",
                'fecha_movimiento' => $compra->fecha_emision ?? now(),
            ]);

            // Actualizar precio actual
            $precioActual = Precio::where('producto_id', $detalle->producto_id)
                ->where('es_actual', true)
                ->first();

            if ($precioActual) {
                $precioActual->update(['es_actual' => false]);
            }

            // Convertir IVA
            $iva = match((int)($detalle->iva_porcentaje ?? 10)) {
                10 => '10',
                5 => '5',
                0 => 'exenta',
                default => '10',
            };

            // Crear nuevo precio actual
            Precio::create([
                'producto_id' => $detalle->producto_id,
                'precio_compra' => $detalle->precio_unitario,
                'precio_venta' => $precioActual ? $precioActual->precio_venta : ($detalle->precio_unitario * 1.3),
                'margen_porcentaje' => $precioActual ? $precioActual->margen_porcentaje : 30,
                'iva' => $iva,
                'moneda' => 'PYG',
                'tipo_cambio' => 1,
                'es_actual' => true,
                'creadoPor' => auth()->id(),
            ]);
        }
    }
}
