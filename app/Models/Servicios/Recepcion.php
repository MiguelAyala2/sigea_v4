<?php

namespace App\Models\Servicios;

use App\Models\Stock\Producto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;

class Recepcion extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'servicios.RECEPCIONES';

    protected $fillable = [
        'numero_recepcion',
        'fecha_recepcion',
        'solicitud_id',
        'cliente_id',
        'producto_id',
        'contacto_cliente',
        'tipo_equipo',
        'marca',
        'modelo',
        'numero_serie',
        'estado_recepcion',
        'estado',
        'descripcion_problema',
        'accesorios_recibidos',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_recepcion' => 'date',
        'activo' => 'boolean',
    ];

    public const ESTADOS_RECEPCION = [
        'bueno' => 'Bueno',
        'regular' => 'Regular',
        'malo' => 'Malo',
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
                $q->where('numero_recepcion', 'ILIKE', "%{$termino}%")
                  ->orWhereHas('cliente', function ($sq) use ($termino) {
                      $sq->where('nombre', 'ILIKE', "%{$termino}%");
                  })
                  ->orWhereHas('solicitud', function ($sq) use ($termino) {
                      $sq->where('numero_solicitud', 'ILIKE', "%{$termino}%");
                  });
            });
        }
    }

    public function scopeBuscarEstadoRecepcion(Builder $query, ?string $estado): void
    {
        if (!empty($estado)) {
            $query->where('estado_recepcion', $estado);
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

    // Generar número de recepción
    public static function generarNumeroRecepcion(): string
    {
        $ultimo = self::orderBy('id', 'desc')->first();
        $numero = $ultimo ? (int) substr($ultimo->numero_recepcion, 4) + 1 : 1;
        return 'REC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
