<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Servicios\Recepcion;

class RecepcionController extends Controller
{
    public function index()
    {
        return view('servicios.recepciones.index');
    }

    public function create()
    {
        return view('servicios.recepciones.create');
    }

    public function edit(Recepcion $recepcion)
    {
        return view('servicios.recepciones.edit', compact('recepcion'));
    }
}
