<?php

namespace App\Livewire\Compras\Recepciones;

use App\Models\Compras\Compra;
use App\Models\Compras\CompraRecepcion;
use App\Models\Compras\CompraRecepcionDetalle;
use App\Models\Empresa\Deposito;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    // Datos de la recepción
    public $compra_id;
    public $compra;
    public $deposito_id;
    public $fecha_recepcion;
    public $numero_remision;
    public $guia_transporte;
    public $fecha_remision;
    public $observaciones = '';

    // Búsqueda de compra
    public $search_compra = '';
    public $compras_encontradas = [];
    public $mostrar_busqueda_compra = false;

    // Detalles de recepción
    public $detalles = [];

    // Listas
    public $depositos = [];

    public function mount($compra_id = null)
    {
        $this->fecha_recepcion = now()->format('Y-m-d');
        $this->fecha_remision = now()->format('Y-m-d');

        $this->cargarDepositos();

        if ($compra_id) {
            $this->compra_id = $compra_id;
            $this->cargarCompra($compra_id);
        }
    }

    public function cargarDepositos()
    {
        $this->depositos = Deposito::where('activo', true)
            ->orderBy('nombre')
            ->get();
    }

    public function updatedSearchCompra()
    {
        if (strlen($this->search_compra) >= 2) {
            $this->compras_encontradas = Compra::with(['proveedor'])
                ->where(function ($query) {
                    $query->where('numero_factura', 'ilike', '%' . $this->search_compra . '%')
                        ->orWhereHas('proveedor', function ($q) {
                            $q->where('nombre', 'ilike', '%' . $this->search_compra . '%');
                        });
                })
                ->where('estado', '!=', 'ANULADA')
                ->where('estado', '!=', 'BORRADOR')
                ->limit(10)
                ->get();

            $this->mostrar_busqueda_compra = true;
        } else {
            $this->compras_encontradas = [];
            $this->mostrar_busqueda_compra = false;
        }
    }

    public function seleccionarCompra($compraId)
    {
        $this->cargarCompra($compraId);
        $this->search_compra = '';
        $this->compras_encontradas = [];
        $this->mostrar_busqueda_compra = false;
    }

    public function cargarCompra($compraId)
    {
        $this->compra = Compra::with(['proveedor', 'detalles.producto', 'deposito'])
            ->findOrFail($compraId);

        $this->compra_id = $this->compra->id;
        $this->deposito_id = $this->compra->deposito_id;

        // Cargar detalles de la compra
        $this->detalles = [];
        foreach ($this->compra->detalles as $detalle) {
            // Verificar cantidades ya recibidas
            $cantidadYaRecibida = CompraRecepcionDetalle::whereHas('recepcion', function ($q) use ($compraId) {
                $q->where('compra_id', $compraId)
                  ->where('estado', '!=', 'RECHAZADA');
            })
            ->where('compra_detalle_id', $detalle->id)
            ->sum('cantidad_recibida');

            $cantidadPendiente = $detalle->cantidad - $cantidadYaRecibida;

            if ($cantidadPendiente > 0) {
                $this->detalles[] = [
                    'compra_detalle_id' => $detalle->id,
                    'producto_id' => $detalle->producto_id,
                    'producto_nombre' => $detalle->producto->nombre,
                    'cantidad_esperada' => $cantidadPendiente,
                    'cantidad_recibida' => $cantidadPendiente,
                    'cantidad_aceptada' => $cantidadPendiente,
                    'cantidad_rechazada' => 0,
                    'lote' => '',
                    'fecha_vencimiento' => null,
                    'motivo_diferencia' => '',
                    'observaciones' => '',
                ];
            }
        }
    }

    public function actualizarCantidadRecibida($index)
    {
        if (isset($this->detalles[$index])) {
            $detalle = &$this->detalles[$index];

            // Por defecto, todo lo recibido es aceptado
            if ($detalle['cantidad_rechazada'] == 0) {
                $detalle['cantidad_aceptada'] = $detalle['cantidad_recibida'];
            } else {
                $detalle['cantidad_aceptada'] = $detalle['cantidad_recibida'] - $detalle['cantidad_rechazada'];
            }
        }
    }

    public function actualizarCantidadRechazada($index)
    {
        if (isset($this->detalles[$index])) {
            $detalle = &$this->detalles[$index];
            $detalle['cantidad_aceptada'] = $detalle['cantidad_recibida'] - $detalle['cantidad_rechazada'];
        }
    }

    public function guardar()
    {
        $this->validate([
            'compra_id' => 'required|exists:compras.COMPRAS,id',
            'deposito_id' => 'required|exists:empresa.DEPOSITOS,id',
            'fecha_recepcion' => 'required|date',
            'numero_remision' => 'nullable|string|max:50',
            'guia_transporte' => 'nullable|string|max:50',
            'fecha_remision' => 'nullable|date',
            'detalles' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Crear recepción
            $recepcion = CompraRecepcion::create([
                'compra_id' => $this->compra_id,
                'deposito_id' => $this->deposito_id,
                'fecha_recepcion' => $this->fecha_recepcion,
                'numero_remision' => $this->numero_remision,
                'guia_transporte' => $this->guia_transporte,
                'fecha_remision' => $this->fecha_remision,
                'observaciones' => $this->observaciones,
                'usuario_receptor_id' => Auth::id(),
                'estado' => 'PENDIENTE',
                'porcentaje_recibido' => 0,
                'creadoPor' => Auth::id(),
            ]);

            // Crear detalles de recepción
            $totalEsperado = 0;
            $totalRecibido = 0;

            foreach ($this->detalles as $detalle) {
                CompraRecepcionDetalle::create([
                    'recepcion_id' => $recepcion->id,
                    'compra_detalle_id' => $detalle['compra_detalle_id'],
                    'producto_id' => $detalle['producto_id'],
                    'cantidad_esperada' => $detalle['cantidad_esperada'],
                    'cantidad_recibida' => $detalle['cantidad_recibida'],
                    'cantidad_aceptada' => $detalle['cantidad_aceptada'],
                    'cantidad_rechazada' => $detalle['cantidad_rechazada'],
                    'diferencia' => $detalle['cantidad_recibida'] - $detalle['cantidad_esperada'],
                    'lote' => $detalle['lote'] ?? null,
                    'fecha_vencimiento' => $detalle['fecha_vencimiento'] ?? null,
                    'motivo_diferencia' => $detalle['motivo_diferencia'] ?? null,
                    'observaciones' => $detalle['observaciones'] ?? null,
                    'creadoPor' => Auth::id(),
                ]);

                $totalEsperado += $detalle['cantidad_esperada'];
                $totalRecibido += $detalle['cantidad_recibida'];
            }

            // Calcular porcentaje recibido
            $porcentajeRecibido = ($totalEsperado > 0) ? ($totalRecibido / $totalEsperado) * 100 : 0;

            // Determinar estado
            if ($porcentajeRecibido >= 100) {
                $estado = 'COMPLETA';
            } elseif ($porcentajeRecibido > 0) {
                $estado = 'PARCIAL';
            } else {
                $estado = 'PENDIENTE';
            }

            $recepcion->update([
                'porcentaje_recibido' => $porcentajeRecibido,
                'estado' => $estado,
            ]);

            DB::commit();

            session()->flash('success', 'Recepción registrada correctamente');
            return redirect()->route('compras.recepciones.show', $recepcion->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar la recepción: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.compras.recepciones.create');
    }
}
