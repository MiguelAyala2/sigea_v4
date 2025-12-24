<?php

namespace App\Models\Servicios;

use App\Models\Stock\Producto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;

class SolicitudServicio extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'servicios.SOLICITUDES_SERVICIO';

    protected $fillable = [
        'numero_solicitud',
        'fecha',
        'cliente_id',
        'producto_id',
        'tipo_servicio',
        'prioridad',
        'estado',
        'observaciones',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha' => 'date',
        'activo' => 'boolean',
    ];

    public const TIPOS_SERVICIO = [
        'mantenimiento' => 'Mantenimiento',
        'reparacion' => 'Reparación',
        'diagnostico' => 'Diagnóstico',
    ];

    public const PRIORIDADES = [
        'baja' => 'Baja',
        'media' => 'Media',
        'alta' => 'Alta',
    ];

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'en_proceso' => 'En Proceso',
        'completado' => 'Completado',
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Scopes
    public function scopeBuscador(Builder $query, ?string $termino): void
    {
        if (!empty($termino)) {
            $query->where(function ($q) use ($termino) {
                $q->where('numero_solicitud', 'ILIKE', "%{$termino}%")
                  ->orWhereHas('cliente', function ($sq) use ($termino) {
                      $sq->where('nombre', 'ILIKE', "%{$termino}%");
                  })
                  ->orWhereHas('producto', function ($sq) use ($termino) {
                      $sq->where('nombre', 'ILIKE', "%{$termino}%");
                  });
            });
        }
    }

    public function scopeBuscarEstado(Builder $query, ?string $estado): void
    {
        if (!empty($estado)) {
            $query->where('estado', $estado);
        }
    }

    public function scopeBuscarPrioridad(Builder $query, ?string $prioridad): void
    {
        if (!empty($prioridad)) {
            $query->where('prioridad', $prioridad);
        }
    }

    public function scopeBuscarTipoServicio(Builder $query, ?string $tipo): void
    {
        if (!empty($tipo)) {
            $query->where('tipo_servicio', $tipo);
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

    // Generar número de solicitud
    public static function generarNumeroSolicitud(): string
    {
        $ultimo = self::orderBy('id', 'desc')->first();
        $numero = $ultimo ? (int) substr($ultimo->numero_solicitud, 4) + 1 : 1;
        return 'SOL-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
