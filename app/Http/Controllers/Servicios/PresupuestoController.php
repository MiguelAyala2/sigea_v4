<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Servicios\Presupuesto;
use Illuminate\Http\Request;

class PresupuestoController extends Controller
{
    public function index()
    {
        return view('servicios.presupuestos.index');
    }

    public function create()
    {
        return view('servicios.presupuestos.create');
    }

    public function edit($id)
    {
        $presupuesto = Presupuesto::findOrFail($id);
        
        // Solo permitir editar si está en estado pendiente
        if ($presupuesto->estado !== 'pendiente_aprobacion') {
            return redirect()->route('servicios.presupuestos.index')
                ->with('error', 'No se puede editar un presupuesto que no está pendiente de aprobación.');
        }

        return view('servicios.presupuestos.edit', compact('presupuesto'));
    }
}
