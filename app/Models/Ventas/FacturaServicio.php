<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaServicio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas.FACTURAS_SERVICIOS';

    protected $fillable = [
        'factura_id',
        'tipo_servicio_id',
        'orden_servicio_id',
        'codigo',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'iva_porcentaje',
        'iva_monto',
        'total',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_monto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }
}
