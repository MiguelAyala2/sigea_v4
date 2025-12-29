<?php

namespace App\Livewire\Ventas\Caja;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ventas\AperturaCaja;
use App\Models\Ventas\MovimientoCaja;
use App\Models\Empresa\PuntoExpedicion;
use Illuminate\Support\Facades\DB;

class Movimientos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Propiedades del formulario
    public $punto_expedicion_id;
    public $tipo_movimiento = 'INGRESO';
    public $forma_pago = 'EFECTIVO';
    public $monto;
    public $descripcion;
    public $referencia;

    // Datos auxiliares
    public $apertura_actual;
    public $tiene_apertura_abierta = false;

    // Modal
    public $showModal = false;

    public function mount()
    {
        $this->verificarAperturaActual();
    }

    protected function rules()
    {
        return [
            'tipo_movimiento' => 'required|in:INGRESO,EGRESO,DEPOSITO,RETIRO',
            'forma_pago' => 'required|in:EFECTIVO,CHEQUE,TARJETA_DEBITO,TARJETA_CREDITO,TRANSFERENCIA,QR,OTRO',
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:500',
            'referencia' => 'nullable|string|max:100',
        ];
    }

    public function verificarAperturaActual()
    {
        if ($this->punto_expedicion_id) {
            $this->apertura_actual = AperturaCaja::obtenerAperturaActual($this->punto_expedicion_id);
            $this->tiene_apertura_abierta = $this->apertura_actual !== null;
        }
    }

    public function updatedPuntoExpedicionId()
    {
        $this->verificarAperturaActual();
        $this->resetPage();
    }

    public function abrirModal()
    {
        if (!$this->tiene_apertura_abierta) {
            session()->flash('error', 'No hay una caja abierta para este punto de expedición.');
            return;
        }

        $this->resetValidation();
        $this->tipo_movimiento = 'INGRESO';
        $this->forma_pago = 'EFECTIVO';
        $this->monto = null;
        $this->descripcion = '';
        $this->referencia = '';
        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function registrarMovimiento()
    {
        $this->validate();

        if (!$this->tiene_apertura_abierta) {
            session()->flash('error', 'No hay una caja abierta para este punto de expedición.');
            return;
        }

        try {
            DB::beginTransaction();

            MovimientoCaja::create([
                'apertura_caja_id' => $this->apertura_actual->id,
                'tipo_movimiento' => $this->tipo_movimiento,
                'forma_pago' => $this->forma_pago,
                'monto' => $this->monto,
                'descripcion' => $this->descripcion,
                'referencia' => $this->referencia,
                'fecha_movimiento' => now()->toDateString(),
                'hora_movimiento' => now()->toTimeString(),
                'usuario_id' => auth()->user()->id,
                'activo' => true,
                'creadoPor' => auth()->user()->id,
            ]);

            DB::commit();

            $tipo = $this->tipo_movimiento === 'INGRESO' || $this->tipo_movimiento === 'DEPOSITO' ? 'ingreso' : 'egreso';
            session()->flash('success', "Movimiento de {$tipo} registrado exitosamente por ₲ " . number_format($this->monto, 0, ',', '.'));

            $this->cerrarModal();
            $this->verificarAperturaActual();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar el movimiento: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $puntos_expedicion = PuntoExpedicion::with('sucursal')
            ->where('activo', true)
            ->where('tipo', 'caja')
            ->orderBy('codigo')
            ->get();

        $movimientos = $this->apertura_actual
            ? $this->apertura_actual->movimientos()
                ->with(['usuarioResponsable', 'factura.cliente'])
                ->orderBy('fecha_movimiento', 'desc')
                ->orderBy('hora_movimiento', 'desc')
                ->paginate(15)
            : collect();

        // Calcular totales
        $total_ingresos = $this->apertura_actual ? $this->apertura_actual->total_ingresos : 0;
        $total_egresos = $this->apertura_actual ? $this->apertura_actual->total_egresos : 0;
        $saldo_actual = $this->apertura_actual ? $this->apertura_actual->saldo_actual : 0;

        return view('livewire.ventas.caja.movimientos', [
            'puntos_expedicion' => $puntos_expedicion,
            'movimientos' => $movimientos,
            'total_ingresos' => $total_ingresos,
            'total_egresos' => $total_egresos,
            'saldo_actual' => $saldo_actual,
        ]);
    }
}
