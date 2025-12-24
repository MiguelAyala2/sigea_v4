<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;

class PromocionController extends Controller
{
    public function index()
    {
        return view('servicios.promociones.index');
    }

    public function create()
    {
        return view('servicios.promociones.create');
    }

    public function edit($id)
    {
        return view('servicios.promociones.edit', compact('id'));
    }
}
