<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresupuestoDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'compras.presupuestos_detalle';

    protected $fillable = [
        'presupuesto_id',
        'producto_id',
        'pedido_compra_detalle_id',
        'descripcion',
        'cantidad_cotizada',
        'precio_unitario',
        'descuento_porcentaje',
        'descuento_monto',
        'subtotal',
        'iva_porcentaje',
        'iva_monto',
        'total',
        'dias_entrega_item',
        'marca_ofrecida',
        'observaciones_proveedor',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'cantidad_cotizada' => 'decimal:3',
        'precio_unitario' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_monto' => 'decimal:2',
        'total' => 'decimal:2',
        'dias_entrega_item' => 'integer',
    ];

    // Relaciones
    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class, 'presupuesto_id');
    }

    public function pedidoCompraDetalle(): BelongsTo
    {
        return $this->belongsTo(PedidoCompraDetalle::class, 'pedido_compra_detalle_id');
    }

    public function producto()
    {
        return $this->hasOne(\App\Models\Stock\Producto::class, 'id', 'producto_id');
    }

    // Eventos del modelo
    protected static function booted()
    {
        static::saving(function ($detalle) {
            $detalle->calcularTotales();
        });

        static::saved(function ($detalle) {
            $detalle->presupuesto->calcularTotales();
        });
    }

    // Métodos auxiliares
    public function calcularTotales()
    {
        // Calcular descuento si hay porcentaje
        if ($this->descuento_porcentaje > 0) {
            $this->descuento_monto = ($this->precio_unitario * $this->cantidad_cotizada * $this->descuento_porcentaje) / 100;
        }

        // Calcular subtotal
        $this->subtotal = ($this->precio_unitario * $this->cantidad_cotizada) - $this->descuento_monto;

        // Calcular IVA
        $this->iva_monto = ($this->subtotal * $this->iva_porcentaje) / 100;

        // Calcular total
        $this->total = $this->subtotal + $this->iva_monto;
    }
}
