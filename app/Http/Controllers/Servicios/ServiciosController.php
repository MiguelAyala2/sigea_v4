<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;

class ServiciosController extends Controller
{
    // Clientes
    public function clientesRegistrar()
    {
        return view('servicios.clientes.registrar');
    }

    public function clientesHistorial()
    {
        return view('servicios.clientes.historial');
    }

    // Gestión de Servicios Técnicos
    public function solicitudes()
    {
        return view('servicios.gestion.solicitudes');
    }

    public function recepcion()
    {
        return view('servicios.gestion.recepcion');
    }

    public function diagnostico()
    {
        return view('servicios.gestion.diagnostico');
    }

    public function presupuestos()
    {
        return view('servicios.gestion.presupuestos');
    }

    public function ordenes()
    {
        return view('servicios.gestion.ordenes');
    }

    public function entrega()
    {
        return view('servicios.gestion.entrega');
    }

    // Promociones y Descuentos
    public function promociones()
    {
        return view('servicios.promociones.index');
    }

    public function descuentos()
    {
        return view('servicios.descuentos.index');
    }

    // Reclamos
    public function reclamosRegistrar()
    {
        return view('servicios.reclamos.registrar');
    }

    public function reclamosSeguimiento()
    {
        return view('servicios.reclamos.seguimiento');
    }

    // Informes
    public function informes()
    {
        return view('servicios.informes.index');
    }
}
