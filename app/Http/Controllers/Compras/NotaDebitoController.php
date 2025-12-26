<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\NotaDebito;
use App\Models\Compras\Proveedor;
use App\Models\Compras\Compra;
use App\Models\Stock\Producto;
use Illuminate\Http\Request;

class NotaDebitoController extends Controller
{
    public function index()
    {
        $notasDebito = NotaDebito::with(['proveedor', 'empresa', 'creador'])
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        return view('compras.notas-debito.index', compact('notasDebito'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->get();
        $compras = Compra::where('estado', 'APROBADO')->get();
        $productos = Producto::where('activo', true)->get();

        return view('compras.notas-debito.create', compact('proveedores', 'compras', 'productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id' => 'required|exists:compras.proveedores,id',
            'compra_id' => 'nullable|exists:compras.compras,id',
            'numero' => 'required|string|max:50|unique:compras.notas_debito,numero',
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

        $notaDebito = NotaDebito::create([
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
            $notaDebito->detalles()->create([
                'producto_id' => $detalle['producto_id'] ?? null,
                'descripcion' => $detalle['descripcion'],
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
                'descuento' => $detalle['descuento'] ?? 0,
            ]);
        }

        foreach ($notaDebito->detalles as $detalle) {
            $detalle->calcularTotales();
        }

        $notaDebito->recalcularTotales();

        return redirect()->route('compras.notas-debito.show', $notaDebito)
            ->with('success', 'Nota de débito creada exitosamente');
    }

    public function show(NotaDebito $notaDebito)
    {
        $notaDebito->load(['proveedor', 'empresa', 'compra', 'detalles.producto', 'creador']);

        return view('compras.notas-debito.show', compact('notaDebito'));
    }

    public function edit(NotaDebito $notaDebito)
    {
        if ($notaDebito->estado !== 'borrador') {
            return redirect()->route('compras.notas-debito.show', $notaDebito)
                ->with('error', 'Solo se pueden editar notas de débito en estado borrador');
        }

        $proveedores = Proveedor::where('activo', true)->get();
        $compras = Compra::where('estado', 'APROBADO')->get();
        $productos = Producto::where('activo', true)->get();

        return view('compras.notas-debito.edit', compact('notaDebito', 'proveedores', 'compras', 'productos'));
    }

    public function aplicar(NotaDebito $notaDebito)
    {
        if ($notaDebito->estado !== 'borrador') {
            return redirect()->route('compras.notas-debito.show', $notaDebito)
                ->with('error', 'La nota de débito ya ha sido procesada');
        }

        $notaDebito->aplicar();

        return redirect()->route('compras.notas-debito.show', $notaDebito)
            ->with('success', 'Nota de débito aplicada exitosamente');
    }

    public function anular(NotaDebito $notaDebito)
    {
        if ($notaDebito->estado === 'anulada') {
            return redirect()->route('compras.notas-debito.show', $notaDebito)
                ->with('error', 'La nota de débito ya está anulada');
        }

        $notaDebito->anular();

        return redirect()->route('compras.notas-debito.show', $notaDebito)
            ->with('success', 'Nota de débito anulada exitosamente');
    }

    public function destroy(NotaDebito $notaDebito)
    {
        if ($notaDebito->estado !== 'borrador') {
            return redirect()->route('compras.notas-debito.index')
                ->with('error', 'Solo se pueden eliminar notas de débito en estado borrador');
        }

        $notaDebito->delete();

        return redirect()->route('compras.notas-debito.index')
            ->with('success', 'Nota de débito eliminada exitosamente');
    }
}
