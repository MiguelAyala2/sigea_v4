<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\CuentaPorPagar;
use App\Models\Compras\Compra;
use App\Models\Empresa\Empresa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CuentasPorPagarExport;

class PagoController extends Controller
{
    public function index()
    {
        return view('compras.pagos.index');
    }

    public function show($id)
    {
        $cuenta = CuentaPorPagar::with(['compra', 'proveedor'])->findOrFail($id);
        return view('compras.pagos.show', compact('cuenta'));
    }

    /**
     * Exportar cuentas por pagar a PDF
     */
    public function exportarPDF()
    {
        $compras = Compra::with(['proveedor', 'creadoPorUsuario'])
            ->where('estado', 'APROBADO')
            ->orderBy('fecha_emision', 'desc')
            ->get();

        $empresa = Empresa::first();

        // Calcular totales
        $totalGeneral = $compras->sum('total');
        $totalSubtotal = $compras->sum('subtotal');
        $totalIVA = $compras->sum('iva_10') + $compras->sum('iva_5');

        $pdf = PDF::loadView('compras.pagos.pdf', [
            'compras' => $compras,
            'empresa' => $empresa,
            'totalGeneral' => $totalGeneral,
            'totalSubtotal' => $totalSubtotal,
            'totalIVA' => $totalIVA,
            'fecha' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('cuentas-por-pagar-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Exportar cuentas por pagar a Excel
     */
    public function exportarExcel()
    {
        return Excel::download(
            new CuentasPorPagarExport(),
            'cuentas-por-pagar-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
