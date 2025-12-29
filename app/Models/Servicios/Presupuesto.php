<?php

namespace App\Models\Servicios;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Presupuesto extends Model
{
    protected $table = 'servicios.PRESUPUESTOS';

    protected $fillable = [
        'codigo',
        'fecha_presupuesto',
        'diagnostico_id',
        'promocion_id',
        'descuento_id',
        'subtotal_servicios',
        'descuento_promocion',
        'total_servicios',
        'subtotal_repuestos',
        'descuento_descuento',
        'total_repuestos',
        'monto_total',
        'estado',
        'observaciones',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_presupuesto' => 'date',
        'subtotal_servicios' => 'decimal:2',
        'descuento_promocion' => 'decimal:2',
        'total_servicios' => 'decimal:2',
        'subtotal_repuestos' => 'decimal:2',
        'descuento_descuento' => 'decimal:2',
        'total_repuestos' => 'decimal:2',
        'monto_total' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Generar código automático
    public static function generarCodigo()
    {
        $ultimoPresupuesto = self::orderBy('id', 'desc')->first();
        $numero = $ultimoPresupuesto ? intval(substr($ultimoPresupuesto->codigo, 4)) + 1 : 1;
        return 'PRE-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    // Relaciones
    public function diagnostico(): BelongsTo
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }

    public function promocion(): BelongsTo
    {
        return $this->belongsTo(Promocion::class, 'promocion_id');
    }

    public function descuento(): BelongsTo
    {
        return $this->belongsTo(Descuento::class, 'descuento_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    public function ordenServicio(): HasOne
    {
        return $this->hasOne(OrdenServicio::class, 'presupuesto_id');
    }

    public function tiposServicio(): HasManyThrough
    {
        return $this->hasManyThrough(
            DiagnosticoTipoServicio::class,
            Diagnostico::class,
            'id',
            'diagnostico_id',
            'diagnostico_id',
            'id'
        );
    }

    public function repuestos(): HasManyThrough
    {
        return $this->hasManyThrough(
            DiagnosticoRepuesto::class,
            Diagnostico::class,
            'id',
            'diagnostico_id',
            'diagnostico_id',
            'id'
        );
    }

    // Accesorios
    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'pendiente_aprobacion' => '<span class="badge badge-warning">Pendiente Aprobación</span>',
            'aprobado' => '<span class="badge badge-success">Aprobado</span>',
            'rechazado' => '<span class="badge badge-danger">Rechazado</span>',
            default => '<span class="badge badge-secondary">Desconocido</span>',
        };
    }

    public function getMontoFormateadoAttribute()
    {
        return '₲ ' . number_format($this->monto_total, 0, ',', '.');
    }

    public function getClienteAttribute()
    {
        return $this->diagnostico?->recepcion?->solicitud?->cliente?->nombre ?? 'N/A';
    }

    public function getEquipoAttribute()
    {
        return $this->diagnostico?->recepcion?->producto?->nombre ?? 'N/A';
    }
}
