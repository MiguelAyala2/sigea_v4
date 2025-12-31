<?php

namespace App\Livewire\Ventas;

use App\Models\Ventas\NotaCredito;
use App\Models\Ventas\NotaCreditoDetalle;
use App\Models\Ventas\Factura;
use App\Models\Empresa\Timbrado;
use App\Models\Empresa\PuntoExpedicion;
use App\Models\Empresa\Deposito;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotaCreditoForm extends Component
{
    // Cabecera
    public $notaCreditoId;
    public $notaCredito;
    public $numero_nota;
    public $fecha_emision;
    public $factura_id;
    public $factura_seleccionada;
    public $cliente_id;
    public $timbrado_id;
    public $punto_expedicion_id;
    public $sucursal_id;
    public $deposito_id;
    public $motivo = '';
    public $observaciones = '';
    public $es_electronica = false;

    // Búsqueda de factura
    public $search_factura = '';
    public $facturas_encontradas = [];
    public $mostrar_busqueda_factura = false;

    // Detalles disponibles de la factura
    public $detalles_factura_disponibles = [];

    // Detalles seleccionados para la nota de crédito
    public $detalles_seleccionados = [];

    // Totales
    public $subtotal = 0;
    public $iva_10 = 0;
    public $iva_5 = 0;
    public $exenta = 0;
    public $total_iva = 0;
    public $total = 0;

    // Listas
    public $timbrados = [];
    public $puntos_expedicion = [];
    public $depositos = [];

    protected $rules = [
        'fecha_emision' => 'required|date',
        'factura_id' => 'required',
        'timbrado_id' => 'required',
        'punto_expedicion_id' => 'required',
        'deposito_id' => 'required',
        'motivo' => 'required|string|min:10',
    ];

    public function mount($notaCreditoId = null, $facturaId = null)
    {
        $this->notaCreditoId = $notaCreditoId;
        $this->factura_id = $facturaId;
        $this->fecha_emision = now()->format('Y-m-d');

        // Cargar listas
        $this->cargarListas();

        // Si hay nota de crédito, cargar datos
        if ($this->notaCreditoId) {
            $this->cargarNotaCredito();
        }

        // Si hay factura, cargarla
        if ($this->factura_id) {
            $this->cargarFactura($this->factura_id);
        }
    }

    public function cargarListas()
    {
        $this->timbrados = Timbrado::where('activo', true)
            ->vigentes()
            ->orderBy('fecha_inicio_vigencia', 'desc')
            ->get();

        $this->puntos_expedicion = PuntoExpedicion::where('activo', true)
            ->with('sucursal')
            ->orderBy('nombre')
            ->get();

        $this->depositos = Deposito::where('activo', true)
            ->orderBy('nombre')
            ->get();

        // Seleccionar primer timbrado activo
        if ($this->timbrados->count() > 0 && !$this->timbrado_id) {
            $this->timbrado_id = $this->timbrados->first()->id;
        }

        // Seleccionar primer punto de expedición activo
        if ($this->puntos_expedicion->count() > 0 && !$this->punto_expedicion_id) {
            $this->punto_expedicion_id = $this->puntos_expedicion->first()->id;
            $this->sucursal_id = $this->puntos_expedicion->first()->sucursal_id;
        }

        // Seleccionar primer depósito activo
        if ($this->depositos->count() > 0 && !$this->deposito_id) {
            $this->deposito_id = $this->depositos->first()->id;
        }
    }

    public function cargarNotaCredito()
    {
        $this->notaCredito = NotaCredito::with(['factura', 'cliente', 'detalles.producto'])
            ->findOrFail($this->notaCreditoId);

        $this->numero_nota = $this->notaCredito->numero_nota;
        $this->fecha_emision = $this->notaCredito->fecha_emision->format('Y-m-d');
        $this->factura_id = $this->notaCredito->factura_id;
        $this->cliente_id = $this->notaCredito->cliente_id;
        $this->timbrado_id = $this->notaCredito->timbrado_id;
        $this->punto_expedicion_id = $this->notaCredito->punto_expedicion_id;
        $this->sucursal_id = $this->notaCredito->sucursal_id;
        $this->deposito_id = $this->notaCredito->deposito_id;
        $this->motivo = $this->notaCredito->motivo;
        $this->observaciones = $this->notaCredito->observaciones ?? '';
        $this->es_electronica = $this->notaCredito->es_electronica;

        // Cargar factura
        $this->cargarFactura($this->factura_id);

        // Cargar detalles de la nota de crédito
        foreach ($this->notaCredito->detalles as $detalle) {
            $this->detalles_seleccionados[] = [
                'id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $detalle->producto->nombre ?? '',
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'descuento_porcentaje' => $detalle->descuento_porcentaje,
                'iva_porcentaje' => $detalle->iva_porcentaje,
            ];
        }

        $this->calcularTotales();
    }

    public function buscarFactura()
    {
        if (strlen($this->search_factura) < 2) {
            $this->facturas_encontradas = [];
            $this->mostrar_busqueda_factura = false;
            return;
        }

        $this->facturas_encontradas = Factura::with(['cliente', 'puntoExpedicion'])
            ->where('estado', 'EMITIDA')
            ->where(function ($query) {
                $query->where('numero_factura', 'ilike', '%' . $this->search_factura . '%')
                    ->orWhereHas('cliente', function ($q) {
                        $q->where('nombre', 'ilike', '%' . $this->search_factura . '%')
                            ->orWhere('documento', 'ilike', '%' . $this->search_factura . '%');
                    });
            })
            ->orderBy('fecha_emision', 'desc')
            ->limit(10)
            ->get();

        $this->mostrar_busqueda_factura = true;
    }

    public function seleccionarFactura($facturaId)
    {
        $this->cargarFactura($facturaId);
        $this->mostrar_busqueda_factura = false;
        $this->search_factura = '';
    }

    public function cargarFactura($facturaId)
    {
        $this->factura_seleccionada = Factura::with(['cliente', 'detalles.producto', 'puntoExpedicion.sucursal'])
            ->findOrFail($facturaId);

        $this->factura_id = $this->factura_seleccionada->id;
        $this->cliente_id = $this->factura_seleccionada->cliente_id;
        $this->sucursal_id = $this->factura_seleccionada->sucursal_id;
        $this->deposito_id = $this->factura_seleccionada->deposito_id;
        $this->punto_expedicion_id = $this->factura_seleccionada->punto_expedicion_id;

        // Cargar detalles disponibles
        $this->detalles_factura_disponibles = [];
        foreach ($this->factura_seleccionada->detalles as $detalle) {
            $this->detalles_factura_disponibles[] = [
                'id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $detalle->producto->nombre ?? '',
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'descuento_porcentaje' => $detalle->descuento_porcentaje,
                'iva_porcentaje' => $detalle->iva_porcentaje,
                'seleccionado' => false,
                'cantidad_nc' => 0,
            ];
        }
    }

    public function toggleDetalleFactura($index)
    {
        $this->detalles_factura_disponibles[$index]['seleccionado'] = !$this->detalles_factura_disponibles[$index]['seleccionado'];

        if ($this->detalles_factura_disponibles[$index]['seleccionado']) {
            $this->detalles_factura_disponibles[$index]['cantidad_nc'] = $this->detalles_factura_disponibles[$index]['cantidad'];
        } else {
            $this->detalles_factura_disponibles[$index]['cantidad_nc'] = 0;
        }

        $this->actualizarDetallesSeleccionados();
    }

    public function actualizarCantidadNC($index)
    {
        $detalle = $this->detalles_factura_disponibles[$index];

        if ($detalle['cantidad_nc'] > $detalle['cantidad']) {
            $this->detalles_factura_disponibles[$index]['cantidad_nc'] = $detalle['cantidad'];
            session()->flash('error', 'La cantidad no puede ser mayor a la cantidad facturada');
        }

        if ($detalle['cantidad_nc'] < 0) {
            $this->detalles_factura_disponibles[$index]['cantidad_nc'] = 0;
        }

        $this->actualizarDetallesSeleccionados();
    }

    public function actualizarDetallesSeleccionados()
    {
        $this->detalles_seleccionados = [];

        foreach ($this->detalles_factura_disponibles as $detalle) {
            if ($detalle['seleccionado'] && $detalle['cantidad_nc'] > 0) {
                $this->detalles_seleccionados[] = [
                    'factura_detalle_id' => $detalle['id'],
                    'producto_id' => $detalle['producto_id'],
                    'producto_nombre' => $detalle['producto_nombre'],
                    'cantidad' => $detalle['cantidad_nc'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'descuento_porcentaje' => $detalle['descuento_porcentaje'],
                    'iva_porcentaje' => $detalle['iva_porcentaje'],
                ];
            }
        }

        $this->calcularTotales();
    }

    public function calcularTotales()
    {
        $this->subtotal = 0;
        $this->iva_10 = 0;
        $this->iva_5 = 0;
        $this->exenta = 0;

        foreach ($this->detalles_seleccionados as $detalle) {
            $cantidad = $detalle['cantidad'];
            $precio_unitario = $detalle['precio_unitario'];
            $descuento_porcentaje = $detalle['descuento_porcentaje'];
            $iva_porcentaje = $detalle['iva_porcentaje'];

            // Calcular subtotal del detalle
            $subtotal_detalle = $cantidad * $precio_unitario;

            // Aplicar descuento
            $descuento_monto = $subtotal_detalle * ($descuento_porcentaje / 100);
            $subtotal_detalle = $subtotal_detalle - $descuento_monto;

            // Calcular IVA
            $iva_monto = $subtotal_detalle * ($iva_porcentaje / 100);

            $this->subtotal += $subtotal_detalle;

            if ($iva_porcentaje == 10) {
                $this->iva_10 += $iva_monto;
            } elseif ($iva_porcentaje == 5) {
                $this->iva_5 += $iva_monto;
            } else {
                $this->exenta += $subtotal_detalle;
            }
        }

        $this->total_iva = $this->iva_10 + $this->iva_5;
        $this->total = $this->subtotal + $this->total_iva;
    }

    public function guardar()
    {
        $this->validate();

        if (count($this->detalles_seleccionados) == 0) {
            session()->flash('error', 'Debe seleccionar al menos un detalle de la factura');
            return;
        }

        DB::beginTransaction();

        try {
            $timbrado = Timbrado::findOrFail($this->timbrado_id);

            if ($this->notaCreditoId) {
                // Actualizar nota de crédito existente
                $notaCredito = NotaCredito::findOrFail($this->notaCreditoId);
                $notaCredito->update([
                    'motivo' => $this->motivo,
                    'observaciones' => $this->observaciones,
                    'actualizado_por' => Auth::id(),
                ]);
            } else {
                // Crear nueva nota de crédito
                $numeroSecuencial = $timbrado->obtenerSiguienteNumero();
                $numero_nota = str_pad($numeroSecuencial, 7, '0', STR_PAD_LEFT);

                $notaCredito = NotaCredito::create([
                    'numero_nota' => $numero_nota,
                    'fecha_emision' => $this->fecha_emision,
                    'factura_id' => $this->factura_id,
                    'cliente_id' => $this->cliente_id,
                    'timbrado_id' => $this->timbrado_id,
                    'punto_expedicion_id' => $this->punto_expedicion_id,
                    'numero_timbrado' => $timbrado->numero_timbrado,
                    'sucursal_id' => $this->sucursal_id,
                    'deposito_id' => $this->deposito_id,
                    'motivo' => $this->motivo,
                    'subtotal' => $this->subtotal,
                    'iva_10' => $this->iva_10,
                    'iva_5' => $this->iva_5,
                    'exenta' => $this->exenta,
                    'total_iva' => $this->total_iva,
                    'total' => $this->total,
                    'es_electronica' => $this->es_electronica,
                    'estado' => 'BORRADOR',
                    'observaciones' => $this->observaciones,
                    'creado_por' => Auth::id(),
                    'activo' => true,
                ]);

                // Guardar detalles
                foreach ($this->detalles_seleccionados as $detalle) {
                    $cantidad = $detalle['cantidad'];
                    $precio_unitario = $detalle['precio_unitario'];
                    $descuento_porcentaje = $detalle['descuento_porcentaje'];
                    $iva_porcentaje = $detalle['iva_porcentaje'];

                    $subtotal_detalle = $cantidad * $precio_unitario;
                    $descuento_monto = $subtotal_detalle * ($descuento_porcentaje / 100);
                    $subtotal_detalle = $subtotal_detalle - $descuento_monto;
                    $iva_monto = $subtotal_detalle * ($iva_porcentaje / 100);
                    $total_detalle = $subtotal_detalle + $iva_monto;

                    NotaCreditoDetalle::create([
                        'nota_credito_id' => $notaCredito->id,
                        'factura_detalle_id' => $detalle['factura_detalle_id'] ?? null,
                        'producto_id' => $detalle['producto_id'],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio_unitario,
                        'descuento_porcentaje' => $descuento_porcentaje,
                        'descuento_monto' => $descuento_monto,
                        'subtotal' => $subtotal_detalle,
                        'iva_porcentaje' => $iva_porcentaje,
                        'iva_monto' => $iva_monto,
                        'total' => $total_detalle,
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', 'Nota de crédito guardada correctamente');
            return redirect()->route('ventas.notas-credito.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ventas.nota-credito-form');
    }
}
