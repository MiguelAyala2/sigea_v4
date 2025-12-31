<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InformesController extends Controller
{
    public function index()
    {
        return view('ventas.informes.index');
    }
}
