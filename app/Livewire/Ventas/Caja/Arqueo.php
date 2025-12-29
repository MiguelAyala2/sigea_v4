<?php

namespace App\Livewire\Ventas\Caja;

use Livewire\Component;
use App\Models\Ventas\AperturaCaja;
use App\Models\Ventas\ArqueoCaja;
use App\Models\Empresa\PuntoExpedicion;
use Illuminate\Support\Facades\DB;

class Arqueo extends Component
{
    // Propiedades del formulario
    public $punto_expedicion_id;

    // Billetes
    public $cantidad_billetes_100000 = 0;
    public $cantidad_billetes_50000 = 0;
    public $cantidad_billetes_20000 = 0;
    public $cantidad_billetes_10000 = 0;
    public $cantidad_billetes_5000 = 0;
    public $cantidad_billetes_2000 = 0;

    // Monedas
    public $cantidad_monedas_1000 = 0;
    public $cantidad_monedas_500 = 0;
    public $cantidad_monedas_100 = 0;
    public $cantidad_monedas_50 = 0;

    public $observaciones;

    // Datos auxiliares
    public $apertura_actual;
    public $tiene_apertura_abierta = false;
    public $arqueo_existente;

    // Totales calculados
    public $subtotal_billetes = 0;
    public $subtotal_monedas = 0;
    public $total_arqueo = 0;

    public function mount()
    {
        $this->verificarAperturaActual();
    }

    protected function rules()
    {
        return [
            'cantidad_billetes_100000' => 'required|integer|min:0',
            'cantidad_billetes_50000' => 'required|integer|min:0',
            'cantidad_billetes_20000' => 'required|integer|min:0',
            'cantidad_billetes_10000' => 'required|integer|min:0',
            'cantidad_billetes_5000' => 'required|integer|min:0',
            'cantidad_billetes_2000' => 'required|integer|min:0',
            'cantidad_monedas_1000' => 'required|integer|min:0',
            'cantidad_monedas_500' => 'required|integer|min:0',
            'cantidad_monedas_100' => 'required|integer|min:0',
            'cantidad_monedas_50' => 'required|integer|min:0',
            'observaciones' => 'nullable|string|max:500',
        ];
    }

    public function verificarAperturaActual()
    {
        if ($this->punto_expedicion_id) {
            $this->apertura_actual = AperturaCaja::obtenerAperturaActual($this->punto_expedicion_id);

            if ($this->apertura_actual) {
                $this->apertura_actual->load(['usuario']);
            }

            $this->tiene_apertura_abierta = $this->apertura_actual !== null;

            if ($this->tiene_apertura_abierta) {
                $this->verificarArqueoExistente();
            }
        }
    }

    public function updatedPuntoExpedicionId()
    {
        $this->verificarAperturaActual();
    }

    protected function verificarArqueoExistente()
    {
        if (!$this->apertura_actual) {
            return;
        }

        $this->arqueo_existente = ArqueoCaja::where('apertura_caja_id', $this->apertura_actual->id)
            ->where('activo', true)
            ->first();

        if ($this->arqueo_existente) {
            $this->cargarArqueoExistente();
        }
    }

    protected function cargarArqueoExistente()
    {
        $this->cantidad_billetes_100000 = $this->arqueo_existente->cantidad_billetes_100000;
        $this->cantidad_billetes_50000 = $this->arqueo_existente->cantidad_billetes_50000;
        $this->cantidad_billetes_20000 = $this->arqueo_existente->cantidad_billetes_20000;
        $this->cantidad_billetes_10000 = $this->arqueo_existente->cantidad_billetes_10000;
        $this->cantidad_billetes_5000 = $this->arqueo_existente->cantidad_billetes_5000;
        $this->cantidad_billetes_2000 = $this->arqueo_existente->cantidad_billetes_2000;
        $this->cantidad_monedas_1000 = $this->arqueo_existente->cantidad_monedas_1000;
        $this->cantidad_monedas_500 = $this->arqueo_existente->cantidad_monedas_500;
        $this->cantidad_monedas_100 = $this->arqueo_existente->cantidad_monedas_100;
        $this->cantidad_monedas_50 = $this->arqueo_existente->cantidad_monedas_50;
        $this->observaciones = $this->arqueo_existente->observaciones;

        $this->calcularTotales();
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'cantidad_')) {
            $this->calcularTotales();
        }
    }

    protected function calcularTotales()
    {
        $this->subtotal_billetes =
            ($this->cantidad_billetes_100000 * 100000) +
            ($this->cantidad_billetes_50000 * 50000) +
            ($this->cantidad_billetes_20000 * 20000) +
            ($this->cantidad_billetes_10000 * 10000) +
            ($this->cantidad_billetes_5000 * 5000) +
            ($this->cantidad_billetes_2000 * 2000);

        $this->subtotal_monedas =
            ($this->cantidad_monedas_1000 * 1000) +
            ($this->cantidad_monedas_500 * 500) +
            ($this->cantidad_monedas_100 * 100) +
            ($this->cantidad_monedas_50 * 50);

        $this->total_arqueo = $this->subtotal_billetes + $this->subtotal_monedas;
    }

    public function limpiarFormulario()
    {
        $this->cantidad_billetes_100000 = 0;
        $this->cantidad_billetes_50000 = 0;
        $this->cantidad_billetes_20000 = 0;
        $this->cantidad_billetes_10000 = 0;
        $this->cantidad_billetes_5000 = 0;
        $this->cantidad_billetes_2000 = 0;
        $this->cantidad_monedas_1000 = 0;
        $this->cantidad_monedas_500 = 0;
        $this->cantidad_monedas_100 = 0;
        $this->cantidad_monedas_50 = 0;
        $this->observaciones = '';
        $this->calcularTotales();
    }

    public function guardarArqueo()
    {
        if (!$this->tiene_apertura_abierta) {
            session()->flash('error', 'No hay una caja abierta para realizar el arqueo.');
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            $datos = [
                'apertura_caja_id' => $this->apertura_actual->id,
                'fecha_arqueo' => now()->toDateString(),
                'hora_arqueo' => now()->toTimeString(),
                'cantidad_billetes_100000' => $this->cantidad_billetes_100000,
                'cantidad_billetes_50000' => $this->cantidad_billetes_50000,
                'cantidad_billetes_20000' => $this->cantidad_billetes_20000,
                'cantidad_billetes_10000' => $this->cantidad_billetes_10000,
                'cantidad_billetes_5000' => $this->cantidad_billetes_5000,
                'cantidad_billetes_2000' => $this->cantidad_billetes_2000,
                'cantidad_monedas_1000' => $this->cantidad_monedas_1000,
                'cantidad_monedas_500' => $this->cantidad_monedas_500,
                'cantidad_monedas_100' => $this->cantidad_monedas_100,
                'cantidad_monedas_50' => $this->cantidad_monedas_50,
                'subtotal_billetes' => $this->subtotal_billetes,
                'subtotal_monedas' => $this->subtotal_monedas,
                'total_arqueo' => $this->total_arqueo,
                'observaciones' => $this->observaciones,
                'activo' => true,
                'actualizadoPor' => auth()->user()->id,
            ];

            if ($this->arqueo_existente) {
                // Actualizar arqueo existente
                $this->arqueo_existente->update($datos);
                $mensaje = 'Arqueo actualizado exitosamente.';
            } else {
                // Crear nuevo arqueo
                $datos['usuario_id'] = auth()->user()->id;
                $datos['creadoPor'] = auth()->user()->id;
                ArqueoCaja::create($datos);
                $mensaje = 'Arqueo registrado exitosamente.';
            }

            DB::commit();

            session()->flash('success', $mensaje . ' Total: ₲ ' . number_format($this->total_arqueo, 0, ',', '.'));

            $this->verificarArqueoExistente();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar el arqueo: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $puntos_expedicion = PuntoExpedicion::with('sucursal')
            ->where('activo', true)
            ->where('tipo', 'caja')
            ->orderBy('codigo')
            ->get();

        return view('livewire.ventas.caja.arqueo', [
            'puntos_expedicion' => $puntos_expedicion,
        ]);
    }
}
