<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function apertura()
    {
        return view('ventas.caja.apertura');
    }

    public function movimientos()
    {
        return view('ventas.caja.movimientos');
    }

    public function cierre()
    {
        return view('ventas.caja.cierre');
    }

    public function arqueo()
    {
        return view('ventas.caja.arqueo');
    }

    public function recaudaciones()
    {
        return view('ventas.caja.recaudaciones');
    }
}
