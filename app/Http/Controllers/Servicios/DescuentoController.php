<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;

class DescuentoController extends Controller
{
    public function index()
    {
        return view('servicios.descuentos.index');
    }

    public function create()
    {
        return view('servicios.descuentos.create');
    }

    public function edit($id)
    {
        return view('servicios.descuentos.edit', compact('id'));
    }
}
