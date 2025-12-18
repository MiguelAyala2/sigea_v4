<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\Presupuesto;
use App\Models\Compras\PedidoCompra;
use App\Models\Empresa\Empresa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresupuestosExport;

class PresupuestoController extends Controller
{
    public function index()
    {
        return view('compras.presupuestos.index');
    }

    public function exportarPDF()
    {
        $presupuestos = Presupuesto::with(['proveedor', 'pedidoCompra', 'solicitadoPorUsuario'])
            ->orderBy('fecha_solicitud', 'desc')
            ->get();

        $empresa = Empresa::first();

        $pdf = PDF::loadView('compras.presupuestos.pdf', [
            'presupuestos' => $presupuestos,
            'empresa' => $empresa,
            'fecha' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('presupuestos-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportarExcel()
    {
        return Excel::download(
            new PresupuestosExport(),
            'presupuestos-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function create(Request $request)
    {
        $pedidoId = $request->get('pedido_compra_id');
        $pedido = null;

        if ($pedidoId) {
            $pedido = PedidoCompra::with('detalles')->findOrFail($pedidoId);
        }

        return view('compras.presupuestos.create', compact('pedido'));
    }

    public function edit($id)
    {
        $presupuesto = Presupuesto::with('detalles')->findOrFail($id);
        return view('compras.presupuestos.edit', compact('presupuesto'));
    }

    public function show($id)
    {
        $presupuesto = Presupuesto::with([
            'detalles',
            'proveedor',
            'pedidoCompra'
        ])->findOrFail($id);

        return view('compras.presupuestos.show', compact('presupuesto'));
    }

    public function imprimirPDF($id)
    {
        $presupuesto = Presupuesto::with([
            'detalles',
            'proveedor',
            'pedidoCompra',
            'solicitadoPorUsuario',
            'evaluadoPorUsuario'
        ])->findOrFail($id);

        $empresa = Empresa::first();

        $pdf = PDF::loadView('compras.presupuestos.pdf-individual', [
            'presupuesto' => $presupuesto,
            'empresa' => $empresa,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('presupuesto-' . $presupuesto->numero_presupuesto . '.pdf');
    }

    public function comparar(Request $request)
    {
        $pedidoId = $request->get('pedido_compra_id');

        if (!$pedidoId) {
            return redirect()
                ->route('compras.presupuestos.index')
                ->with('error', 'Debe seleccionar un pedido de compra');
        }

        $pedido = PedidoCompra::with('detalles')->findOrFail($pedidoId);
        $presupuestos = Presupuesto::where('pedido_compra_id', $pedidoId)
            ->with(['detalles', 'proveedor'])
            ->get();

        return view('compras.presupuestos.comparar', compact('pedido', 'presupuestos'));
    }

    public function seleccionar(Request $request, $id)
    {
        $presupuesto = Presupuesto::findOrFail($id);

        // Verificar si se está rechazando el presupuesto
        if ($request->has('rechazar') && $request->rechazar == '1') {
            $presupuesto->estado = 'RECHAZADO';
            $presupuesto->save();

            return redirect()
                ->route('compras.presupuestos.show', $id)
                ->with('success', 'Presupuesto rechazado correctamente');
        }

        // Si no, se marca como seleccionado (aprobado)
        $presupuesto->marcarComoSeleccionado();

        return redirect()
            ->route('compras.presupuestos.show', $id)
            ->with('success', 'Presupuesto seleccionado correctamente');
    }
}
