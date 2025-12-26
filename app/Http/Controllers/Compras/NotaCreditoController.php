<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\NotaCredito;
use App\Models\Compras\Proveedor;
use App\Models\Compras\Compra;
use App\Models\Stock\Producto;
use Illuminate\Http\Request;

class NotaCreditoController extends Controller
{
    public function index()
    {
        $notasCredito = NotaCredito::with(['proveedor', 'empresa', 'creador'])
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        return view('compras.notas-credito.index', compact('notasCredito'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->get();
        $compras = Compra::where('estado', 'APROBADO')->get();
        $productos = Producto::where('activo', true)->get();

        return view('compras.notas-credito.create', compact('proveedores', 'compras', 'productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id' => 'required|exists:compras.proveedores,id',
            'compra_id' => 'nullable|exists:compras.compras,id',
            'numero' => 'required|string|max:50|unique:compras.notas_credito,numero',
            'fecha' => 'required|date',
            'numero_factura_afectada' => 'nullable|string|max:50',
            'motivo' => 'required|string',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'nullable|exists:productos,id',
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
        ]);

        $notaCredito = NotaCredito::create([
            'proveedor_id' => $validated['proveedor_id'],
            'compra_id' => $validated['compra_id'] ?? null,
            'empresa_id' => session('empresa_id', 1),
            'numero' => $validated['numero'],
            'fecha' => $validated['fecha'],
            'numero_factura_afectada' => $validated['numero_factura_afectada'] ?? null,
            'motivo' => $validated['motivo'],
            'observaciones' => $validated['observaciones'] ?? null,
            'estado' => 'borrador',
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['detalles'] as $detalle) {
            $notaCredito->detalles()->create([
                'producto_id' => $detalle['producto_id'] ?? null,
                'descripcion' => $detalle['descripcion'],
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
                'descuento' => $detalle['descuento'] ?? 0,
            ]);
        }

        foreach ($notaCredito->detalles as $detalle) {
            $detalle->calcularTotales();
        }

        $notaCredito->recalcularTotales();

        return redirect()->route('compras.notas-credito.show', $notaCredito)
            ->with('success', 'Nota de crédito creada exitosamente');
    }

    public function show(NotaCredito $notaCredito)
    {
        $notaCredito->load(['proveedor', 'empresa', 'compra', 'detalles.producto', 'creador']);

        return view('compras.notas-credito.show', compact('notaCredito'));
    }

    public function edit(NotaCredito $notaCredito)
    {
        if ($notaCredito->estado !== 'borrador') {
            return redirect()->route('compras.notas-credito.show', $notaCredito)
                ->with('error', 'Solo se pueden editar notas de crédito en estado borrador');
        }

        $proveedores = Proveedor::where('activo', true)->get();
        $compras = Compra::where('estado', 'APROBADO')->get();
        $productos = Producto::where('activo', true)->get();

        return view('compras.notas-credito.edit', compact('notaCredito', 'proveedores', 'compras', 'productos'));
    }

    public function aplicar(NotaCredito $notaCredito)
    {
        if ($notaCredito->estado !== 'borrador') {
            return redirect()->route('compras.notas-credito.show', $notaCredito)
                ->with('error', 'La nota de crédito ya ha sido procesada');
        }

        $notaCredito->aplicar();

        return redirect()->route('compras.notas-credito.show', $notaCredito)
            ->with('success', 'Nota de crédito aplicada exitosamente');
    }

    public function anular(NotaCredito $notaCredito)
    {
        if ($notaCredito->estado === 'anulada') {
            return redirect()->route('compras.notas-credito.show', $notaCredito)
                ->with('error', 'La nota de crédito ya está anulada');
        }

        $notaCredito->anular();

        return redirect()->route('compras.notas-credito.show', $notaCredito)
            ->with('success', 'Nota de crédito anulada exitosamente');
    }

    public function destroy(NotaCredito $notaCredito)
    {
        if ($notaCredito->estado !== 'borrador') {
            return redirect()->route('compras.notas-credito.index')
                ->with('error', 'Solo se pueden eliminar notas de crédito en estado borrador');
        }

        $notaCredito->delete();

        return redirect()->route('compras.notas-credito.index')
            ->with('success', 'Nota de crédito eliminada exitosamente');
    }
}
