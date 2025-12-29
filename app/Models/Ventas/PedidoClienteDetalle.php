<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoClienteDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas.pedidos_clientes_detalle';

    protected $fillable = [
        'pedido_cliente_id',
        'producto_id',
        'cotizacion_detalle_id',
        'cantidad_solicitada',
        'cantidad_entregada',
        'cantidad_pendiente',
        'precio_unitario',
        'descuento_porcentaje',
        'descuento_monto',
        'subtotal',
        'iva_porcentaje',
        'iva_monto',
        'total',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'cantidad_solicitada' => 'decimal:2',
        'cantidad_entregada' => 'decimal:2',
        'cantidad_pendiente' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_monto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Eventos del modelo
    protected static function booted()
    {
        static::saving(function ($detalle) {
            $detalle->calcularTotales();
        });

        static::saved(function ($detalle) {
            $detalle->pedidoCliente->calcularTotales();
            $detalle->pedidoCliente->actualizarPorcentajeEntregado();
        });

        static::deleted(function ($detalle) {
            $detalle->pedidoCliente->calcularTotales();
            $detalle->pedidoCliente->actualizarPorcentajeEntregado();
        });
    }

    // Relaciones
    public function pedidoCliente()
    {
        return $this->belongsTo(PedidoCliente::class, 'pedido_cliente_id');
    }

    public function producto()
    {
        return $this->belongsTo(\App\Models\Stock\Producto::class, 'producto_id');
    }

    public function cotizacionDetalle()
    {
        return $this->belongsTo(\App\Models\Ventas\CotizacionDetalle::class, 'cotizacion_detalle_id');
    }

    // Métodos de negocio

    /**
     * Calcula los totales del detalle
     * IMPORTANTE: Los precios incluyen IVA
     */
    public function calcularTotales()
    {
        // Subtotal con IVA incluido = (cantidad × precio) - descuento
        $subtotalConIva = ($this->cantidad_solicitada * $this->precio_unitario);

        // Aplicar descuento al subtotal
        if ($this->descuento_porcentaje > 0) {
            $this->descuento_monto = ($subtotalConIva * $this->descuento_porcentaje) / 100;
            $subtotalConIva -= $this->descuento_monto;
        }

        // El subtotal ya incluye IVA
        $this->subtotal = $subtotalConIva;

        // Calcular el IVA incluido en el subtotal (solo informativo)
        if ($this->iva_porcentaje == 10) {
            $this->iva_monto = $this->subtotal * 0.10;
        } elseif ($this->iva_porcentaje == 5) {
            $this->iva_monto = $this->subtotal * 0.05;
        } else {
            // IVA 0% (exenta)
            $this->iva_monto = 0;
        }

        // El total es igual al subtotal (porque el IVA ya está incluido)
        $this->total = $this->subtotal;

        // Calcular cantidad pendiente
        $this->calcularCantidadPendiente();
    }

    /**
     * Calcula la cantidad pendiente de entregar
     */
    public function calcularCantidadPendiente()
    {
        $this->cantidad_pendiente = $this->cantidad_solicitada - $this->cantidad_entregada;

        // Actualizar estado según cantidad pendiente
        if ($this->cantidad_pendiente <= 0) {
            $this->estado = 'COMPLETAMENTE_ENTREGADO';
        } elseif ($this->cantidad_entregada > 0) {
            $this->estado = 'PARCIALMENTE_ENTREGADO';
        } else {
            $this->estado = 'PENDIENTE';
        }
    }

    /**
     * Registra la entrega de una cantidad
     */
    public function registrarEntrega($cantidad)
    {
        $this->cantidad_entregada += $cantidad;
        $this->save();

        return $this;
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para filtrar pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('cantidad_pendiente', '>', 0);
    }
}
