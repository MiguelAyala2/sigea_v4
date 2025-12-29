<?php

namespace App\Livewire\Ventas\Caja;

use Livewire\Component;
use App\Models\Ventas\AperturaCaja;
use App\Models\Ventas\CierreCaja;
use App\Models\Ventas\ArqueoCaja;
use App\Models\Ventas\RecaudacionADepositar;
use App\Models\Empresa\PuntoExpedicion;
use Illuminate\Support\Facades\DB;

class Cierre extends Component
{
    // Propiedades del formulario
    public $punto_expedicion_id;
    public $saldo_declarado;
    public $observaciones;

    // Datos auxiliares
    public $apertura_actual;
    public $tiene_apertura_abierta = false;
    public $arqueo;

    // Detalles del cierre calculados
    public $saldo_inicial = 0;
    public $total_ingresos = 0;
    public $total_egresos = 0;
    public $saldo_calculado = 0;
    public $diferencia = 0;

    // Totales por forma de pago
    public $total_efectivo = 0;
    public $total_cheques = 0;
    public $total_tarjeta_debito = 0;
    public $total_tarjeta_credito = 0;
    public $total_transferencias = 0;
    public $total_qr = 0;
    public $total_otros = 0;

    public function mount()
    {
        $this->verificarAperturaActual();
    }

    protected function rules()
    {
        return [
            'saldo_declarado' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000',
        ];
    }

    public function verificarAperturaActual()
    {
        if ($this->punto_expedicion_id) {
            $this->apertura_actual = AperturaCaja::obtenerAperturaActual($this->punto_expedicion_id);

            if ($this->apertura_actual) {
                $this->apertura_actual->load(['movimientos', 'usuario']);
            }

            $this->tiene_apertura_abierta = $this->apertura_actual !== null;

            if ($this->tiene_apertura_abierta) {
                $this->calcularTotales();
                $this->verificarArqueo();
            }
        }
    }

    public function updatedPuntoExpedicionId()
    {
        $this->verificarAperturaActual();
    }

    public function updatedSaldoDeclarado()
    {
        // Si el saldo declarado es null o vacío, se asume que no hay diferencia
        if ($this->saldo_declarado === null || $this->saldo_declarado === '') {
            $this->diferencia = 0;
        } else {
            $this->diferencia = $this->saldo_declarado - $this->saldo_calculado;
        }
    }

    protected function calcularTotales()
    {
        if (!$this->apertura_actual) {
            return;
        }

        $this->saldo_inicial = $this->apertura_actual->saldo_inicial;
        $this->total_ingresos = $this->apertura_actual->total_ingresos;
        $this->total_egresos = $this->apertura_actual->total_egresos;
        $this->saldo_calculado = $this->apertura_actual->saldo_actual;

        // Calcular totales por forma de pago
        $movimientos = $this->apertura_actual->movimientos;

        $this->total_efectivo = $movimientos->where('forma_pago', 'EFECTIVO')
            ->sum(function($m) { return $m->es_ingreso ? $m->monto : -$m->monto; });

        $this->total_cheques = $movimientos->where('forma_pago', 'CHEQUE')
            ->sum(function($m) { return $m->es_ingreso ? $m->monto : -$m->monto; });

        $this->total_tarjeta_debito = $movimientos->where('forma_pago', 'TARJETA_DEBITO')
            ->sum(function($m) { return $m->es_ingreso ? $m->monto : -$m->monto; });

        $this->total_tarjeta_credito = $movimientos->where('forma_pago', 'TARJETA_CREDITO')
            ->sum(function($m) { return $m->es_ingreso ? $m->monto : -$m->monto; });

        $this->total_transferencias = $movimientos->where('forma_pago', 'TRANSFERENCIA')
            ->sum(function($m) { return $m->es_ingreso ? $m->monto : -$m->monto; });

        $this->total_qr = $movimientos->where('forma_pago', 'QR')
            ->sum(function($m) { return $m->es_ingreso ? $m->monto : -$m->monto; });

        $this->total_otros = $movimientos->where('forma_pago', 'OTRO')
            ->sum(function($m) { return $m->es_ingreso ? $m->monto : -$m->monto; });
    }

    protected function verificarArqueo()
    {
        if (!$this->apertura_actual) {
            return;
        }

        $this->arqueo = ArqueoCaja::where('apertura_caja_id', $this->apertura_actual->id)
            ->where('activo', true)
            ->first();
    }

    protected function generarRecaudaciones($cierre)
    {
        // Generar recaudaciones para cheques
        if ($this->total_cheques > 0) {
            RecaudacionADepositar::create([
                'apertura_caja_id' => $this->apertura_actual->id,
                'cierre_caja_id' => $cierre->id,
                'fecha_recaudacion' => now()->toDateString(),
                'tipo_recaudacion' => 'CHEQUE',
                'monto' => $this->total_cheques,
                'fecha_prevista_deposito' => now()->addDay()->toDateString(),
                'estado' => 'PENDIENTE',
                'usuario_registra_id' => auth()->id(),
                'observaciones' => 'Generado automáticamente en cierre de caja',
                'activo' => true,
                'creadoPor' => auth()->id(),
            ]);
        }

        // Generar recaudaciones para tarjetas
        if ($this->total_tarjeta_debito + $this->total_tarjeta_credito > 0) {
            RecaudacionADepositar::create([
                'apertura_caja_id' => $this->apertura_actual->id,
                'cierre_caja_id' => $cierre->id,
                'fecha_recaudacion' => now()->toDateString(),
                'tipo_recaudacion' => 'TARJETA',
                'monto' => $this->total_tarjeta_debito + $this->total_tarjeta_credito,
                'fecha_prevista_deposito' => now()->addDays(2)->toDateString(),
                'estado' => 'PENDIENTE',
                'usuario_registra_id' => auth()->id(),
                'observaciones' => 'Generado automáticamente en cierre de caja',
                'activo' => true,
                'creadoPor' => auth()->id(),
            ]);
        }

        // Generar recaudaciones para transferencias
        if ($this->total_transferencias > 0) {
            RecaudacionADepositar::create([
                'apertura_caja_id' => $this->apertura_actual->id,
                'cierre_caja_id' => $cierre->id,
                'fecha_recaudacion' => now()->toDateString(),
                'tipo_recaudacion' => 'TRANSFERENCIA',
                'monto' => $this->total_transferencias,
                'fecha_prevista_deposito' => now()->toDateString(),
                'estado' => 'PENDIENTE',
                'usuario_registra_id' => auth()->id(),
                'observaciones' => 'Generado automáticamente en cierre de caja',
                'activo' => true,
                'creadoPor' => auth()->id(),
            ]);
        }

        // Generar recaudaciones para QR y otros (si son montos significativos)
        if ($this->total_qr + $this->total_otros > 0) {
            RecaudacionADepositar::create([
                'apertura_caja_id' => $this->apertura_actual->id,
                'cierre_caja_id' => $cierre->id,
                'fecha_recaudacion' => now()->toDateString(),
                'tipo_recaudacion' => 'MIXTO',
                'monto' => $this->total_qr + $this->total_otros,
                'fecha_prevista_deposito' => now()->addDay()->toDateString(),
                'estado' => 'PENDIENTE',
                'usuario_registra_id' => auth()->id(),
                'observaciones' => 'QR y otros medios de pago - Generado automáticamente en cierre de caja',
                'activo' => true,
                'creadoPor' => auth()->id(),
            ]);
        }
    }

    public function cerrarCaja()
    {
        if (!$this->tiene_apertura_abierta) {
            session()->flash('error', 'No hay una caja abierta para cerrar.');
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // Si el saldo declarado es null, usar el saldo calculado (sin diferencia)
            $saldo_declarado_final = $this->saldo_declarado ?? $this->saldo_calculado;
            $diferencia_final = $this->saldo_declarado !== null && $this->saldo_declarado !== ''
                ? $this->diferencia
                : 0;

            // Determinar estado del cierre antes de crear
            $estado = abs($diferencia_final) <= 0.01
                ? 'CERRADO_CORRECTO'
                : ($diferencia_final < 0 ? 'CERRADO_CON_FALTANTE' : 'CERRADO_CON_SOBRANTE');

            // Crear registro de cierre
            $cierre = CierreCaja::create([
                'apertura_caja_id' => $this->apertura_actual->id,
                'usuario_id' => auth()->id(),
                'fecha_cierre' => now()->toDateString(),
                'hora_cierre' => now()->toTimeString(),
                'saldo_inicial' => $this->saldo_inicial,
                'total_ingresos' => $this->total_ingresos,
                'total_egresos' => $this->total_egresos,
                'saldo_calculado' => $this->saldo_calculado,
                'saldo_declarado' => $saldo_declarado_final,
                'diferencia' => $diferencia_final,
                'total_efectivo' => $this->total_efectivo,
                'total_cheques' => $this->total_cheques,
                'total_tarjetas' => $this->total_tarjeta_debito + $this->total_tarjeta_credito,
                'total_transferencias' => $this->total_transferencias,
                'total_otros' => $this->total_qr + $this->total_otros,
                'estado' => $estado,
                'observaciones' => $this->observaciones,
                'activo' => true,
                'creadoPor' => auth()->id(),
            ]);

            // Generar recaudaciones a depositar (todo lo que no es efectivo)
            $this->generarRecaudaciones($cierre);

            // Cerrar la apertura
            $this->apertura_actual->cerrar();

            DB::commit();

            session()->flash('success', 'Caja cerrada exitosamente. Estado: ' . CierreCaja::ESTADOS[$estado]);

            // Limpiar formulario
            $this->reset(['saldo_declarado', 'observaciones']);
            $this->verificarAperturaActual();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al cerrar la caja: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $puntos_expedicion = PuntoExpedicion::with('sucursal')
            ->where('activo', true)
            ->where('tipo', 'caja')
            ->orderBy('codigo')
            ->get();

        return view('livewire.ventas.caja.cierre', [
            'puntos_expedicion' => $puntos_expedicion,
        ]);
    }
}
