<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;

class SucursalController extends Controller
{
    public function index()
    {
        return view('empresa.sucursales.index');
    }

    public function create()
    {
        return view('empresa.sucursales.create');
    }

    public function edit($sucursal)
    {
        return view('empresa.sucursales.edit', compact('sucursal'));
    }
}
