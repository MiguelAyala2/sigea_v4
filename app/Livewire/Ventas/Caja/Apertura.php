<?php

namespace App\Livewire\Ventas\Caja;

use Livewire\Component;
use App\Models\Ventas\AperturaCaja;
use App\Models\Empresa\PuntoExpedicion;
use Illuminate\Support\Facades\DB;

class Apertura extends Component
{
    // Propiedades del formulario
    public $punto_expedicion_id;
    public $saldo_inicial = 0;
    public $observaciones;

    // Datos auxiliares
    public $apertura_actual;
    public $tiene_apertura_abierta = false;

    public function mount()
    {
        $this->verificarAperturaActual();
    }

    protected function rules()
    {
        return [
            'punto_expedicion_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('empresa.PUNTOS_EXPEDICION')
                        ->where('id', $value)
                        ->where('activo', true)
                        ->exists();

                    if (!$exists) {
                        $fail('El punto de expedición seleccionado no es válido.');
                    }

                    // Verificar que no tenga apertura abierta
                    if (AperturaCaja::tieneAperturaAbierta($value)) {
                        $fail('Este punto de expedición ya tiene una apertura de caja abierta.');
                    }
                },
            ],
            'saldo_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
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
    }

    public function abrirCaja()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            AperturaCaja::create([
                'punto_expedicion_id' => $this->punto_expedicion_id,
                'usuario_id' => auth()->user()->id,
                'fecha_apertura' => now()->toDateString(),
                'hora_apertura' => now()->toTimeString(),
                'saldo_inicial' => $this->saldo_inicial,
                'estado' => 'ABIERTA',
                'observaciones' => $this->observaciones,
                'activo' => true,
                'creadoPor' => auth()->user()->id,
            ]);

            DB::commit();

            session()->flash('success', "Caja abierta exitosamente. Saldo inicial: ₲ " . number_format($this->saldo_inicial, 0, ',', '.'));

            // Redirigir a movimientos de caja
            return redirect()->route('ventas.caja.movimientos');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al abrir la caja: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $puntos_expedicion = PuntoExpedicion::with('sucursal')
            ->where('activo', true)
            ->where('tipo', 'caja')
            ->orderBy('codigo')
            ->get();

        return view('livewire.ventas.caja.apertura', [
            'puntos_expedicion' => $puntos_expedicion,
        ]);
    }
}
