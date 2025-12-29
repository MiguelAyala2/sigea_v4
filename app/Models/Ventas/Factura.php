<?php

namespace App\Models\Ventas;

use App\Models\Empresa\Timbrado;
use App\Models\Empresa\PuntoExpedicion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class Factura extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'ventas.FACTURAS';

    protected $fillable = [
        'numero_factura',
        'fecha_emision',
        'fecha_vencimiento',
        'cliente_id',
        'pedido_cliente_id',
        'cotizacion_id',
        'timbrado_id',
        'punto_expedicion_id',
        'numero_timbrado',
        'sucursal_id',
        'deposito_id',
        'vendedor_id',
        'condicion_pago',
        'cantidad_cuotas',
        'subtotal',
        'iva_10',
        'iva_5',
        'exenta',
        'total_iva',
        'descuento_global',
        'flete',
        'total',
        'es_electronica',
        'cdc',
        'qr_data',
        'xml_firmado',
        'estado_set',
        'fecha_envio_set',
        'fecha_respuesta_set',
        'mensaje_set',
        'estado',
        'observaciones',
        'motivo_anulacion',
        'creado_por',
        'actualizado_por',
        'emitido_por',
        'emitido_en',
        'anulado_por',
        'anulado_en',
        'activo',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'iva_10' => 'decimal:2',
        'iva_5' => 'decimal:2',
        'exenta' => 'decimal:2',
        'total_iva' => 'decimal:2',
        'descuento_global' => 'decimal:2',
        'flete' => 'decimal:2',
        'total' => 'decimal:2',
        'es_electronica' => 'boolean',
        'fecha_envio_set' => 'datetime',
        'fecha_respuesta_set' => 'datetime',
        'emitido_en' => 'datetime',
        'anulado_en' => 'datetime',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Servicios\Cliente::class, 'cliente_id');
    }

    public function pedidoCliente()
    {
        return $this->belongsTo(PedidoCliente::class, 'pedido_cliente_id');
    }

    public function cotizacion()
    {
        return $this->belongsTo(\App\Models\Ventas\Cotizacion::class, 'cotizacion_id');
    }

    public function timbrado()
    {
        return $this->belongsTo(Timbrado::class, 'timbrado_id');
    }

    public function puntoExpedicion()
    {
        return $this->belongsTo(PuntoExpedicion::class, 'punto_expedicion_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(\App\Models\Empresa\Sucursal::class, 'sucursal_id');
    }

    public function deposito()
    {
        return $this->belongsTo(\App\Models\Empresa\Deposito::class, 'deposito_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(\App\Models\User::class, 'vendedor_id');
    }

    public function detalles()
    {
        return $this->hasMany(FacturaDetalle::class, 'factura_id');
    }

    public function servicios()
    {
        return $this->hasMany(FacturaServicio::class, 'factura_id');
    }

    public function formasPago()
    {
        return $this->hasMany(FacturaFormaPago::class, 'factura_id');
    }

    public function notaCredito()
    {
        return $this->hasOne(NotaCredito::class, 'factura_id');
    }

    public function notaDebito()
    {
        return $this->hasOne(NotaDebito::class, 'factura_id');
    }

    public function cuentaPorCobrar()
    {
        return $this->hasOne(CuentaPorCobrar::class, 'factura_id');
    }

    public function movimientosCaja()
    {
        return $this->hasMany(MovimientoCaja::class, 'venta_id');
    }

    public function cuotas()
    {
        return $this->hasMany(FacturaCuota::class, 'factura_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'actualizado_por');
    }

    public function emitidoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'emitido_por');
    }

    public function anuladoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'anulado_por');
    }

    // Accessors
    public function getNumeroCompletoAttribute(): string
    {
        return $this->numero_timbrado . '-' . $this->numero_factura;
    }

    public function getEstadoTextoAttribute(): string
    {
        return match($this->estado) {
            'BORRADOR' => 'Borrador',
            'EMITIDA' => 'Emitida',
            'PAGADA' => 'Pagada',
            'PARCIALMENTE_PAGADA' => 'Parcialmente Pagada',
            'VENCIDA' => 'Vencida',
            'ANULADA' => 'Anulada',
            default => $this->estado,
        };
    }

    public function getTotalPagadoAttribute(): float
    {
        return $this->formasPago()->sum('monto');
    }

    public function getSaldoPendienteAttribute(): float
    {
        return $this->total - $this->total_pagado;
    }

    // Métodos de negocio

    /**
     * Calcula los totales de la factura basándose en los detalles
     */
    /**
     * Calcula los totales de la factura
     * IMPORTANTE: Los precios incluyen IVA
     */
    public function calcularTotales()
    {
        $detalles = $this->detalles;
        $servicios = $this->servicios;

        // Sumar subtotales de productos y servicios (ya incluyen IVA)
        $this->subtotal = $detalles->sum('subtotal') + $servicios->sum('subtotal');

        // IVA es solo informativo (ya está incluido en el subtotal)
        $this->iva_10 = $detalles->where('iva_porcentaje', 10)->sum('iva_monto') + $servicios->sum('iva_monto');
        $this->iva_5 = $detalles->where('iva_porcentaje', 5)->sum('iva_monto');
        $this->exenta = $detalles->where('iva_porcentaje', 0)->sum('subtotal');
        $this->total_iva = $this->iva_10 + $this->iva_5;

        // Total = Subtotal (ya incluye IVA) + Flete - Descuento Global
        $this->total = $this->subtotal + $this->flete - $this->descuento_global;

        $this->save();
    }

    /**
     * Genera el número de factura usando el timbrado
     */
    public function generarNumeroFactura($timbradoId): string
    {
        $timbrado = Timbrado::findOrFail($timbradoId);

        // Verificar que el timbrado sea válido
        if (!$timbrado->esta_vigente) {
            throw new \Exception('El timbrado no está vigente.');
        }

        if ($timbrado->esta_agotado) {
            throw new \Exception('El timbrado está agotado.');
        }

        // Obtener siguiente número
        $numeroSecuencial = $timbrado->obtenerSiguienteNumero();

        return str_pad($numeroSecuencial, 7, '0', STR_PAD_LEFT);
    }

    /**
     * Emite la factura
     */
    public function emitir($usuarioId = null)
    {
        DB::beginTransaction();

        try {
            // 1. Validar estado
            if ($this->estado !== 'BORRADOR') {
                throw new \Exception('Solo se pueden emitir facturas en estado BORRADOR.');
            }

            // 2. Validar que tenga al menos un detalle
            if ($this->detalles()->count() == 0 && $this->servicios()->count() == 0) {
                throw new \Exception('La factura debe tener al menos un detalle de producto o servicio.');
            }

            // 3. Validar timbrado
            if (!$this->timbrado->esta_vigente) {
                throw new \Exception('El timbrado ha vencido. Fecha de vencimiento: ' . $this->timbrado->fecha_fin_vigencia->format('d/m/Y'));
            }

            if ($this->timbrado->esta_agotado) {
                throw new \Exception('El timbrado está agotado. Número actual: ' . $this->timbrado->numero_actual . ' / Final: ' . $this->timbrado->numero_hasta);
            }

            // 4. Si es al contado, validar que tenga formas de pago
            if ($this->condicion_pago === 'CONTADO') {
                if ($this->formasPago()->count() == 0) {
                    throw new \Exception('Las facturas al contado deben tener al menos una forma de pago.');
                }

                $totalFormasPago = $this->formasPago()->sum('monto');
                if ($totalFormasPago < $this->total) {
                    throw new \Exception('El total de las formas de pago (₲ ' . number_format($totalFormasPago, 0, ',', '.') . ') debe ser igual al total de la factura (₲ ' . number_format($this->total, 0, ',', '.') . ').');
                }
            }

            // 5. Descontar stock de productos
            foreach ($this->detalles as $detalle) {
                $producto = $detalle->producto;

                // Buscar el stock del producto en el depósito
                $stock = \App\Models\Stock\Stock::where('producto_id', $producto->id)
                    ->where('deposito_id', $this->deposito_id)
                    ->first();

                if (!$stock) {
                    throw new \Exception("El producto '{$producto->nombre}' no tiene stock en el depósito '{$this->deposito->nombre}'.");
                }

                if ($stock->stock_actual < $detalle->cantidad) {
                    throw new \Exception("Stock insuficiente para el producto '{$producto->nombre}'. Disponible: {$stock->stock_actual}, Requerido: {$detalle->cantidad}");
                }

                // Guardar stock anterior para el movimiento
                $stockAnterior = $stock->stock_actual;

                // Descontar stock
                $stock->stock_actual -= $detalle->cantidad;
                $stock->save();

                // Registrar movimiento de stock
                \App\Models\Stock\MovimientoStock::create([
                    'producto_id' => $producto->id,
                    'deposito_id' => $this->deposito_id,
                    'tipo' => 'SALIDA_VENTA',
                    'cantidad' => $detalle->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_posterior' => $stock->stock_actual,
                    'costo_unitario' => $producto->costo_promedio ?? 0,
                    'costo_total' => ($producto->costo_promedio ?? 0) * $detalle->cantidad,
                    'documento_tipo' => 'FACTURA',
                    'documento_id' => $this->id,
                    'motivo' => 'Venta - Factura ' . $this->numero_timbrado . '-' . $this->numero_factura,
                    'fecha_movimiento' => now(),
                    'usuario_id' => $usuarioId ?? auth()->id(),
                ]);
            }

            // 6. Actualizar estado de la factura
            $this->estado = 'EMITIDA';
            $this->emitido_por = $usuarioId ?? auth()->id();
            $this->emitido_en = now();
            $this->save();

            // 7. Crear cuenta por cobrar si es a crédito
            if ($this->condicion_pago !== 'CONTADO') {
                CuentaPorCobrar::create([
                    'factura_id' => $this->id,
                    'numero_factura' => $this->numero_timbrado . '-' . $this->numero_factura,
                    'cliente_id' => $this->cliente_id,
                    'fecha_emision' => $this->fecha_emision,
                    'fecha_vencimiento' => $this->fecha_vencimiento,
                    'monto_total' => $this->total,
                    'monto_pagado' => 0,
                    'saldo_pendiente' => $this->total,
                    'estado' => 'PENDIENTE',
                    'activo' => true,
                    'creado_por' => $usuarioId ?? auth()->id(),
                ]);
            }

            // 8. Registrar movimiento de caja si es CONTADO
            if ($this->condicion_pago === 'CONTADO') {
                $aperturaActual = AperturaCaja::obtenerAperturaActual($this->punto_expedicion_id);

                if (!$aperturaActual) {
                    throw new \Exception('No hay una caja abierta para este punto de expedición. Debe abrir la caja antes de emitir facturas al contado.');
                }

                foreach ($this->formasPago as $fp) {
                    MovimientoCaja::create([
                        'apertura_caja_id' => $aperturaActual->id,
                        'usuario_responsable_id' => $usuarioId ?? auth()->id(),
                        'tipo_movimiento' => 'INGRESO',
                        'concepto' => 'Venta factura ' . $this->numero_timbrado . '-' . $this->numero_factura,
                        'monto' => $fp->monto,
                        'forma_pago' => $fp->forma_pago,
                        'comprobante_numero' => $this->numero_timbrado . '-' . $this->numero_factura,
                        'referencia' => $fp->referencia,
                        'fecha_movimiento' => now(),
                        'hora_movimiento' => now()->format('H:i:s'),
                        'venta_id' => $this->id,
                        'activo' => true,
                        'creadoPor' => $usuarioId ?? auth()->id(),
                    ]);
                }
            }

            // 9. Si viene de un pedido, marcarlo como facturado
            if ($this->pedido_cliente_id) {
                $this->pedidoCliente->facturar($usuarioId);
            }

            DB::commit();
            return $this;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza el estado de pago de la factura
     */
    public function actualizarEstadoPago()
    {
        if ($this->estado === 'ANULADA') {
            return;
        }

        $totalPagado = $this->total_pagado;

        if ($totalPagado >= $this->total) {
            $this->estado = 'PAGADA';
        } elseif ($totalPagado > 0) {
            $this->estado = 'PARCIALMENTE_PAGADA';
        } elseif ($this->fecha_vencimiento && $this->fecha_vencimiento < now()) {
            $this->estado = 'VENCIDA';
        } else {
            $this->estado = 'EMITIDA';
        }

        $this->save();
    }

    /**
     * Anula la factura
     */
    public function anular(string $motivo, $usuarioId = null)
    {
        DB::beginTransaction();

        try {
            $this->estado = 'ANULADA';
            $this->motivo_anulacion = $motivo;
            $this->anulado_por = $usuarioId ?? auth()->user()->id;
            $this->anulado_en = now();
            $this->save();

            DB::commit();
            return $this;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Registra una forma de pago
     */
    public function registrarPago(string $formaPago, float $monto, array $datos = [])
    {
        DB::beginTransaction();

        try {
            $this->formasPago()->create([
                'forma_pago' => $formaPago,
                'monto' => $monto,
                'referencia' => $datos['referencia'] ?? null,
                'banco' => $datos['banco'] ?? null,
                'fecha_pago' => $datos['fecha_pago'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            $this->actualizarEstadoPago();

            DB::commit();
            return $this;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Genera el CDC (Código de Control) para facturas electrónicas
     */
    public function generarCDC(): string
    {
        // Formato CDC: Timbrado (8) + RUC (11) + Punto Expedición (3) + Número (7) + Tipo Doc (2) + Fecha (8) + DV (1)
        // TODO: Implementar generación completa de CDC con DV

        $timbrado = str_pad(substr($this->numero_timbrado, 0, 8), 8, '0', STR_PAD_LEFT);
        $ruc = '80000000001'; // TODO: Obtener RUC de configuración de empresa
        $puntoExp = str_pad($this->puntoExpedicion->codigo, 3, '0', STR_PAD_LEFT);
        $numero = str_pad($this->numero_factura, 7, '0', STR_PAD_LEFT);
        $tipoDoc = '01'; // 01 = Factura
        $fecha = $this->fecha_emision->format('Ymd');
        $dv = '0'; // TODO: Calcular dígito verificador

        return $timbrado . $ruc . $puntoExp . $numero . $tipoDoc . $fecha . $dv;
    }

    /**
     * Envía la factura electrónica a SET
     */
    public function enviarSET()
    {
        // TODO: Implementar integración con SIFEN
        $this->estado_set = 'PENDIENTE';
        $this->fecha_envio_set = now();
        $this->save();

        return $this;
    }

    // Scopes
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_emision', [$fechaInicio, $fechaFin]);
        }

        return $query->whereDate('fecha_emision', $fechaInicio);
    }

    public function scopeVencidas($query)
    {
        return $query->where('estado', '!=', 'ANULADA')
            ->where('estado', '!=', 'PAGADA')
            ->whereDate('fecha_vencimiento', '<', now());
    }

    /**
     * Genera el PDF de la factura
     */
    public function generarPDF()
    {
        // Cargar relaciones necesarias
        $this->load([
            'cliente',
            'timbrado',
            'puntoExpedicion.sucursal.empresa',
            'deposito',
            'detalles.producto',
            'servicios',
            'formasPago',
            'creadoPor',
            'emitidoPor',
        ]);

        // Generar PDF usando DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ventas.facturas.pdf', [
            'factura' => $this,
        ]);

        // Configurar tamaño y orientación
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Descarga el PDF de la factura
     */
    public function descargarPDF()
    {
        $nombreArchivo = 'factura_' . $this->numero_timbrado . '-' . $this->numero_factura . '.pdf';
        return $this->generarPDF()->download($nombreArchivo);
    }

    /**
     * Visualiza el PDF de la factura en el navegador
     */
    public function verPDF()
    {
        return $this->generarPDF()->stream('factura_' . $this->numero_timbrado . '-' . $this->numero_factura . '.pdf');
    }
}
