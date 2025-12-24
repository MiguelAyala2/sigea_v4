<?php

namespace App\Models\Servicios;

use App\Models\Stock\Producto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticoRepuesto extends Model
{
    use HasFactory;

    protected $table = 'servicios.DIAGNOSTICO_REPUESTOS';

    protected $fillable = [
        'diagnostico_id',
        'producto_id',
        'cantidad',
        'costo',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'costo' => 'decimal:2',
    ];

    // Relaciones
    public function diagnostico(): BelongsTo
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
