<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;

class EmpresaController extends Controller
{
    public function index()
    {
        return view('empresa.empresa.index');
    }

    public function edit()
    {
        return view('empresa.empresa.edit');
    }
}