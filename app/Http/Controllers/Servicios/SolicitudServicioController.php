<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Servicios\SolicitudServicio;

class SolicitudServicioController extends Controller
{
    public function index()
    {
        return view('servicios.solicitudes.index');
    }

    public function create()
    {
        return view('servicios.solicitudes.create');
    }

    public function edit(SolicitudServicio $solicitud)
    {
        return view('servicios.solicitudes.edit', compact('solicitud'));
    }
}
