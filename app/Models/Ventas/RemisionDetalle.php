<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RemisionDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas.REMISIONES_DETALLE';

    protected $fillable = [
        'remision_id',
        'producto_id',
        'producto_descripcion',
        'cantidad',
        'unidad_medida',
        'precio_unitario',
        'subtotal',
        'factura_detalle_id',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function remision()
    {
        return $this->belongsTo(Remision::class, 'remision_id');
    }

    public function producto()
    {
        return $this->belongsTo(\App\Models\Stock\Producto::class, 'producto_id');
    }

    public function facturaDetalle()
    {
        return $this->belongsTo(FacturaDetalle::class, 'factura_detalle_id');
    }
}
