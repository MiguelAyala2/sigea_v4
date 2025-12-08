<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;

class ReporteController extends Controller
{
    public function stockBajo()
    {
        return view('stock.reportes.stock-bajo');
    }

    public function rotacion()
    {
        return view('stock.reportes.rotacion');
    }

    public function valorizado()
    {
        return view('stock.reportes.valorizado');
    }
}
