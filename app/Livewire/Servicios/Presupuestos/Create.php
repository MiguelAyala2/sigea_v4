<?php

namespace App\Livewire\Servicios\Presupuestos;

use App\Models\Servicios\Diagnostico;
use App\Models\Servicios\Presupuesto;
use App\Models\Servicios\Promocion;
use App\Models\Servicios\Descuento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public $fecha_presupuesto;
    public $buscarCliente = '';
    public $diagnosticoSeleccionado = null;
    public $promocion_id = '';
    public $descuento_id = '';
    public $observaciones = '';

    // Datos autocompletados del diagnóstico
    public $diagnostico_codigo = '';
    public $solicitud_codigo = '';
    public $estado_diagnostico = '';
    public $cliente_nombre = '';
    public $equipo_nombre = '';
    public $problema_detectado = '';
    public $solucion_propuesta = '';

    public $presupuestoGuardado = false;
    public $codigoPresupuesto = '';

    // Cálculos
    public $subtotal_servicios = 0;
    public $descuento_promocion = 0;
    public $total_servicios = 0;
    public $subtotal_repuestos = 0;
    public $descuento_descuento = 0;
    public $total_repuestos = 0;
    public $monto_total = 0;

    public function mount()
    {
        $this->fecha_presupuesto = now()->format('Y-m-d');
    }

    public function seleccionarDiagnostico($diagnosticoId)
    {
        $diagnostico = Diagnostico::with([
            'recepcion.solicitud.cliente',
            'recepcion.producto',
            'tiposServicio'
        ])->findOrFail($diagnosticoId);

        $this->diagnosticoSeleccionado = $diagnostico->id;
        $this->diagnostico_codigo = $diagnostico->codigo;
        $this->solicitud_codigo = $diagnostico->recepcion->solicitud->codigo;
        $this->estado_diagnostico = $diagnostico->estado;
        $this->cliente_nombre = $diagnostico->recepcion->solicitud->cliente->nombre;
        $this->equipo_nombre = $diagnostico->recepcion->producto->nombre ?? 'N/A';
        $this->problema_detectado = $diagnostico->problema_detectado;
        $this->solucion_propuesta = $diagnostico->solucion_propuesta ?? '';

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
        if (!$this->diagnosticoSeleccionado) {
            return;
        }

        $diagnostico = Diagnostico::with([
            'tiposServicio',
            'repuestos'
        ])->findOrFail($this->diagnosticoSeleccionado);

        // Calcular subtotal de servicios
        $this->subtotal_servicios = $diagnostico->tiposServicio->sum(function ($servicio) {
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
        $this->subtotal_repuestos = $diagnostico->repuestos->sum(function ($repuesto) {
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

    public function guardar()
    {
        $this->validate([
            'fecha_presupuesto' => 'required|date',
            'diagnosticoSeleccionado' => 'required',
        ], [
            'diagnosticoSeleccionado.required' => 'Debe seleccionar un diagnóstico.',
        ]);

        // Validar que el diagnóstico exista
        if (!Diagnostico::find($this->diagnosticoSeleccionado)) {
            session()->flash('error', 'El diagnóstico seleccionado no existe.');
            return;
        }

        DB::beginTransaction();
        try {
            $presupuesto = Presupuesto::create([
                'codigo' => Presupuesto::generarCodigo(),
                'fecha_presupuesto' => $this->fecha_presupuesto,
                'diagnostico_id' => $this->diagnosticoSeleccionado,
                'promocion_id' => $this->promocion_id ?: null,
                'descuento_id' => $this->descuento_id ?: null,
                'subtotal_servicios' => $this->subtotal_servicios,
                'descuento_promocion' => $this->descuento_promocion,
                'total_servicios' => $this->total_servicios,
                'subtotal_repuestos' => $this->subtotal_repuestos,
                'descuento_descuento' => $this->descuento_descuento,
                'total_repuestos' => $this->total_repuestos,
                'monto_total' => $this->monto_total,
                'estado' => 'pendiente_aprobacion',
                'observaciones' => strtoupper($this->observaciones),
                'activo' => true,
                'creadoPor' => Auth::id(),
            ]);

            DB::commit();

            // Marcar como guardado y almacenar el código
            $this->presupuestoGuardado = true;
            $this->codigoPresupuesto = $presupuesto->codigo;

            session()->flash('success', 'Presupuesto ' . $presupuesto->codigo . ' registrado correctamente!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar el presupuesto: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $diagnosticos = [];
        if (strlen($this->buscarCliente) >= 2) {
            $busqueda = $this->buscarCliente;

            $diagnosticos = Diagnostico::with([
                'recepcion.solicitud.cliente',
                'recepcion.producto'
            ])
                ->where('estado', 'pendiente')
                ->where(function ($query) use ($busqueda) {
                    // Buscar por número de diagnóstico
                    $query->where('numero_diagnostico', 'ILIKE', '%' . $busqueda . '%')
                        // O buscar por nombre del cliente
                        ->orWhereHas('recepcion.solicitud.cliente', function ($q) use ($busqueda) {
                            $q->where('nombre', 'ILIKE', '%' . $busqueda . '%');
                        });
                })
                ->limit(10)
                ->get();
        }

        $promociones = Promocion::where('activo', true)->get();
        $descuentos = Descuento::where('activo', true)->get();

        return view('livewire.servicios.presupuestos.create', compact(
            'diagnosticos',
            'promociones',
            'descuentos'
        ));
    }
}
