<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\Compra;
use App\Models\Compras\NotaCredito;
use App\Models\Compras\NotaDebito;
use App\Models\Compras\Proveedor;
use App\Models\Empresa\Empresa;
use App\Exports\LibroComprasExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LibroComprasController extends Controller
{
    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        $proveedorId = $request->input('proveedor_id');

        // Obtener compras
        $compras = Compra::with(['proveedor'])
            ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->when($proveedorId, function ($query) use ($proveedorId) {
                return $query->where('proveedor_id', $proveedorId);
            })
            ->where('estado', 'APROBADO')
            ->get()
            ->map(function ($compra) {
                return [
                    'tipo' => 'Factura',
                    'numero' => $compra->numero_factura,
                    'fecha' => $compra->fecha_emision,
                    'proveedor' => $compra->proveedor->razon_social ?? $compra->proveedor->nombre_fantasia,
                    'ruc' => $compra->proveedor->ruc,
                    'subtotal' => $compra->subtotal,
                    'impuesto' => ($compra->iva_10 ?? 0) + ($compra->iva_5 ?? 0),
                    'total' => $compra->total,
                    'estado' => $compra->estado,
                ];
            });

        // Obtener notas de crédito
        $notasCredito = NotaCredito::with(['proveedor'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->when($proveedorId, function ($query) use ($proveedorId) {
                return $query->where('proveedor_id', $proveedorId);
            })
            ->where('estado', 'aplicada')
            ->get()
            ->map(function ($nota) {
                return [
                    'tipo' => 'Nota Crédito',
                    'numero' => $nota->numero,
                    'fecha' => $nota->fecha,
                    'proveedor' => $nota->proveedor->razon_social ?? $nota->proveedor->nombre_fantasia,
                    'ruc' => $nota->proveedor->ruc,
                    'subtotal' => -$nota->subtotal, // Negativo porque disminuye el total
                    'impuesto' => -$nota->impuesto,
                    'total' => -$nota->total,
                    'estado' => $nota->estado,
                ];
            });

        // Obtener notas de débito
        $notasDebito = NotaDebito::with(['proveedor'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->when($proveedorId, function ($query) use ($proveedorId) {
                return $query->where('proveedor_id', $proveedorId);
            })
            ->where('estado', 'aplicada')
            ->get()
            ->map(function ($nota) {
                return [
                    'tipo' => 'Nota Débito',
                    'numero' => $nota->numero,
                    'fecha' => $nota->fecha,
                    'proveedor' => $nota->proveedor->razon_social ?? $nota->proveedor->nombre_fantasia,
                    'ruc' => $nota->proveedor->ruc,
                    'subtotal' => $nota->subtotal,
                    'impuesto' => $nota->impuesto,
                    'total' => $nota->total,
                    'estado' => $nota->estado,
                ];
            });

        // Combinar todas las transacciones
        $transacciones = $compras->concat($notasCredito)->concat($notasDebito)
            ->sortBy('fecha')
            ->values();

        // Calcular totales
        $totales = [
            'subtotal' => $transacciones->sum('subtotal'),
            'impuesto' => $transacciones->sum('impuesto'),
            'total' => $transacciones->sum('total'),
        ];

        return view('compras.reportes.libro-compras', compact('transacciones', 'totales', 'fechaInicio', 'fechaFin'));
    }

    public function exportarPDF(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        $proveedorId = $request->input('proveedor_id');

        // Obtener compras
        $compras = Compra::with(['proveedor'])
            ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->when($proveedorId, function ($query) use ($proveedorId) {
                return $query->where('proveedor_id', $proveedorId);
            })
            ->where('estado', 'APROBADO')
            ->get()
            ->map(function ($compra) {
                return [
                    'tipo' => 'Factura',
                    'numero' => $compra->numero_factura,
                    'fecha' => $compra->fecha_emision,
                    'proveedor' => $compra->proveedor->razon_social ?? $compra->proveedor->nombre_fantasia,
                    'ruc' => $compra->proveedor->ruc,
                    'subtotal' => $compra->subtotal,
                    'impuesto' => ($compra->iva_10 ?? 0) + ($compra->iva_5 ?? 0),
                    'total' => $compra->total,
                    'estado' => $compra->estado,
                ];
            });

        // Obtener notas de crédito
        $notasCredito = NotaCredito::with(['proveedor'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->when($proveedorId, function ($query) use ($proveedorId) {
                return $query->where('proveedor_id', $proveedorId);
            })
            ->where('estado', 'aplicada')
            ->get()
            ->map(function ($nota) {
                return [
                    'tipo' => 'Nota Crédito',
                    'numero' => $nota->numero,
                    'fecha' => $nota->fecha,
                    'proveedor' => $nota->proveedor->razon_social ?? $nota->proveedor->nombre_fantasia,
                    'ruc' => $nota->proveedor->ruc,
                    'subtotal' => -$nota->subtotal,
                    'impuesto' => -$nota->impuesto,
                    'total' => -$nota->total,
                    'estado' => $nota->estado,
                ];
            });

        // Obtener notas de débito
        $notasDebito = NotaDebito::with(['proveedor'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->when($proveedorId, function ($query) use ($proveedorId) {
                return $query->where('proveedor_id', $proveedorId);
            })
            ->where('estado', 'aplicada')
            ->get()
            ->map(function ($nota) {
                return [
                    'tipo' => 'Nota Débito',
                    'numero' => $nota->numero,
                    'fecha' => $nota->fecha,
                    'proveedor' => $nota->proveedor->razon_social ?? $nota->proveedor->nombre_fantasia,
                    'ruc' => $nota->proveedor->ruc,
                    'subtotal' => $nota->subtotal,
                    'impuesto' => $nota->impuesto,
                    'total' => $nota->total,
                    'estado' => $nota->estado,
                ];
            });

        // Combinar todas las transacciones
        $transacciones = $compras->concat($notasCredito)->concat($notasDebito)
            ->sortBy('fecha')
            ->values();

        // Calcular totales
        $totales = [
            'subtotal' => $transacciones->sum('subtotal'),
            'impuesto' => $transacciones->sum('impuesto'),
            'total' => $transacciones->sum('total'),
        ];

        // Obtener nombre del proveedor si se filtró
        $proveedorNombre = null;
        if ($proveedorId) {
            $proveedor = Proveedor::find($proveedorId);
            $proveedorNombre = $proveedor ? ($proveedor->razon_social ?? $proveedor->nombre_fantasia) : null;
        }

        $empresa = Empresa::first();

        $pdf = PDF::loadView('compras.reportes.libro-compras-pdf', [
            'transacciones' => $transacciones,
            'totales' => $totales,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'proveedorNombre' => $proveedorNombre,
            'empresa' => $empresa,
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('libro-compras-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportarExcel(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        $proveedorId = $request->input('proveedor_id');

        // Obtener compras
        $compras = Compra::with(['proveedor'])
            ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->when($proveedorId, function ($query) use ($proveedorId) {
                return $query->where('proveedor_id', $proveedorId);
            })
            ->where('estado', 'APROBADO')
            ->get()
            ->map(function ($compra) {
                return [
                    'tipo' => 'Factura',
                    'numero' => $compra->numero_factura,
                    'fecha' => $compra->fecha_emision,
                    'proveedor' => $compra->proveedor->razon_social ?? $compra->proveedor->nombre_fantasia,
                    'ruc' => $compra->proveedor->ruc,
                    'subtotal' => $compra->subtotal,
                    'impuesto' => ($compra->iva_10 ?? 0) + ($compra->iva_5 ?? 0),
                    'total' => $compra->total,
                    'estado' => $compra->estado,
                ];
            });

        // Obtener notas de crédito
        $notasCredito = NotaCredito::with(['proveedor'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->when($proveedorId, function ($query) use ($proveedorId) {
                return $query->where('proveedor_id', $proveedorId);
            })
            ->where('estado', 'aplicada')
            ->get()
            ->map(function ($nota) {
                return [
                    'tipo' => 'Nota Crédito',
                    'numero' => $nota->numero,
                    'fecha' => $nota->fecha,
                    'proveedor' => $nota->proveedor->razon_social ?? $nota->proveedor->nombre_fantasia,
                    'ruc' => $nota->proveedor->ruc,
                    'subtotal' => -$nota->subtotal,
                    'impuesto' => -$nota->impuesto,
                    'total' => -$nota->total,
                    'estado' => $nota->estado,
                ];
            });

        // Obtener notas de débito
        $notasDebito = NotaDebito::with(['proveedor'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->when($proveedorId, function ($query) use ($proveedorId) {
                return $query->where('proveedor_id', $proveedorId);
            })
            ->where('estado', 'aplicada')
            ->get()
            ->map(function ($nota) {
                return [
                    'tipo' => 'Nota Débito',
                    'numero' => $nota->numero,
                    'fecha' => $nota->fecha,
                    'proveedor' => $nota->proveedor->razon_social ?? $nota->proveedor->nombre_fantasia,
                    'ruc' => $nota->proveedor->ruc,
                    'subtotal' => $nota->subtotal,
                    'impuesto' => $nota->impuesto,
                    'total' => $nota->total,
                    'estado' => $nota->estado,
                ];
            });

        // Combinar todas las transacciones
        $transacciones = $compras->concat($notasCredito)->concat($notasDebito)
            ->sortBy('fecha')
            ->values();

        // Calcular totales
        $totales = [
            'subtotal' => $transacciones->sum('subtotal'),
            'impuesto' => $transacciones->sum('impuesto'),
            'total' => $transacciones->sum('total'),
        ];

        return Excel::download(
            new LibroComprasExport($transacciones, $totales, $fechaInicio, $fechaFin),
            'libro-compras-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
