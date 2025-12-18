<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\PedidoCompra;
use App\Models\Empresa\Empresa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PedidosCompraExport;

class PedidoCompraController extends Controller
{
    public function index()
    {
        return view('compras.pedidos.index');
    }

    public function create()
    {
        return view('compras.pedidos.create');
    }

    public function edit($id)
    {
        $pedido = PedidoCompra::with('detalles')->findOrFail($id);
        return view('compras.pedidos.edit', compact('pedido'));
    }

    public function show($id)
    {
        $pedido = PedidoCompra::with([
            'detalles',
            'usuarioSolicitante',
            'presupuestos',
            'ordenesCompra'
        ])->findOrFail($id);

        return view('compras.pedidos.show', compact('pedido'));
    }

    public function imprimirPDF($id)
    {
        $pedido = PedidoCompra::with([
            'detalles',
            'usuarioSolicitante',
            'creadoPorUsuario',
            'aprobadoPorUsuario'
        ])->findOrFail($id);

        $empresa = Empresa::first();

        $pdf = PDF::loadView('compras.pedidos.pdf-individual', [
            'pedido' => $pedido,
            'empresa' => $empresa,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('pedido-compra-' . $pedido->numero_pedido . '.pdf');
    }

    public function aprobar($id)
    {
        $pedido = PedidoCompra::findOrFail($id);
        $pedido->aprobar(auth()->id());

        return redirect()
            ->route('compras.pedidos.show', $id)
            ->with('success', 'Pedido aprobado correctamente');
    }

    public function rechazar($id)
    {
        $pedido = PedidoCompra::findOrFail($id);
        $pedido->rechazar();

        return redirect()
            ->route('compras.pedidos.index')
            ->with('success', 'Pedido rechazado');
    }

    public function exportarPDF()
    {
        $pedidos = PedidoCompra::with(['usuarioSolicitante'])
            ->orderBy('created_at', 'desc')
            ->get();

        $empresa = Empresa::first();

        $pdf = PDF::loadView('compras.pedidos.pdf', [
            'pedidos' => $pedidos,
            'empresa' => $empresa,
            'fecha' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('pedidos-compra-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportarExcel()
    {
        return Excel::download(
            new PedidosCompraExport(),
            'pedidos-compra-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
