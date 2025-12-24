<?php

namespace App\Livewire\Servicios\Presupuestos;

use App\Models\Servicios\Presupuesto;
use App\Models\Servicios\Promocion;
use App\Models\Servicios\Descuento;
use App\Models\Servicios\Diagnostico;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{
    public $presupuestoId;
    public $codigo;
    public $fecha_presupuesto;
    public $promocion_id = '';
    public $descuento_id = '';
    public $observaciones = '';

    // Datos del diagnóstico
    public $diagnostico_codigo = '';
    public $solicitud_codigo = '';
    public $estado_diagnostico = '';
    public $cliente_nombre = '';
    public $equipo_nombre = '';
    public $problema_detectado = '';
    public $solucion_propuesta = '';

    // Cálculos
    public $subtotal_servicios = 0;
    public $descuento_promocion = 0;
    public $total_servicios = 0;
    public $subtotal_repuestos = 0;
    public $descuento_descuento = 0;
    public $total_repuestos = 0;
    public $monto_total = 0;

    public $diagnostico;

    public function mount($id)
    {
        $presupuesto = Presupuesto::with([
            'diagnostico.recepcion.solicitud.cliente',
            'diagnostico.recepcion.producto',
            'diagnostico.tiposServicio.tipoServicio',
            'diagnostico.repuestos.producto'
        ])->findOrFail($id);

        $this->presupuestoId = $presupuesto->id;
        $this->codigo = $presupuesto->codigo;
        $this->fecha_presupuesto = $presupuesto->fecha_presupuesto->format('Y-m-d');
        $this->promocion_id = $presupuesto->promocion_id ?? '';
        $this->descuento_id = $presupuesto->descuento_id ?? '';
        $this->observaciones = $presupuesto->observaciones;

        // Cargar datos del diagnóstico
        $this->diagnostico = $presupuesto->diagnostico;
        $this->diagnostico_codigo = $this->diagnostico->codigo;
        $this->solicitud_codigo = $this->diagnostico->recepcion->solicitud->codigo;
        $this->estado_diagnostico = $this->diagnostico->estado;
        $this->cliente_nombre = $this->diagnostico->recepcion->solicitud->cliente->nombre;
        $this->equipo_nombre = $this->diagnostico->recepcion->producto->nombre ?? 'N/A';
        $this->problema_detectado = $this->diagnostico->problema_detectado;
        $this->solucion_propuesta = $this->diagnostico->solucion_propuesta ?? '';

        $this->calcularTotales();
    }

    public function updatedPromocionId()
    {
        $this->calcularTotales();
    }

    public function updatedDescuentoId()
    {
        $this->calcularTotales();
    }

    public function calcularTotales()
    {
        // Calcular subtotal de servicios
        $this->subtotal_servicios = $this->diagnostico->tiposServicio->sum(function ($servicio) {
            return $servicio->cantidad * $servicio->costo_unitario;
        });

        // Aplicar promoción si existe
        $this->descuento_promocion = 0;
        if ($this->promocion_id) {
            $promocion = Promocion::find($this->promocion_id);
            if ($promocion && $promocion->activo) {
                $this->descuento_promocion = ($this->subtotal_servicios * $promocion->descuento) / 100;
            }
        }

        $this->total_servicios = $this->subtotal_servicios - $this->descuento_promocion;

        // Calcular subtotal de repuestos
        $this->subtotal_repuestos = $this->diagnostico->repuestos->sum(function ($repuesto) {
            return $repuesto->cantidad * $repuesto->costo;
        });

        // Aplicar descuento si existe
        $this->descuento_descuento = 0;
        if ($this->descuento_id) {
            $descuento = Descuento::find($this->descuento_id);
            if ($descuento && $descuento->activo) {
                if ($descuento->tipo_descuento === 'porcentaje') {
                    $this->descuento_descuento = ($this->subtotal_repuestos * $descuento->valor) / 100;
                } else {
                    $this->descuento_descuento = $descuento->valor;
                }
            }
        }

        $this->total_repuestos = $this->subtotal_repuestos - $this->descuento_descuento;

        $this->monto_total = $this->total_servicios + $this->total_repuestos;
    }

    public function actualizar()
    {
        $this->validate([
            'fecha_presupuesto' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $presupuesto = Presupuesto::findOrFail($this->presupuestoId);

            $presupuesto->update([
                'fecha_presupuesto' => $this->fecha_presupuesto,
                'promocion_id' => $this->promocion_id ?: null,
                'descuento_id' => $this->descuento_id ?: null,
                'subtotal_servicios' => $this->subtotal_servicios,
                'descuento_promocion' => $this->descuento_promocion,
                'total_servicios' => $this->total_servicios,
                'subtotal_repuestos' => $this->subtotal_repuestos,
                'descuento_descuento' => $this->descuento_descuento,
                'total_repuestos' => $this->total_repuestos,
                'monto_total' => $this->monto_total,
                'observaciones' => strtoupper($this->observaciones),
                'actualizadoPor' => Auth::id(),
            ]);

            DB::commit();
            session()->flash('success', 'Presupuesto actualizado correctamente!');
            $this->redirectRoute('servicios.presupuestos.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar el presupuesto: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $promociones = Promocion::where('activo', true)->get();
        $descuentos = Descuento::where('activo', true)->get();

        return view('livewire.servicios.presupuestos.edit', compact('promociones', 'descuentos'));
    }
}
