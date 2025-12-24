<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticoTipoServicio extends Model
{
    use HasFactory;

    protected $table = 'servicios.DIAGNOSTICO_TIPOS_SERVICIO';

    protected $fillable = [
        'diagnostico_id',
        'tipo_servicio_id',
        'cantidad',
        'costo_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'costo_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function diagnostico(): BelongsTo
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }

    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(TipoServicio::class, 'tipo_servicio_id');
    }
}
