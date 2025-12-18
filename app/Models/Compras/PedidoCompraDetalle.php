<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoCompraDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'compras.pedidos_compra_detalle';

    protected $fillable = [
        'pedido_compra_id',
        'producto_id',
        'cantidad_solicitada',
        'cantidad_aprobada',
        'cantidad_ordenada',
        'cantidad_pendiente',
        'stock_actual',
        'stock_minimo',
        'precio_estimado',
        'subtotal_estimado',
        'iva_porcentaje',
        'justificacion_item',
        'observaciones',
        'estado',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'cantidad_solicitada' => 'decimal:3',
        'cantidad_aprobada' => 'decimal:3',
        'cantidad_ordenada' => 'decimal:3',
        'cantidad_pendiente' => 'decimal:3',
        'stock_actual' => 'decimal:3',
        'stock_minimo' => 'decimal:3',
        'precio_estimado' => 'decimal:2',
        'subtotal_estimado' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
    ];

    // Relaciones
    public function pedidoCompra(): BelongsTo
    {
        return $this->belongsTo(PedidoCompra::class, 'pedido_compra_id');
    }

    // Eventos del modelo
    protected static function booted()
    {
        static::saving(function ($detalle) {
            $detalle->calcularSubtotal();
            $detalle->calcularCantidadPendiente();
        });

        static::saved(function ($detalle) {
            $detalle->pedidoCompra->calcularTotalEstimado();
        });
    }

    // Métodos auxiliares
    public function calcularSubtotal()
    {
        $this->subtotal_estimado = $this->cantidad_solicitada * $this->precio_estimado;
    }

    public function calcularCantidadPendiente()
    {
        $this->cantidad_pendiente = $this->cantidad_aprobada - $this->cantidad_ordenada;
    }
}
