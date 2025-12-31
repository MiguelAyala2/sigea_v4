<?php

namespace App\Livewire\Ventas\Cobranzas;

use App\Models\Ventas\CuentaPorCobrar;
use App\Models\Ventas\MovimientoCaja;
use App\Models\Ventas\AperturaCaja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RegistrarCobranza extends Component
{
    // Búsqueda de cuenta por cobrar
    public $search_cuenta = '';
    public $mostrar_busqueda = false;
    public $cuentas_encontradas = [];

    // Cuenta seleccionada
    public $cuenta_seleccionada = null;
    public $factura_numero = '';
    public $cliente_nombre = '';
    public $saldo_pendiente = 0;

    // Datos de la cobranza
    public $fecha_cobranza;
    public $forma_pago = '';
    public $monto_cobrado = 0;
    public $referencia = '';
    public $comprobante_numero = '';
    public $observaciones = '';

    protected $rules = [
        'cuenta_seleccionada' => 'required',
        'fecha_cobranza' => 'required|date',
        'forma_pago' => 'required|in:EFECTIVO,CHEQUE,TARJETA_DEBITO,TARJETA_CREDITO,TRANSFERENCIA,QR,OTRO',
        'monto_cobrado' => 'required|numeric|min:0.01',
        'referencia' => 'nullable|string|max:255',
        'comprobante_numero' => 'nullable|string|max:100',
        'observaciones' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'cuenta_seleccionada.required' => 'Debe seleccionar una cuenta por cobrar.',
        'fecha_cobranza.required' => 'La fecha de cobranza es obligatoria.',
        'forma_pago.required' => 'Debe seleccionar una forma de pago.',
        'monto_cobrado.required' => 'El monto cobrado es obligatorio.',
        'monto_cobrado.min' => 'El monto debe ser mayor a cero.',
    ];

    public function mount()
    {
        $this->fecha_cobranza = now()->format('Y-m-d');
    }

    public function updatedSearchCuenta()
    {
        if (strlen($this->search_cuenta) >= 2) {
            $this->cuentas_encontradas = CuentaPorCobrar::with(['factura', 'cliente'])
                ->where(function ($query) {
                    $query->where('numero_factura', 'ilike', '%' . $this->search_cuenta . '%')
                        ->orWhereHas('cliente', function ($q) {
                            $q->where('nombre', 'ilike', '%' . $this->search_cuenta . '%');
                        });
                })
                ->whereIn('estado', ['PENDIENTE', 'PARCIALMENTE_PAGADA'])
                ->where('saldo_pendiente', '>', 0)
                ->limit(10)
                ->get();

            $this->mostrar_busqueda = true;
        } else {
            $this->cuentas_encontradas = [];
            $this->mostrar_busqueda = false;
        }
    }

    public function seleccionarCuenta($cuentaId)
    {
        $cuenta = CuentaPorCobrar::with(['factura', 'cliente'])->find($cuentaId);

        if ($cuenta) {
            $this->cuenta_seleccionada = $cuenta->id;
            $this->factura_numero = $cuenta->numero_factura;
            $this->cliente_nombre = $cuenta->cliente->nombre ?? 'N/A';
            $this->saldo_pendiente = $cuenta->saldo_pendiente;
            $this->monto_cobrado = $cuenta->saldo_pendiente; // Prellenar con el saldo total
            $this->mostrar_busqueda = false;
            $this->search_cuenta = $cuenta->numero_factura . ' - ' . $this->cliente_nombre;
        }
    }

    public function limpiarSeleccion()
    {
        $this->reset([
            'cuenta_seleccionada',
            'factura_numero',
            'cliente_nombre',
            'saldo_pendiente',
            'search_cuenta',
            'monto_cobrado',
            'forma_pago',
            'referencia',
            'comprobante_numero',
            'observaciones'
        ]);
    }

    public function guardar()
    {
        $this->validate();

        // Validar que el monto no exceda el saldo pendiente
        if ($this->monto_cobrado > $this->saldo_pendiente) {
            session()->flash('error', 'El monto cobrado no puede ser mayor al saldo pendiente.');
            return;
        }

        // Verificar que haya una caja abierta
        $aperturaActiva = AperturaCaja::where('usuario_responsable_id', Auth::id())
            ->where('estado', 'ABIERTA')
            ->first();

        if (!$aperturaActiva) {
            session()->flash('error', 'No hay una caja abierta. Debe abrir caja antes de registrar cobranzas.');
            return;
        }

        DB::beginTransaction();
        try {
            // Buscar la cuenta
            $cuenta = CuentaPorCobrar::findOrFail($this->cuenta_seleccionada);

            // Registrar el movimiento de caja
            $movimiento = MovimientoCaja::create([
                'apertura_caja_id' => $aperturaActiva->id,
                'usuario_responsable_id' => Auth::id(),
                'tipo_movimiento' => 'INGRESO',
                'concepto' => 'Cobranza de Factura ' . $cuenta->numero_factura,
                'monto' => $this->monto_cobrado,
                'forma_pago' => $this->forma_pago,
                'comprobante_numero' => $this->comprobante_numero,
                'referencia' => $this->referencia,
                'fecha_movimiento' => $this->fecha_cobranza,
                'hora_movimiento' => now()->format('H:i:s'),
                'venta_id' => $cuenta->factura_id,
                'activo' => true,
                'creadoPor' => Auth::id(),
            ]);

            // Actualizar la cuenta por cobrar
            $cuenta->registrarPago($this->monto_cobrado);

            DB::commit();

            session()->flash('success', 'Cobranza registrada exitosamente. Monto: ₲ ' . number_format($this->monto_cobrado, 0, ',', '.'));

            // Limpiar el formulario
            $this->limpiarSeleccion();
            $this->fecha_cobranza = now()->format('Y-m-d');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar la cobranza: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ventas.cobranzas.registrar-cobranza');
    }
}
