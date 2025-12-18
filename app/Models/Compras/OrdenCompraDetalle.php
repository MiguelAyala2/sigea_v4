<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenCompraDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'compras.ordenes_compra_detalle';

    protected $fillable = [
        'orden_compra_id',
        'producto_id',
        'pedido_compra_detalle_id',
        'presupuesto_detalle_id',
        'descripcion',
        'cantidad_ordenada',
        'cantidad_recibida',
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
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'cantidad_ordenada' => 'decimal:3',
        'cantidad_recibida' => 'decimal:3',
        'cantidad_pendiente' => 'decimal:3',
        'precio_unitario' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_monto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function pedidoCompraDetalle(): BelongsTo
    {
        return $this->belongsTo(PedidoCompraDetalle::class, 'pedido_compra_detalle_id');
    }

    public function presupuestoDetalle(): BelongsTo
    {
        return $this->belongsTo(PresupuestoDetalle::class, 'presupuesto_detalle_id');
    }

    // Eventos del modelo
    protected static function booted()
    {
        static::saving(function ($detalle) {
            $detalle->calcularTotales();
            $detalle->calcularCantidadPendiente();
        });

        static::saved(function ($detalle) {
            $detalle->ordenCompra->calcularTotales();

            // Actualizar pedido detalle si existe
            if ($detalle->pedidoCompraDetalle) {
                $detalle->actualizarPedidoDetalle();
            }
        });
    }

    // Métodos auxiliares
    public function calcularTotales()
    {
        // Calcular descuento si hay porcentaje
        if ($this->descuento_porcentaje > 0) {
            $this->descuento_monto = ($this->precio_unitario * $this->cantidad_ordenada * $this->descuento_porcentaje) / 100;
        }

        // Calcular subtotal
        $this->subtotal = ($this->precio_unitario * $this->cantidad_ordenada) - $this->descuento_monto;

        // Calcular IVA
        $this->iva_monto = ($this->subtotal * $this->iva_porcentaje) / 100;

        // Calcular total
        $this->total = $this->subtotal + $this->iva_monto;
    }

    public function calcularCantidadPendiente()
    {
        $this->cantidad_pendiente = $this->cantidad_ordenada - $this->cantidad_recibida;

        // Actualizar estado del detalle
        if ($this->cantidad_recibida == 0) {
            $this->estado = 'PENDIENTE';
        } elseif ($this->cantidad_recibida < $this->cantidad_ordenada) {
            $this->estado = 'PARCIALMENTE_RECIBIDO';
        } else {
            $this->estado = 'COMPLETAMENTE_RECIBIDO';
        }
    }

    public function actualizarPedidoDetalle()
    {
        $pedidoDetalle = $this->pedidoCompraDetalle;

        // Sumar todas las cantidades ordenadas de este producto en todas las órdenes
        $totalOrdenado = OrdenCompraDetalle::where('pedido_compra_detalle_id', $pedidoDetalle->id)
            ->sum('cantidad_ordenada');

        $pedidoDetalle->cantidad_ordenada = $totalOrdenado;
        $pedidoDetalle->save();

        // Actualizar estado del pedido completo
        $pedidoDetalle->pedidoCompra->actualizarPorcentajeOrdenado();
    }
}
