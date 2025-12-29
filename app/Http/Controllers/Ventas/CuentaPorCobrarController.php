<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\CuentaPorCobrar;
use Illuminate\Http\Request;

class CuentaPorCobrarController extends Controller
{
    public function index()
    {
        $cuentas = CuentaPorCobrar::with(['cliente', 'factura'])
            ->pendientes()
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return view('ventas.cuentas-cobrar.index', compact('cuentas'));
    }

    public function cobrar(CuentaPorCobrar $cuenta)
    {
        return view('ventas.cuentas-cobrar.cobrar', compact('cuenta'));
    }

    public function registrarPago(Request $request, CuentaPorCobrar $cuenta)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'forma_pago' => 'nullable|string',
            'referencia' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ]);

        if ($request->monto > $cuenta->saldo_pendiente) {
            return back()
                ->withInput()
                ->with('error', 'El monto no puede ser mayor al saldo pendiente (₲ ' . number_format($cuenta->saldo_pendiente, 0, ',', '.') . ')');
        }

        if ($cuenta->estado === 'PAGADA') {
            return back()->with('error', 'Esta cuenta ya está completamente pagada.');
        }

        try {
            \DB::beginTransaction();

            // Registrar el pago en la cuenta
            $cuenta->registrarPago($request->monto);

            // Registrar movimiento de caja si hay apertura activa
            $factura = $cuenta->factura;
            if ($factura && $factura->punto_expedicion_id) {
                $aperturaActual = \App\Models\Ventas\AperturaCaja::obtenerAperturaActual($factura->punto_expedicion_id);

                if ($aperturaActual) {
                    \App\Models\Ventas\MovimientoCaja::create([
                        'apertura_caja_id' => $aperturaActual->id,
                        'usuario_responsable_id' => auth()->id(),
                        'tipo_movimiento' => 'INGRESO',
                        'concepto' => 'Pago de factura ' . $cuenta->numero_factura . ' - ' . ($request->observaciones ?? 'Pago a cuenta'),
                        'monto' => $request->monto,
                        'forma_pago' => $request->forma_pago ?? 'EFECTIVO',
                        'comprobante_numero' => $cuenta->numero_factura,
                        'referencia' => $request->referencia,
                        'fecha_movimiento' => now(),
                        'hora_movimiento' => now()->format('H:i:s'),
                        'venta_id' => $factura->id,
                        'activo' => true,
                        'creadoPor' => auth()->id(),
                    ]);
                }
            }

            \DB::commit();

            $mensaje = 'Pago de ₲ ' . number_format($request->monto, 0, ',', '.') . ' registrado correctamente.';

            if ($cuenta->saldo_pendiente <= 0) {
                $mensaje .= ' La cuenta ha sido cancelada en su totalidad.';
            } else {
                $mensaje .= ' Saldo restante: ₲ ' . number_format($cuenta->saldo_pendiente, 0, ',', '.');
            }

            return redirect()->route('ventas.cuentas-cobrar.index')
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }
}
