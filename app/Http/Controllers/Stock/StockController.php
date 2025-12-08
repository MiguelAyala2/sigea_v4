<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;

class StockController extends Controller
{
    public function index()
    {
        return view('stock.stock.index');
    }

    public function ajuste()
    {
        return view('stock.stock.ajuste');
    }

    public function transferencia()
    {
        return view('stock.stock.transferencia');
    }

    public function inventario()
    {
        return view('stock.stock.inventario');
    }
}
