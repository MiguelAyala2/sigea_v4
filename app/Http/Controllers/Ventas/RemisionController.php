<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\Remision;
use App\Models\Servicios\Cliente;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RemisionController extends Controller
{
    public function index()
    {
        $remisiones = Remision::with(['cliente', 'factura', 'sucursal'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ventas.remisiones.index', compact('remisiones'));
    }

    public function crear()
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();

        return view('ventas.remisiones.crear', compact('clientes'));
    }

    public function show(Remision $remision)
    {
        $remision->load(['cliente', 'factura', 'detalles.producto', 'sucursal', 'deposito', 'responsable']);

        return view('ventas.remisiones.show', compact('remision'));
    }

    public function pdf(Remision $remision)
    {
        $remision->load(['cliente', 'factura', 'detalles.producto', 'sucursal', 'deposito', 'responsable', 'emitidoPor']);

        $pdf = Pdf::loadView('ventas.remisiones.pdf', compact('remision'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('remision-' . $remision->numero_remision . '.pdf');
    }

    public function emitir(Remision $remision)
    {
        try {
            $remision->emitir();
            return redirect()->route('ventas.remisiones.show', $remision)
                ->with('success', 'Remisión emitida correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al emitir la remisión: ' . $e->getMessage());
        }
    }

    public function marcarEntregada(Request $request, Remision $remision)
    {
        $request->validate([
            'receptor_nombre' => 'required|string|max:255',
            'receptor_ci' => 'required|string|max:20',
        ]);

        try {
            $remision->marcarEntregada($request->receptor_nombre, $request->receptor_ci);
            return redirect()->route('ventas.remisiones.show', $remision)
                ->with('success', 'Remisión marcada como entregada.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function anular(Request $request, Remision $remision)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string',
        ]);

        try {
            $remision->anular($request->motivo_anulacion);
            return redirect()->route('ventas.remisiones.index')
                ->with('success', 'Remisión anulada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function buscarFacturas(Request $request)
    {
        $cliente_id = $request->get('cliente_id');

        if (!$cliente_id) {
            return response()->json([]);
        }

        $facturas = \App\Models\Ventas\Factura::where('cliente_id', $cliente_id)
            ->whereIn('estado', ['EMITIDA', 'PAGADA', 'PARCIALMENTE_PAGADA'])
            ->with('detalles.producto')
            ->orderBy('fecha_emision', 'desc')
            ->get();

        return response()->json($facturas);
    }
}
