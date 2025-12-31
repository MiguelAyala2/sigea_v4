<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\NotaCredito;
use Illuminate\Http\Request;

class NotaCreditoController extends Controller
{
    public function index()
    {
        return view('ventas.notas-credito.index');
    }

    public function create(Request $request)
    {
        $facturaId = $request->query('factura_id');
        return view('ventas.notas-credito.crear', compact('facturaId'));
    }

    public function show(NotaCredito $notaCredito)
    {
        $notaCredito->load(['factura', 'cliente', 'detalles.producto', 'puntoExpedicion', 'timbrado']);
        return view('ventas.notas-credito.show', compact('notaCredito'));
    }

    public function edit(NotaCredito $notaCredito)
    {
        if ($notaCredito->estado !== 'BORRADOR') {
            return redirect()->route('ventas.notas-credito.index')
                ->with('error', 'Solo se pueden editar notas de crédito en estado BORRADOR');
        }

        return view('ventas.notas-credito.editar', compact('notaCredito'));
    }

    public function emitir(NotaCredito $notaCredito)
    {
        try {
            $notaCredito->emitir(auth()->id());

            return redirect()->route('ventas.notas-credito.show', $notaCredito)
                ->with('success', 'Nota de crédito emitida correctamente');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function anular(Request $request, NotaCredito $notaCredito)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|min:10',
        ]);

        try {
            $notaCredito->anular($request->motivo_anulacion, auth()->id());

            return redirect()->route('ventas.notas-credito.show', $notaCredito)
                ->with('success', 'Nota de crédito anulada correctamente');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pdf(NotaCredito $notaCredito)
    {
        // TODO: Implementar generación de PDF
        return back()->with('info', 'Generación de PDF en desarrollo');
    }
}
