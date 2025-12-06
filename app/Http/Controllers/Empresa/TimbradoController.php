<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;

class TimbradoController extends Controller
{
    public function index()
    {
        return view('empresa.timbrados.index');
    }

    public function create()
    {
        return view('empresa.timbrados.create');
    }

    public function edit($timbrado)
    {
        return view('empresa.timbrados.edit', compact('timbrado'));
    }
}