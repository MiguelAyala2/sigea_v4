<?php

namespace App\Models\Compras;

use App\Models\User;
use App\Models\Empresa\Deposito;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class CompraRecepcion extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'compras.compras_recepcion';

    protected $fillable = [
        'compra_id',
        'deposito_id',
        'fecha_recepcion',
        'estado',
        'porcentaje_recibido',
        'observaciones',
        'numero_remision',
        'guia_transporte',
        'fecha_remision',
        'usuario_receptor_id',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'fecha_recepcion' => 'date',
            'fecha_remision' => 'date',
            'porcentaje_recibido' => 'decimal:2',
            'estado' => 'string',
        ];
    }

    // ==================== CONSTANTES ====================

    public const ESTADOS = [
        'PENDIENTE' => 'Pendiente',
        'PARCIAL' => 'Parcial',
        'COMPLETA' => 'Completa',
        'RECHAZADO' => 'Rechazado',
    ];

    // ==================== RELACIONES ====================

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function deposito(): BelongsTo
    {
        return $this->belongsTo(Deposito::class, 'deposito_id');
    }

    public function receptor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_receptor_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CompraRecepcionDetalle::class, 'recepcion_id');
    }

    public function recepcionDetalles(): HasMany
    {
        return $this->hasMany(CompraRecepcionDetalle::class, 'recepcion_id');
    }

    public function aprobaciones(): HasMany
    {
        return $this->hasMany(AprobacionFlujo::class, 'documento_id')
            ->where('documento_tipo', 'RECEPCION');
    }

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    // ==================== ACCESSORS ====================

    public function getEstadoTextoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getFechaRecepcionFormateadaAttribute(): string
    {
        return $this->fecha_recepcion?->format('d/m/Y') ?? '';
    }

    public function getFechaRemisionFormateadaAttribute(): string
    {
        return $this->fecha_remision?->format('d/m/Y') ?? '';
    }

    public function getPorcentajeRecibidoFormateadoAttribute(): string
    {
        return number_format($this->porcentaje_recibido, 2, ',', '.') . '%';
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->where('numero_remision', 'like', "%{$search}%")
                ->orWhere('guia_transporte', 'like', "%{$search}%")
                ->orWhereHas('compra', function ($q) use ($search) {
                    $q->where('numero_factura', 'like', "%{$search}%");
                });
        });
    }

    #[Scope]
    protected function buscarEstado(Builder $query, $estado): void
    {
        $query->when($estado, function (Builder $query, string $estado) {
            $query->where('estado', $estado);
        });
    }

    #[Scope]
    protected function buscarCompra(Builder $query, $compraId): void
    {
        $query->when($compraId, function (Builder $query, $compraId) {
            $query->where('compra_id', $compraId);
        });
    }

    #[Scope]
    protected function buscarDeposito(Builder $query, $depositoId): void
    {
        $query->when($depositoId, function (Builder $query, $depositoId) {
            $query->where('deposito_id', $depositoId);
        });
    }

    #[Scope]
    protected function buscarFechaDesde(Builder $query, $fecha): void
    {
        $query->when($fecha, function (Builder $query, string $fecha) {
            $query->whereDate('fecha_recepcion', '>=', $fecha);
        });
    }

    #[Scope]
    protected function buscarFechaHasta(Builder $query, $fecha): void
    {
        $query->when($fecha, function (Builder $query, string $fecha) {
            $query->whereDate('fecha_recepcion', '<=', $fecha);
        });
    }

    #[Scope]
    protected function pendientes(Builder $query): void
    {
        $query->where('estado', 'PENDIENTE');
    }

    #[Scope]
    protected function completas(Builder $query): void
    {
        $query->where('estado', 'COMPLETA');
    }

    // ==================== MÉTODOS ====================

    /**
     * Verifica si la recepción está completa
     */
    public function estaCompleta(): bool
    {
        return $this->estado === 'COMPLETA';
    }

    /**
     * Verifica si la recepción está pendiente
     */
    public function estaPendiente(): bool
    {
        return $this->estado === 'PENDIENTE';
    }

    /**
     * Marca la recepción como completa
     */
    public function marcarComoCompleta(): void
    {
        $this->update([
            'estado' => 'COMPLETA',
            'porcentaje_recibido' => 100.00,
        ]);
    }

    /**
     * Marca la recepción como parcial con el porcentaje especificado
     */
    public function marcarComoParcial(float $porcentaje): void
    {
        $this->update([
            'estado' => 'PARCIAL',
            'porcentaje_recibido' => $porcentaje,
        ]);
    }
}
