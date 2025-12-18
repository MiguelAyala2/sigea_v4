<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\Producto;
use App\Models\Empresa\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductosExport;

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

    /**
     * Exportar productos a PDF
     */
    public function exportarPDF()
    {
        $productos = Producto::with(['categoria', 'marca', 'unidadMedida', 'precioActual'])
            ->orderBy('codigo')
            ->get();

        $empresa = Empresa::first();

        // Calcular estadísticas
        $totalProductos = $productos->count();
        $productosActivos = $productos->where('activo', true)->count();
        $productosInactivos = $productos->where('activo', false)->count();

        $pdf = PDF::loadView('stock.productos.pdf', [
            'productos' => $productos,
            'empresa' => $empresa,
            'totalProductos' => $totalProductos,
            'productosActivos' => $productosActivos,
            'productosInactivos' => $productosInactivos,
            'fecha' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('listado-productos-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Exportar productos a Excel
     */
    public function exportarExcel()
    {
        return Excel::download(
            new ProductosExport(),
            'listado-productos-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
