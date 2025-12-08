<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;

class MarcaController extends Controller
{
    public function index()
    {
        return view('stock.marcas.index');
    }

    public function create()
    {
        return view('stock.marcas.create');
    }

    public function edit($marca)
    {
        return view('stock.marcas.edit', compact('marca'));
    }
}