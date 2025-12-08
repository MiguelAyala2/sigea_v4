<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;

class UnidadMedidaController extends Controller
{
    public function index()
    {
        return view('stock.unidades-medida.index');
    }

    public function create()
    {
        return view('stock.unidades-medida.create');
    }

    public function edit($unidadMedida)
    {
        return view('stock.unidades-medida.edit', compact('unidadMedida'));
    }
}
