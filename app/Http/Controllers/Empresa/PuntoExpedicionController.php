<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;

class PuntoExpedicionController extends Controller
{
    public function index()
    {
        return view('empresa.puntos-expedicion.index');
    }

    public function create()
    {
        return view('empresa.puntos-expedicion.create');
    }

    public function edit($puntoExpedicion)
    {
        return view('empresa.puntos-expedicion.edit', compact('puntoExpedicion'));
    }
}