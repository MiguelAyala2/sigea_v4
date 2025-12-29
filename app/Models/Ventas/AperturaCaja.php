<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Empresa\PuntoExpedicion;
use App\Models\User;
use OwenIt\Auditing\Contracts\Auditable;

class AperturaCaja extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'ventas.APERTURAS_CAJA';

    protected $fillable = [
        'punto_expedicion_id',
        'usuario_id',
        'fecha_apertura',
        'hora_apertura',
        'saldo_inicial',
        'estado',
        'observaciones',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_apertura' => 'date',
        'saldo_inicial' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Constantes
    const ESTADOS = [
        'ABIERTA' => 'Abierta',
        'CERRADA' => 'Cerrada',
        'CANCELADA' => 'Cancelada',
    ];

    // Relaciones
    public function puntoExpedicion(): BelongsTo
    {
        return $this->belongsTo(PuntoExpedicion::class, 'punto_expedicion_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoCaja::class, 'apertura_caja_id');
    }

    public function cierre(): HasOne
    {
        return $this->hasOne(CierreCaja::class, 'apertura_caja_id');
    }

    public function recaudaciones(): HasMany
    {
        return $this->hasMany(RecaudacionADepositar::class, 'apertura_caja_id');
    }

    public function arqueo(): HasOne
    {
        return $this->hasOne(ArqueoCaja::class, 'apertura_caja_id');
    }

    // Accessors
    public function getEstadoTextoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getTotalIngresosAttribute(): float
    {
        return $this->movimientos()
            ->where('tipo_movimiento', 'INGRESO')
            ->sum('monto');
    }

    public function getTotalEgresosAttribute(): float
    {
        return $this->movimientos()
            ->where('tipo_movimiento', 'EGRESO')
            ->sum('monto');
    }

    public function getSaldoActualAttribute(): float
    {
        return $this->saldo_inicial + $this->total_ingresos - $this->total_egresos;
    }

    public function getEstaAbiertaAttribute(): bool
    {
        return $this->estado === 'ABIERTA';
    }

    public function getEstaCerradaAttribute(): bool
    {
        return $this->estado === 'CERRADA';
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->puntoExpedicion->nombre_completo} - {$this->fecha_apertura->format('d/m/Y')}";
    }

    // Scopes
    public function scopeBuscador($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->whereHas('puntoExpedicion', function ($subQ) use ($search) {
                    $subQ->where('nombre', 'ILIKE', "%{$search}%")
                         ->orWhere('codigo', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('usuario', function ($subQ) use ($search) {
                    $subQ->where('name', 'ILIKE', "%{$search}%");
                });
            });
        }
        return $query;
    }

    public function scopeBuscarPuntoExpedicion($query, $puntoExpedicionId)
    {
        if ($puntoExpedicionId) {
            return $query->where('punto_expedicion_id', $puntoExpedicionId);
        }
        return $query;
    }

    public function scopeBuscarEstado($query, $estado)
    {
        if ($estado) {
            return $query->where('estado', $estado);
        }
        return $query;
    }

    public function scopeBuscarFecha($query, $fecha)
    {
        if ($fecha) {
            return $query->whereDate('fecha_apertura', $fecha);
        }
        return $query;
    }

    public function scopeBuscarRangoFechas($query, $fechaDesde, $fechaHasta)
    {
        if ($fechaDesde && $fechaHasta) {
            return $query->whereBetween('fecha_apertura', [$fechaDesde, $fechaHasta]);
        }
        return $query;
    }

    public function scopeSoloActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeSoloAbiertas($query)
    {
        return $query->where('estado', 'ABIERTA');
    }

    public function scopeSoloCerradas($query)
    {
        return $query->where('estado', 'CERRADA');
    }

    public function scopeBuscarUsuario($query, $usuarioId)
    {
        if ($usuarioId) {
            return $query->where('usuario_id', $usuarioId);
        }
        return $query;
    }

    // Métodos de negocio
    public function cerrar(): bool
    {
        if ($this->estado !== 'ABIERTA') {
            return false;
        }

        $this->update(['estado' => 'CERRADA']);
        return true;
    }

    public function cancelar(): bool
    {
        if ($this->estado !== 'ABIERTA') {
            return false;
        }

        $this->update(['estado' => 'CANCELADA']);
        return true;
    }

    public static function obtenerAperturaActual($puntoExpedicionId): ?self
    {
        return self::where('punto_expedicion_id', $puntoExpedicionId)
            ->where('estado', 'ABIERTA')
            ->latest('fecha_apertura')
            ->latest('hora_apertura')
            ->first();
    }

    public static function tieneAperturaAbierta($puntoExpedicionId): bool
    {
        return self::where('punto_expedicion_id', $puntoExpedicionId)
            ->where('estado', 'ABIERTA')
            ->exists();
    }
}
