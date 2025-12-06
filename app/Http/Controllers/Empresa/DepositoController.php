<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;

class DepositoController extends Controller
{
    public function index()
    {
        return view('empresa.depositos.index');
    }

    public function create()
    {
        return view('empresa.depositos.create');
    }

    public function edit($deposito)
    {
        return view('empresa.depositos.edit', compact('deposito'));
    }
}
