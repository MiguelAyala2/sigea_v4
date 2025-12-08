<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\AtributoTipo;

class AtributoTipoController extends Controller
{
    public function index()
    {
        return view('stock.atributos-tipo.index');
    }

    public function create()
    {
        return view('stock.atributos-tipo.create');
    }

    public function edit(AtributoTipo $atributo)
    {
        return view('stock.atributos-tipo.edit', compact('atributo'));
    }
}
