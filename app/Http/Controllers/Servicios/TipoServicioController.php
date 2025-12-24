<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoServicioController extends Controller
{
    public function index()
    {
        return view('servicios.tipos-servicio.index');
    }

    public function create()
    {
        return view('servicios.tipos-servicio.create');
    }

    public function edit($id)
    {
        return view('servicios.tipos-servicio.edit', compact('id'));
    }
}
