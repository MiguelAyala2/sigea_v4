<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\Categoria;

class CategoriaController extends Controller
{
    public function index()
    {
        return view('stock.categorias.index');
    }

    public function create()
    {
        return view('stock.categorias.create');
    }

    public function edit(Categoria $categoria)
    {
        return view('stock.categorias.edit', compact('categoria'));
    }
}
