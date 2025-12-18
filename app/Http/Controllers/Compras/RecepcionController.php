<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\CompraRecepcion;
use Illuminate\Http\Request;

class RecepcionController extends Controller
{
    /**
     * Mostrar formulario de edición
     */
    public function edit(CompraRecepcion $recepcion)
    {
        // Verificar permisos
        if (!auth()->user()->can('compras.recepciones.editar')) {
            abort(403, 'No tiene permiso para editar recepciones.');
        }

        return view('compras.recepciones.edit', compact('recepcion'));
    }

    /**
     * Marcar recepción como completa
     */
    public function completar(CompraRecepcion $recepcion)
    {
        // Verificar permisos
        if (!auth()->user()->can('compras.recepciones.editar')) {
            return back()->with('error', 'No tiene permiso para completar recepciones.');
        }

        // Validar estado
        if ($recepcion->estado === 'COMPLETA') {
            return back()->with('error', 'La recepción ya está completa.');
        }

        // Completar
        $recepcion->marcarComoCompleta();

        return back()->with('success', 'Recepción marcada como completa.');
    }

    /**
     * Marcar recepción como parcial
     */
    public function marcarParcial(Request $request, CompraRecepcion $recepcion)
    {
        $request->validate([
            'porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        // Verificar permisos
        if (!auth()->user()->can('compras.recepciones.editar')) {
            return back()->with('error', 'No tiene permiso para editar recepciones.');
        }

        // Validar estado
        if ($recepcion->estado === 'COMPLETA') {
            return back()->with('error', 'No se puede cambiar una recepción completa a parcial.');
        }

        // Marcar como parcial
        $recepcion->marcarComoParcial($request->porcentaje);

        return back()->with('success', "Recepción marcada como parcial ({$request->porcentaje}%).");
    }

    /**
     * Obtener estadísticas de recepciones
     */
    public function getEstadisticas(Request $request)
    {
        $estadisticas = [
            'totalRecepciones' => CompraRecepcion::count(),
            'pendientes' => CompraRecepcion::where('estado', 'PENDIENTE')->count(),
            'parciales' => CompraRecepcion::where('estado', 'PARCIAL')->count(),
            'completas' => CompraRecepcion::where('estado', 'COMPLETA')->count(),
            'rechazadas' => CompraRecepcion::where('estado', 'RECHAZADO')->count(),
        ];

        return response()->json($estadisticas);
    }
}
