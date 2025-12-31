<?php

namespace App\Livewire\Ventas;

use App\Models\Ventas\NotaDebito;
use App\Models\Ventas\NotaDebitoDetalle;
use App\Models\Ventas\Factura;
use App\Models\Stock\Producto;
use App\Models\Empresa\Timbrado;
use App\Models\Empresa\PuntoExpedicion;
use App\Models\Empresa\Deposito;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotaDebitoForm extends Component
{
    // Cabecera
    public $notaDebitoId;
    public $notaDebito;
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

    // Búsqueda de productos para conceptos
    public $search_producto = '';
    public $productos_encontrados = [];
    public $mostrar_busqueda_producto = false;

    // Detalles/conceptos de la nota de débito
    public $detalles = [];
    public $detalle_temp = [
        'producto_id' => null,
        'producto_nombre' => '',
        'descripcion_adicional' => '',
        'cantidad' => 1,
        'precio_unitario' => 0,
        'iva_porcentaje' => 10,
    ];

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

    public function mount($notaDebitoId = null, $facturaId = null)
    {
        $this->notaDebitoId = $notaDebitoId;
        $this->factura_id = $facturaId;
        $this->fecha_emision = now()->format('Y-m-d');

        // Cargar listas
        $this->cargarListas();

        // Si hay nota de débito, cargar datos
        if ($this->notaDebitoId) {
            $this->cargarNotaDebito();
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

    public function cargarNotaDebito()
    {
        $this->notaDebito = NotaDebito::with(['factura', 'cliente', 'detalles.producto'])
            ->findOrFail($this->notaDebitoId);

        $this->numero_nota = $this->notaDebito->numero_nota;
        $this->fecha_emision = $this->notaDebito->fecha_emision->format('Y-m-d');
        $this->factura_id = $this->notaDebito->factura_id;
        $this->cliente_id = $this->notaDebito->cliente_id;
        $this->timbrado_id = $this->notaDebito->timbrado_id;
        $this->punto_expedicion_id = $this->notaDebito->punto_expedicion_id;
        $this->sucursal_id = $this->notaDebito->sucursal_id;
        $this->deposito_id = $this->notaDebito->deposito_id;
        $this->motivo = $this->notaDebito->motivo;
        $this->observaciones = $this->notaDebito->observaciones ?? '';
        $this->es_electronica = $this->notaDebito->es_electronica;

        // Cargar factura
        $this->cargarFactura($this->factura_id);

        // Cargar detalles de la nota de débito
        foreach ($this->notaDebito->detalles as $detalle) {
            $this->detalles[] = [
                'id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $detalle->producto->nombre ?? '',
                'descripcion_adicional' => $detalle->descripcion_adicional,
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
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
        $this->factura_seleccionada = Factura::with(['cliente', 'puntoExpedicion.sucursal'])
            ->findOrFail($facturaId);

        $this->factura_id = $this->factura_seleccionada->id;
        $this->cliente_id = $this->factura_seleccionada->cliente_id;
        $this->sucursal_id = $this->factura_seleccionada->sucursal_id;
        $this->deposito_id = $this->factura_seleccionada->deposito_id;
        $this->punto_expedicion_id = $this->factura_seleccionada->punto_expedicion_id;
    }

    public function buscarProducto()
    {
        if (strlen($this->search_producto) < 2) {
            $this->productos_encontrados = [];
            $this->mostrar_busqueda_producto = false;
            return;
        }

        $this->productos_encontrados = Producto::where('activo', true)
            ->where(function ($query) {
                $query->where('nombre', 'ilike', '%' . $this->search_producto . '%')
                    ->orWhere('codigo_barra', 'ilike', '%' . $this->search_producto . '%');
            })
            ->limit(10)
            ->get();

        $this->mostrar_busqueda_producto = true;
    }

    public function seleccionarProducto($productoId)
    {
        $producto = Producto::find($productoId);

        if ($producto) {
            $this->detalle_temp['producto_id'] = $producto->id;
            $this->detalle_temp['producto_nombre'] = $producto->nombre;
            $this->detalle_temp['precio_unitario'] = $producto->precio_venta ?? 0;
            $this->detalle_temp['iva_porcentaje'] = $producto->tipo_iva == 'IVA_10' ? 10 : ($producto->tipo_iva == 'IVA_5' ? 5 : 0);
        }

        $this->mostrar_busqueda_producto = false;
        $this->search_producto = '';
    }

    public function agregarDetalle()
    {
        if (!$this->detalle_temp['producto_id']) {
            session()->flash('error', 'Debe seleccionar un producto');
            return;
        }

        if ($this->detalle_temp['cantidad'] <= 0) {
            session()->flash('error', 'La cantidad debe ser mayor a 0');
            return;
        }

        if ($this->detalle_temp['precio_unitario'] <= 0) {
            session()->flash('error', 'El precio unitario debe ser mayor a 0');
            return;
        }

        $this->detalles[] = [
            'producto_id' => $this->detalle_temp['producto_id'],
            'producto_nombre' => $this->detalle_temp['producto_nombre'],
            'descripcion_adicional' => $this->detalle_temp['descripcion_adicional'],
            'cantidad' => $this->detalle_temp['cantidad'],
            'precio_unitario' => $this->detalle_temp['precio_unitario'],
            'iva_porcentaje' => $this->detalle_temp['iva_porcentaje'],
        ];

        // Limpiar detalle temporal
        $this->detalle_temp = [
            'producto_id' => null,
            'producto_nombre' => '',
            'descripcion_adicional' => '',
            'cantidad' => 1,
            'precio_unitario' => 0,
            'iva_porcentaje' => 10,
        ];

        $this->calcularTotales();
    }

    public function eliminarDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
        $this->calcularTotales();
    }

    public function calcularTotales()
    {
        $this->subtotal = 0;
        $this->iva_10 = 0;
        $this->iva_5 = 0;
        $this->exenta = 0;

        foreach ($this->detalles as $detalle) {
            $cantidad = $detalle['cantidad'];
            $precio_unitario = $detalle['precio_unitario'];
            $iva_porcentaje = $detalle['iva_porcentaje'];

            // Calcular subtotal del detalle
            $subtotal_detalle = $cantidad * $precio_unitario;

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

        if (count($this->detalles) == 0) {
            session()->flash('error', 'Debe agregar al menos un concepto a la nota de débito');
            return;
        }

        DB::beginTransaction();

        try {
            $timbrado = Timbrado::findOrFail($this->timbrado_id);

            if ($this->notaDebitoId) {
                // Actualizar nota de débito existente
                $notaDebito = NotaDebito::findOrFail($this->notaDebitoId);
                $notaDebito->update([
                    'motivo' => $this->motivo,
                    'observaciones' => $this->observaciones,
                    'actualizado_por' => Auth::id(),
                ]);
            } else {
                // Crear nueva nota de débito
                $numeroSecuencial = $timbrado->obtenerSiguienteNumero();
                $numero_nota = str_pad($numeroSecuencial, 7, '0', STR_PAD_LEFT);

                $notaDebito = NotaDebito::create([
                    'numero_nota' => $numero_nota,
                    'fecha_emision' => $this->fecha_emision,
                    'factura_id' => $this->factura_id,
                    'cliente_id' => $this->cliente_id,
                    'timbrado_id' => $this->timbrado_id,
                    'punto_expedicion_id' => $this->punto_expedicion_id,
                    'numero_timbrado' => $timbrado->numero_timbrado,
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
                foreach ($this->detalles as $detalle) {
                    $cantidad = $detalle['cantidad'];
                    $precio_unitario = $detalle['precio_unitario'];
                    $iva_porcentaje = $detalle['iva_porcentaje'];

                    $subtotal_detalle = $cantidad * $precio_unitario;
                    $iva_monto = $subtotal_detalle * ($iva_porcentaje / 100);
                    $total_detalle = $subtotal_detalle + $iva_monto;

                    NotaDebitoDetalle::create([
                        'nota_debito_id' => $notaDebito->id,
                        'producto_id' => $detalle['producto_id'],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio_unitario,
                        'descuento_porcentaje' => 0,
                        'descuento_monto' => 0,
                        'subtotal' => $subtotal_detalle,
                        'iva_porcentaje' => $iva_porcentaje,
                        'iva_monto' => $iva_monto,
                        'total' => $total_detalle,
                        'descripcion_adicional' => $detalle['descripcion_adicional'] ?? '',
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', 'Nota de débito guardada correctamente');
            return redirect()->route('ventas.notas-debito.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ventas.nota-debito-form');
    }
}
