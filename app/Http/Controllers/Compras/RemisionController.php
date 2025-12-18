<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\Remision;
use App\Models\Compras\Proveedor;
use App\Models\Compras\OrdenCompra;
use App\Models\Stock\Producto;
use App\Models\Empresa\Sucursal;
use App\Models\Empresa\Deposito;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RemisionController extends Controller
{
    public function index()
    {
        $remisiones = Remision::with(['proveedor', 'empresa', 'creador', 'sucursalDestino', 'depositoDestino'])
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        return view('compras.remisiones.index', compact('remisiones'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->get();
        $ordenesCompra = OrdenCompra::where('estado', 'APROBADO')->get();
        $productos = Producto::where('activo', true)->get();
        $sucursales = Sucursal::where('activo', true)->get();

        // Generar próximo número de remisión
        $ultimaRemision = Remision::orderBy('id', 'desc')->first();
        $siguienteNumero = $ultimaRemision ? intval(substr($ultimaRemision->numero, 4)) + 1 : 1;
        $numeroRemision = 'REM-' . str_pad($siguienteNumero, 8, '0', STR_PAD_LEFT);

        return view('compras.remisiones.create', compact('proveedores', 'ordenesCompra', 'productos', 'sucursales', 'numeroRemision'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:INTERNA,EXTERNA',
            'proveedor_id' => ['nullable', Rule::exists(Proveedor::class, 'id')],
            'orden_compra_id' => ['nullable', Rule::exists(OrdenCompra::class, 'id')],
            'numero' => ['required', 'string', 'max:50', Rule::unique(Remision::class, 'numero')],
            'fecha' => 'required|date',

            // Campos para remisión externa
            'cliente_nombre' => 'required_if:tipo,EXTERNA|nullable|string|max:200',
            'cliente_ruc' => 'nullable|string|max:50',
            'cliente_direccion' => 'nullable|string',
            'cliente_telefono' => 'nullable|string|max:50',
            'cliente_email' => 'nullable|email|max:100',

            // Campos para remisión interna
            'sucursal_destino_id' => ['required_if:tipo,INTERNA', 'nullable', Rule::exists(Sucursal::class, 'id')],
            'deposito_destino_id' => ['required_if:tipo,INTERNA', 'nullable', Rule::exists(Deposito::class, 'id')],

            'numero_guia_proveedor' => 'nullable|string|max:50',
            'transportista' => 'nullable|string|max:100',
            'placa_vehiculo' => 'nullable|string|max:20',
            'direccion_entrega' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => ['required', Rule::exists(Producto::class, 'id')],
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.cantidad_enviada' => 'required|numeric|min:0.01',
            'detalles.*.observaciones' => 'nullable|string',
        ]);

        $remision = Remision::create([
            'tipo' => $validated['tipo'],
            'proveedor_id' => $validated['proveedor_id'] ?? null,
            'orden_compra_id' => $validated['orden_compra_id'] ?? null,
            'empresa_id' => session('empresa_id', 1),

            // Datos cliente (solo para EXTERNA)
            'cliente_nombre' => $validated['tipo'] === 'EXTERNA' ? $validated['cliente_nombre'] : null,
            'cliente_ruc' => $validated['cliente_ruc'] ?? null,
            'cliente_direccion' => $validated['cliente_direccion'] ?? null,
            'cliente_telefono' => $validated['cliente_telefono'] ?? null,
            'cliente_email' => $validated['cliente_email'] ?? null,

            // Datos sucursal (solo para INTERNA)
            'sucursal_destino_id' => $validated['tipo'] === 'INTERNA' ? $validated['sucursal_destino_id'] : null,
            'deposito_destino_id' => $validated['tipo'] === 'INTERNA' ? $validated['deposito_destino_id'] : null,

            'numero' => $validated['numero'],
            'fecha' => $validated['fecha'],
            'numero_guia_proveedor' => $validated['numero_guia_proveedor'] ?? null,
            'transportista' => $validated['transportista'] ?? null,
            'placa_vehiculo' => $validated['placa_vehiculo'] ?? null,
            'direccion_entrega' => $validated['direccion_entrega'] ?? null,
            'observaciones' => $validated['observaciones'] ?? null,
            'estado' => 'pendiente',
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['detalles'] as $detalle) {
            $remision->detalles()->create([
                'producto_id' => $detalle['producto_id'],
                'descripcion' => $detalle['descripcion'],
                'cantidad_enviada' => $detalle['cantidad_enviada'],
                'unidad_medida' => $detalle['unidad_medida'] ?? null,
                'observaciones' => $detalle['observaciones'] ?? null,
            ]);
        }

        return redirect()->route('compras.remisiones.show', $remision)
            ->with('success', 'Remisión creada exitosamente');
    }

    public function show(Remision $remision)
    {
        $remision->load(['proveedor', 'empresa', 'ordenCompra', 'detalles.producto', 'creador', 'recibidoPor', 'sucursalDestino', 'depositoDestino']);

        return view('compras.remisiones.show', compact('remision'));
    }

    public function edit(Remision $remision)
    {
        if ($remision->estado !== 'pendiente') {
            return redirect()->route('compras.remisiones.show', $remision)
                ->with('error', 'Solo se pueden editar remisiones en estado pendiente');
        }

        $proveedores = Proveedor::where('activo', true)->get();
        $ordenesCompra = OrdenCompra::where('estado', 'aprobada')->get();
        $productos = Producto::where('activo', true)->get();
        $sucursales = Sucursal::where('activo', true)->get();

        return view('compras.remisiones.edit', compact('remision', 'proveedores', 'ordenesCompra', 'productos', 'sucursales'));
    }

    public function recibir(Request $request, Remision $remision)
    {
        if ($remision->estado === 'recibida' || $remision->estado === 'anulada') {
            return redirect()->route('compras.remisiones.show', $remision)
                ->with('error', 'La remisión ya fue procesada');
        }

        $validated = $request->validate([
            'detalles' => 'required|array',
            'detalles.*.id' => 'required|exists:compras.remisiones_detalle,id',
            'detalles.*.cantidad_recibida' => 'required|numeric|min:0',
            'detalles.*.cantidad_rechazada' => 'nullable|numeric|min:0',
            'detalles.*.motivo_rechazo' => 'nullable|string',
        ]);

        foreach ($validated['detalles'] as $detalleData) {
            $detalle = $remision->detalles()->find($detalleData['id']);
            $detalle->update([
                'cantidad_recibida' => $detalleData['cantidad_recibida'],
                'cantidad_rechazada' => $detalleData['cantidad_rechazada'] ?? 0,
                'motivo_rechazo' => $detalleData['motivo_rechazo'] ?? null,
            ]);
        }

        $remision->verificarEstadoParcial();

        return redirect()->route('compras.remisiones.show', $remision)
            ->with('success', 'Recepción de mercancía registrada exitosamente');
    }

    public function anular(Remision $remision)
    {
        if ($remision->estado === 'anulada') {
            return redirect()->route('compras.remisiones.show', $remision)
                ->with('error', 'La remisión ya está anulada');
        }

        $remision->anular();

        return redirect()->route('compras.remisiones.show', $remision)
            ->with('success', 'Remisión anulada exitosamente');
    }

    public function destroy(Remision $remision)
    {
        if ($remision->estado !== 'pendiente') {
            return redirect()->route('compras.remisiones.index')
                ->with('error', 'Solo se pueden eliminar remisiones en estado pendiente');
        }

        $remision->delete();

        return redirect()->route('compras.remisiones.index')
            ->with('success', 'Remisión eliminada exitosamente');
    }

    /**
     * API endpoint para obtener depósitos de una sucursal
     */
    public function getDepositosPorSucursal($sucursalId)
    {
        $depositos = Deposito::where('sucursal_id', $sucursalId)
            ->where('activo', true)
            ->get(['id', 'codigo', 'nombre']);

        return response()->json($depositos);
    }

    /**
     * API endpoint para obtener datos de una sucursal
     */
    public function getSucursalDatos($sucursalId)
    {
        $sucursal = Sucursal::with('depositos')->findOrFail($sucursalId);

        return response()->json($sucursal);
    }
}
