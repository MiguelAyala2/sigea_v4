<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class NotaCreditoDetalle extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'ventas.NOTAS_CREDITO_DETALLE';

    protected $fillable = [
        'nota_credito_id',
        'factura_detalle_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'descuento_porcentaje',
        'descuento_monto',
        'subtotal',
        'iva_porcentaje',
        'iva_monto',
        'total',
        'descripcion_adicional',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
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
            $detalle->notaCredito->calcularTotales();
        });

        static::deleted(function ($detalle) {
            $detalle->notaCredito->calcularTotales();
        });
    }

    // Relaciones
    public function notaCredito()
    {
        return $this->belongsTo(NotaCredito::class, 'nota_credito_id');
    }

    public function facturaDetalle()
    {
        return $this->belongsTo(FacturaDetalle::class, 'factura_detalle_id');
    }

    public function producto()
    {
        return $this->belongsTo(\App\Models\Stock\Producto::class, 'producto_id');
    }

    // Métodos de negocio

    /**
     * Calcula los totales del detalle
     */
    public function calcularTotales()
    {
        // Calcular descuento
        if ($this->descuento_porcentaje > 0) {
            $this->descuento_monto = ($this->cantidad * $this->precio_unitario * $this->descuento_porcentaje) / 100;
        }

        // Subtotal = (cantidad × precio) - descuento
        $this->subtotal = ($this->cantidad * $this->precio_unitario) - $this->descuento_monto;

        // Calcular IVA
        $this->iva_monto = ($this->subtotal * $this->iva_porcentaje) / 100;

        // Total = subtotal + IVA
        $this->total = $this->subtotal + $this->iva_monto;
    }
}
