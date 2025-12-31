<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\CompraRecepcion;
use Illuminate\Http\Request;

class RecepcionesController extends Controller
{
    public function index()
    {
        return view('compras.recepciones.index');
    }

    public function create()
    {
        return view('compras.recepciones.create');
    }

    public function show($id)
    {
        $recepcion = CompraRecepcion::with([
            'compra.proveedor',
            'deposito',
            'receptor',
            'detalles.producto'
        ])->findOrFail($id);

        return view('compras.recepciones.show', compact('recepcion'));
    }
}
