<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\OrdenCompra;
use App\Models\Compras\Presupuesto;
use App\Models\Compras\PedidoCompra;
use App\Models\Empresa\Empresa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdenesCompraExport;

class OrdenCompraController extends Controller
{
    public function index()
    {
        return view('compras.ordenes.index');
    }

    public function exportarPDF()
    {
        $ordenes = OrdenCompra::with(['proveedor', 'pedidoCompra', 'creador'])
            ->orderBy('fecha_orden', 'desc')
            ->get();

        $empresa = Empresa::first();

        $pdf = PDF::loadView('compras.ordenes.pdf', [
            'ordenes' => $ordenes,
            'empresa' => $empresa,
            'fecha' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('ordenes-compra-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportarExcel()
    {
        return Excel::download(
            new OrdenesCompraExport(),
            'ordenes-compra-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function create(Request $request)
    {
        $presupuestoId = $request->get('presupuesto_id');
        $pedidoId = $request->get('pedido_compra_id');

        $presupuesto = null;
        $pedido = null;

        if ($presupuestoId) {
            $presupuesto = Presupuesto::with(['detalles', 'proveedor'])->findOrFail($presupuestoId);
        }

        if ($pedidoId) {
            $pedido = PedidoCompra::with('detalles')->findOrFail($pedidoId);
        }

        return view('compras.ordenes.create', compact('presupuesto', 'pedido'));
    }

    public function edit($id)
    {
        $orden = OrdenCompra::with('detalles')->findOrFail($id);
        return view('compras.ordenes.edit', compact('orden'));
    }

    public function show($id)
    {
        $orden = OrdenCompra::with([
            'detalles',
            'proveedor',
            'presupuesto',
            'pedidoCompra',
            'creador',
            'actualizador'
        ])->findOrFail($id);

        return view('compras.ordenes.show', compact('orden'));
    }

    public function imprimirPDF($id)
    {
        $orden = OrdenCompra::with([
            'detalles',
            'proveedor',
            'presupuesto',
            'pedidoCompra',
            'creadoPorUsuario',
            'aprobadoPorUsuario',
            'confirmadaPorUsuario'
        ])->findOrFail($id);

        $empresa = Empresa::first();

        $pdf = PDF::loadView('compras.ordenes.pdf-individual', [
            'orden' => $orden,
            'empresa' => $empresa,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('orden-compra-' . $orden->numero_orden . '.pdf');
    }

    public function emitir($id)
    {
        $orden = OrdenCompra::findOrFail($id);
        $orden->emitir();

        return redirect()
            ->route('compras.ordenes.show', $id)
            ->with('success', 'Orden de compra emitida correctamente');
    }

    public function enviar($id)
    {
        $orden = OrdenCompra::findOrFail($id);
        $orden->enviar();

        return redirect()
            ->route('compras.ordenes.show', $id)
            ->with('success', 'Orden de compra enviada al proveedor');
    }

    public function confirmar($id)
    {
        $orden = OrdenCompra::findOrFail($id);
        $orden->confirmar(auth()->id());

        return redirect()
            ->route('compras.ordenes.show', $id)
            ->with('success', 'Orden de compra confirmada');
    }

    public function cancelar($id)
    {
        $orden = OrdenCompra::findOrFail($id);
        $orden->cancelar();

        return redirect()
            ->route('compras.ordenes.index')
            ->with('success', 'Orden de compra cancelada');
    }

    public function imprimir($id)
    {
        $orden = OrdenCompra::with([
            'detalles',
            'proveedor',
            'creador'
        ])->findOrFail($id);

        return view('compras.ordenes.imprimir', compact('orden'));
    }
}
