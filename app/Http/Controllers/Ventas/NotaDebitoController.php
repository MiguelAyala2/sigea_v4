<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\NotaDebito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotaDebitoController extends Controller
{
    public function index()
    {
        return view('ventas.notas-debito.index');
    }

    public function create(Request $request)
    {
        $facturaId = $request->query('factura_id');
        return view('ventas.notas-debito.crear', compact('facturaId'));
    }

    public function show(NotaDebito $notaDebito)
    {
        $notaDebito->load(['factura', 'cliente', 'detalles.producto', 'puntoExpedicion', 'timbrado']);
        return view('ventas.notas-debito.show', compact('notaDebito'));
    }

    public function edit(NotaDebito $notaDebito)
    {
        if ($notaDebito->estado !== 'BORRADOR') {
            return redirect()->route('ventas.notas-debito.index')
                ->with('error', 'Solo se pueden editar notas de débito en estado BORRADOR');
        }

        return view('ventas.notas-debito.editar', compact('notaDebito'));
    }

    public function emitir(NotaDebito $notaDebito)
    {
        try {
            $notaDebito->emitir(Auth::id());

            return redirect()->route('ventas.notas-debito.show', $notaDebito)
                ->with('success', 'Nota de débito emitida correctamente');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function anular(Request $request, NotaDebito $notaDebito)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|min:10',
        ]);

        try {
            $notaDebito->anular($request->motivo_anulacion, Auth::id());

            return redirect()->route('ventas.notas-debito.show', $notaDebito)
                ->with('success', 'Nota de débito anulada correctamente');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pdf(NotaDebito $notaDebito)
    {
        // TODO: Implementar generación de PDF
        $notaDebito->load(['factura', 'cliente', 'detalles.producto']);
        return back()->with('info', 'Generación de PDF en desarrollo');
    }
}
