<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;

class ClienteController extends Controller
{
    public function index()
    {
        return view('servicios.clientes.index');
    }

    public function create()
    {
        return view('servicios.clientes.create');
    }

    public function edit($cliente)
    {
        return view('servicios.clientes.edit', compact('cliente'));
    }
}
