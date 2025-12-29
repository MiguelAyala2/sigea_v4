<?php

namespace App\Livewire\Ventas;

use App\Models\Ventas\CuentaPorCobrar;
use App\Models\Ventas\FacturaCuota;
use App\Models\Ventas\AperturaCaja;
use App\Models\Ventas\MovimientoCaja;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PagarCuenta extends Component
{
    public $cuenta;
    public $cuotas;
    public $cuotas_seleccionadas = [];
    public $tipo_pago = 'CUOTA'; // CUOTA o TOTAL
    public $formas_pago = [];
    public $forma_pago_temp = [
        'forma_pago' => 'EFECTIVO',
        'monto' => 0,
        'referencia' => '',
    ];
    public $total_a_pagar = 0;
    public $total_pagado = 0;
    public $saldo_restante = 0;
    public $observaciones = '';

    protected $rules = [
        'tipo_pago' => 'required|in:CUOTA,TOTAL',
        'cuotas_seleccionadas' => 'required_if:tipo_pago,CUOTA|array|min:1',
        'formas_pago' => 'required|array|min:1',
        'observaciones' => 'nullable|string',
    ];

    public function mount(CuentaPorCobrar $cuenta)
    {
        // Cargar la cuenta con las relaciones necesarias
        $this->cuenta = $cuenta->load(['factura.cuotas', 'cliente']);

        // Cargar cuotas de la factura si es a crédito
        if ($this->cuenta->factura && $this->cuenta->factura->condicion_pago === 'CREDITO') {
            $this->cuotas = $this->cuenta->factura->cuotas()
                ->where('estado', '!=', 'PAGADA')
                ->orderBy('numero_cuota')
                ->get();
        } else {
            $this->cuotas = collect();
        }

        $this->calcularTotal();
    }

    public function updatedTipoPago()
    {
        $this->cuotas_seleccionadas = [];
        $this->calcularTotal();
    }

    public function toggleCuota($cuotaId)
    {
        if (in_array($cuotaId, $this->cuotas_seleccionadas)) {
            $this->cuotas_seleccionadas = array_values(array_diff($this->cuotas_seleccionadas, [$cuotaId]));
        } else {
            $this->cuotas_seleccionadas[] = $cuotaId;
        }

        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        if ($this->tipo_pago === 'TOTAL') {
            $this->total_a_pagar = $this->cuenta->saldo_pendiente;
        } elseif ($this->tipo_pago === 'CUOTA' && count($this->cuotas_seleccionadas) > 0) {
            $cuotasSeleccionadas = $this->cuotas->whereIn('id', $this->cuotas_seleccionadas);
            $this->total_a_pagar = $cuotasSeleccionadas->sum('saldo_pendiente');
        } else {
            $this->total_a_pagar = 0;
        }

        $this->calcularSaldo();
    }

    public function calcularSaldo()
    {
        $this->total_pagado = round(array_sum(array_column($this->formas_pago, 'monto')), 2);
        $this->saldo_restante = round($this->total_a_pagar - $this->total_pagado, 2);
    }

    public function isPagoCompleto()
    {
        return abs($this->total_pagado - $this->total_a_pagar) < 0.01 && count($this->formas_pago) > 0;
    }

    public function agregarFormaPago()
    {
        if ($this->forma_pago_temp['monto'] <= 0) {
            session()->flash('error', 'El monto debe ser mayor a 0.');
            return;
        }

        $this->formas_pago[] = [
            'forma_pago' => $this->forma_pago_temp['forma_pago'],
            'monto' => $this->forma_pago_temp['monto'],
            'referencia' => $this->forma_pago_temp['referencia'],
        ];

        // Resetear el formulario temporal
        $this->forma_pago_temp = [
            'forma_pago' => 'EFECTIVO',
            'monto' => 0,
            'referencia' => '',
        ];

        $this->calcularSaldo();
    }

    public function eliminarFormaPago($index)
    {
        unset($this->formas_pago[$index]);
        $this->formas_pago = array_values($this->formas_pago);
        $this->calcularSaldo();
    }

    public function pagoTotal()
    {
        $this->tipo_pago = 'TOTAL';
        $this->cuotas_seleccionadas = [];
        $this->calcularTotal();
    }

    public function registrarPago()
    {
        // Validaciones
        if ($this->total_a_pagar <= 0) {
            session()->flash('error', 'Debe seleccionar al menos una cuota o elegir pago total.');
            return;
        }

        if (count($this->formas_pago) === 0) {
            session()->flash('error', 'Debe agregar al menos una forma de pago.');
            return;
        }

        if (abs($this->total_pagado - $this->total_a_pagar) >= 0.01) {
            session()->flash('error', 'El total de formas de pago debe ser igual al monto a pagar. Diferencia: ₲ ' . number_format(abs($this->total_pagado - $this->total_a_pagar), 0, ',', '.'));
            return;
        }

        if ($this->cuenta->estado === 'PAGADA') {
            session()->flash('error', 'Esta cuenta ya está completamente pagada.');
            return;
        }

        try {
            DB::beginTransaction();

            // Si es pago total o si es por cuotas
            if ($this->tipo_pago === 'TOTAL') {
                // Pagar todas las cuotas pendientes proporcionalmente
                if ($this->cuotas->isNotEmpty()) {
                    $totalCuotasPendientes = $this->cuotas->sum('saldo_pendiente');

                    foreach ($this->cuotas as $cuota) {
                        $montoPagarCuota = ($cuota->saldo_pendiente / $totalCuotasPendientes) * $this->total_pagado;
                        $cuota->registrarPago($montoPagarCuota);
                    }
                }

                // Registrar pago en la cuenta por cobrar
                $this->cuenta->registrarPago($this->total_pagado);
            } else {
                // Pago por cuotas seleccionadas
                foreach ($this->cuotas_seleccionadas as $cuotaId) {
                    $cuota = FacturaCuota::find($cuotaId);
                    if ($cuota) {
                        // Calcular proporción del pago para esta cuota
                        $montoPagarCuota = min($cuota->saldo_pendiente, $this->total_pagado);
                        $cuota->registrarPago($montoPagarCuota);
                    }
                }

                // Registrar pago en la cuenta por cobrar
                $this->cuenta->registrarPago($this->total_pagado);
            }

            // Registrar movimientos de caja para cada forma de pago
            $factura = $this->cuenta->factura;
            if ($factura && $factura->punto_expedicion_id) {
                $aperturaActual = AperturaCaja::obtenerAperturaActual($factura->punto_expedicion_id);

                if ($aperturaActual) {
                    foreach ($this->formas_pago as $fp) {
                        MovimientoCaja::create([
                            'apertura_caja_id' => $aperturaActual->id,
                            'usuario_responsable_id' => auth()->id(),
                            'tipo_movimiento' => 'INGRESO',
                            'concepto' => 'Pago de factura ' . $this->cuenta->numero_factura .
                                         ($this->tipo_pago === 'TOTAL' ? ' - Pago Total' : ' - Pago de Cuota(s)') .
                                         ($this->observaciones ? ' - ' . $this->observaciones : ''),
                            'monto' => $fp['monto'],
                            'forma_pago' => $fp['forma_pago'],
                            'comprobante_numero' => $this->cuenta->numero_factura,
                            'referencia' => $fp['referencia'],
                            'fecha_movimiento' => now(),
                            'hora_movimiento' => now()->format('H:i:s'),
                            'venta_id' => $factura->id,
                            'activo' => true,
                            'creadoPor' => auth()->id(),
                        ]);
                    }
                }
            }

            DB::commit();

            $mensaje = 'Pago de ₲ ' . number_format($this->total_pagado, 0, ',', '.') . ' registrado correctamente.';

            $this->cuenta->refresh();
            if ($this->cuenta->saldo_pendiente <= 0) {
                $mensaje .= ' La cuenta ha sido cancelada en su totalidad.';
            } else {
                $mensaje .= ' Saldo restante: ₲ ' . number_format($this->cuenta->saldo_pendiente, 0, ',', '.');
            }

            session()->flash('success', $mensaje);
            return redirect()->route('ventas.cuentas-cobrar.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ventas.pagar-cuenta');
    }
}
