<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use OwenIt\Auditing\Contracts\Auditable;

class RecaudacionADepositar extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'ventas.RECAUDACIONES_A_DEPOSITAR';

    protected $fillable = [
        'apertura_caja_id',
        'cierre_caja_id',
        'fecha_recaudacion',
        'tipo_recaudacion',
        'monto',
        'banco',
        'cuenta',
        'numero_documento',
        'fecha_prevista_deposito',
        'fecha_real_deposito',
        'estado',
        'usuario_registra_id',
        'usuario_deposita_id',
        'comprobante_deposito',
        'observaciones',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_recaudacion' => 'date',
        'fecha_prevista_deposito' => 'date',
        'fecha_real_deposito' => 'date',
        'monto' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Constantes
    const TIPOS_RECAUDACION = [
        'EFECTIVO' => 'Efectivo',
        'CHEQUE' => 'Cheque',
        'TARJETA' => 'Tarjeta',
        'TRANSFERENCIA' => 'Transferencia',
        'MIXTO' => 'Mixto',
    ];

    const ESTADOS = [
        'PENDIENTE' => 'Pendiente',
        'DEPOSITADO' => 'Depositado',
        'RECHAZADO' => 'Rechazado',
        'CANCELADO' => 'Cancelado',
    ];

    // Relaciones
    public function aperturaCaja(): BelongsTo
    {
        return $this->belongsTo(AperturaCaja::class, 'apertura_caja_id');
    }

    public function cierreCaja(): BelongsTo
    {
        return $this->belongsTo(CierreCaja::class, 'cierre_caja_id');
    }

    public function usuarioRegistra(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_registra_id');
    }

    public function usuarioDeposita(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_deposita_id');
    }

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    // Accessors
    public function getTipoRecaudacionTextoAttribute(): string
    {
        return self::TIPOS_RECAUDACION[$this->tipo_recaudacion] ?? $this->tipo_recaudacion;
    }

    public function getEstadoTextoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getEstaPendienteAttribute(): bool
    {
        return $this->estado === 'PENDIENTE';
    }

    public function getEstaDepositadoAttribute(): bool
    {
        return $this->estado === 'DEPOSITADO';
    }

    public function getDiasVencimientoAttribute(): ?int
    {
        if (!$this->fecha_prevista_deposito || $this->esta_depositado) {
            return null;
        }

        return now()->startOfDay()->diffInDays($this->fecha_prevista_deposito, false);
    }

    public function getEstaVencidoAttribute(): bool
    {
        return $this->dias_vencimiento !== null && $this->dias_vencimiento < 0;
    }

    // Scopes
    public function scopeBuscador($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('numero_documento', 'ILIKE', "%{$search}%")
                  ->orWhere('banco', 'ILIKE', "%{$search}%")
                  ->orWhere('comprobante_deposito', 'ILIKE', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeBuscarTipo($query, $tipo)
    {
        if ($tipo) {
            return $query->where('tipo_recaudacion', $tipo);
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
            return $query->whereDate('fecha_recaudacion', $fecha);
        }
        return $query;
    }

    public function scopeBuscarRangoFechas($query, $fechaDesde, $fechaHasta)
    {
        if ($fechaDesde && $fechaHasta) {
            return $query->whereBetween('fecha_recaudacion', [$fechaDesde, $fechaHasta]);
        }
        return $query;
    }

    public function scopeSoloActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeSoloPendientes($query)
    {
        return $query->where('estado', 'PENDIENTE');
    }

    public function scopeSoloDepositados($query)
    {
        return $query->where('estado', 'DEPOSITADO');
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', 'PENDIENTE')
            ->where('fecha_prevista_deposito', '<', now());
    }

    public function scopeProximosAVencer($query, $dias = 3)
    {
        return $query->where('estado', 'PENDIENTE')
            ->whereBetween('fecha_prevista_deposito', [now(), now()->addDays($dias)]);
    }

    // Métodos de negocio
    public function marcarComoDepositado(string $comprobanteDeposito): bool
    {
        if ($this->estado !== 'PENDIENTE') {
            return false;
        }

        $this->update([
            'estado' => 'DEPOSITADO',
            'fecha_real_deposito' => now(),
            'comprobante_deposito' => $comprobanteDeposito,
            'usuario_deposita_id' => auth()->id(),
        ]);

        return true;
    }

    public function cancelar(string $motivo): bool
    {
        if ($this->estado === 'DEPOSITADO') {
            return false;
        }

        $this->update([
            'estado' => 'CANCELADO',
            'observaciones' => ($this->observaciones ?? '') . "\n\nCancelado: {$motivo}",
        ]);

        return true;
    }
}
