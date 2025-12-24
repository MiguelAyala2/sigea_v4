<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Servicios\Diagnostico;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    public function index()
    {
        return view('servicios.diagnosticos.index');
    }

    public function create()
    {
        return view('servicios.diagnosticos.create');
    }

    public function edit(Diagnostico $diagnostico)
    {
        return view('servicios.diagnosticos.edit', compact('diagnostico'));
    }
}
