<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use OwenIt\Auditing\Contracts\Auditable;

class CierreCaja extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'ventas.CIERRES_CAJA';

    protected $fillable = [
        'apertura_caja_id',
        'usuario_id',
        'fecha_cierre',
        'hora_cierre',
        'saldo_inicial',
        'total_ingresos',
        'total_egresos',
        'saldo_calculado',
        'saldo_declarado',
        'diferencia',
        'total_efectivo',
        'total_cheques',
        'total_tarjetas',
        'total_transferencias',
        'total_otros',
        'estado',
        'observaciones',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_cierre' => 'date',
        'saldo_inicial' => 'decimal:2',
        'total_ingresos' => 'decimal:2',
        'total_egresos' => 'decimal:2',
        'saldo_calculado' => 'decimal:2',
        'saldo_declarado' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'total_efectivo' => 'decimal:2',
        'total_cheques' => 'decimal:2',
        'total_tarjetas' => 'decimal:2',
        'total_transferencias' => 'decimal:2',
        'total_otros' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Constantes
    const ESTADOS = [
        'CERRADO_CORRECTO' => 'Cerrado Correcto',
        'CERRADO_CON_DIFERENCIA' => 'Cerrado con Diferencia',
        'CERRADO_CON_FALTANTE' => 'Cerrado con Faltante',
        'CERRADO_CON_SOBRANTE' => 'Cerrado con Sobrante',
    ];

    // Relaciones
    public function aperturaCaja(): BelongsTo
    {
        return $this->belongsTo(AperturaCaja::class, 'apertura_caja_id');
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

    public function arqueos(): HasMany
    {
        return $this->hasMany(ArqueoCaja::class, 'cierre_caja_id');
    }

    public function recaudaciones(): HasMany
    {
        return $this->hasMany(RecaudacionADepositar::class, 'cierre_caja_id');
    }

    // Accessors
    public function getEstadoTextoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getTieneDiferenciaAttribute(): bool
    {
        return abs($this->diferencia) > 0.01;
    }

    public function getEsFaltanteAttribute(): bool
    {
        return $this->diferencia < -0.01;
    }

    public function getEsSobranteAttribute(): bool
    {
        return $this->diferencia > 0.01;
    }

    public function getDiferenciaFormateadaAttribute(): string
    {
        $color = $this->es_faltante ? 'danger' : ($this->es_sobrante ? 'success' : 'secondary');
        $signo = $this->diferencia > 0 ? '+' : '';
        return "<span class='text-{$color}'>{$signo} ₲ " . number_format($this->diferencia, 0, ',', '.') . "</span>";
    }

    public function getPorcentajeDiferenciaAttribute(): float
    {
        if ($this->saldo_calculado == 0) {
            return 0;
        }
        return ($this->diferencia / $this->saldo_calculado) * 100;
    }

    // Scopes
    public function scopeBuscador($query, $search)
    {
        if ($search) {
            return $query->whereHas('aperturaCaja.puntoExpedicion', function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('codigo', 'ILIKE', "%{$search}%");
            });
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
            return $query->whereDate('fecha_cierre', $fecha);
        }
        return $query;
    }

    public function scopeBuscarRangoFechas($query, $fechaDesde, $fechaHasta)
    {
        if ($fechaDesde && $fechaHasta) {
            return $query->whereBetween('fecha_cierre', [$fechaDesde, $fechaHasta]);
        }
        return $query;
    }

    public function scopeSoloActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConDiferencia($query)
    {
        return $query->whereRaw('ABS(diferencia) > 0.01');
    }

    public function scopeSinDiferencia($query)
    {
        return $query->whereRaw('ABS(diferencia) <= 0.01');
    }

    // Métodos de negocio
    public function calcularEstado(): string
    {
        if (abs($this->diferencia) <= 0.01) {
            return 'CERRADO_CORRECTO';
        } elseif ($this->diferencia < 0) {
            return 'CERRADO_CON_FALTANTE';
        } else {
            return 'CERRADO_CON_SOBRANTE';
        }
    }

    public function calcularTotalesPorFormaPago(): void
    {
        $movimientos = $this->aperturaCaja->movimientos;

        $this->total_efectivo = $movimientos->where('forma_pago', 'EFECTIVO')
            ->where('tipo_movimiento', 'INGRESO')
            ->sum('monto');

        $this->total_cheques = $movimientos->where('forma_pago', 'CHEQUE')
            ->where('tipo_movimiento', 'INGRESO')
            ->sum('monto');

        $this->total_tarjetas = $movimientos->whereIn('forma_pago', ['TARJETA_DEBITO', 'TARJETA_CREDITO'])
            ->where('tipo_movimiento', 'INGRESO')
            ->sum('monto');

        $this->total_transferencias = $movimientos->where('forma_pago', 'TRANSFERENCIA')
            ->where('tipo_movimiento', 'INGRESO')
            ->sum('monto');

        $this->total_otros = $movimientos->whereIn('forma_pago', ['QR', 'OTRO'])
            ->where('tipo_movimiento', 'INGRESO')
            ->sum('monto');
    }
}
