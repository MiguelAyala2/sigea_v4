<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\Producto;

class ProductoController extends Controller
{
    public function index()
    {
        return view('stock.productos.index');
    }

    public function create()
    {
        return view('stock.productos.create');
    }

    public function show(Producto $producto)
    {
        return view('stock.productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        return view('stock.productos.edit', compact('producto'));
    }

    public function kardex(Producto $producto)
    {
        return view('stock.productos.kardex', compact('producto'));
    }
}
