<?php

namespace App\Models\Compras;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RemisionDetalle extends Model
{
    use HasFactory;

    protected $table = 'compras.remisiones_detalle';

    protected $fillable = [
        'remision_id',
        'producto_id',
        'descripcion',
        'cantidad_enviada',
        'cantidad_recibida',
        'cantidad_rechazada',
        'unidad_medida',
        'motivo_rechazo',
        'observaciones',
    ];

    protected $casts = [
        'cantidad_enviada' => 'decimal:2',
        'cantidad_recibida' => 'decimal:2',
        'cantidad_rechazada' => 'decimal:2',
    ];

    // Relaciones
    public function remision()
    {
        return $this->belongsTo(Remision::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // Métodos auxiliares
    public function getCantidadPendienteAttribute()
    {
        return $this->cantidad_enviada - ($this->cantidad_recibida + $this->cantidad_rechazada);
    }
}
