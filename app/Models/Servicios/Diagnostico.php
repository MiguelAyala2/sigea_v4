<?php

namespace App\Models\Servicios;

use App\Models\Stock\Producto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;

class Diagnostico extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'servicios.DIAGNOSTICOS';

    protected $fillable = [
        'numero_diagnostico',
        'fecha_diagnostico',
        'solicitud_id',
        'recepcion_id',
        'cliente_id',
        'producto_id',
        'problema_detectado',
        'solucion_propuesta',
        'mano_obra_descripcion',
        'mano_obra_costo',
        'estado_diagnostico',
        'estado',
        'observaciones',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_diagnostico' => 'date',
        'mano_obra_costo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    protected $appends = ['codigo'];

    public const ESTADOS_DIAGNOSTICO = [
        'reparable' => 'Reparable',
        'no_reparable' => 'No Reparable',
        'requiere_repuestos' => 'Requiere Repuestos',
    ];

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'en_proceso' => 'En Proceso',
        'completado' => 'Completado',
    ];

    // Relaciones
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudServicio::class, 'solicitud_id');
    }

    public function recepcion(): BelongsTo
    {
        return $this->belongsTo(Recepcion::class, 'recepcion_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function repuestos(): HasMany
    {
        return $this->hasMany(DiagnosticoRepuesto::class, 'diagnostico_id');
    }

    public function tiposServicio(): HasMany
    {
        return $this->hasMany(DiagnosticoTipoServicio::class, 'diagnostico_id');
    }

    // Scopes
    public function scopeBuscador(Builder $query, ?string $termino): void
    {
        if (!empty($termino)) {
            $query->where(function ($q) use ($termino) {
                $q->where('numero_diagnostico', 'ILIKE', "%{$termino}%")
                  ->orWhereHas('cliente', function ($sq) use ($termino) {
                      $sq->where('nombre', 'ILIKE', "%{$termino}%");
                  })
                  ->orWhereHas('solicitud', function ($sq) use ($termino) {
                      $sq->where('numero_solicitud', 'ILIKE', "%{$termino}%");
                  });
            });
        }
    }

    public function scopeBuscarEstadoDiagnostico(Builder $query, ?string $estado): void
    {
        if (!empty($estado)) {
            $query->where('estado_diagnostico', $estado);
        }
    }

    public function scopeBuscarEstado(Builder $query, ?string $estado): void
    {
        if (!empty($estado)) {
            $query->where('estado', $estado);
        }
    }

    public function scopeBuscarActivo(Builder $query, ?string $activo): void
    {
        if ($activo !== '' && $activo !== null) {
            $query->where('activo', (bool) $activo);
        }
    }

    public function scopeSoloActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    // Accessor para código (alias de numero_diagnostico)
    public function getCodigoAttribute(): string
    {
        return $this->numero_diagnostico;
    }

    // Generar número de diagnóstico
    public static function generarNumeroDiagnostico(): string
    {
        $ultimo = self::orderBy('id', 'desc')->first();
        $numero = $ultimo ? (int) substr($ultimo->numero_diagnostico, 5) + 1 : 1;
        return 'DIAG-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
