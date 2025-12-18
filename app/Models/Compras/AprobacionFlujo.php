<?php

namespace App\Models\Compras;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class AprobacionFlujo extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'compras.compras_aprobacion_flujo';

    protected $fillable = [
        'documento_tipo',
        'documento_id',
        'estado',
        'nivel_aprobacion',
        'secuencia',
        'usuario_aprobador_id',
        'rol_requerido',
        'fecha_asignacion',
        'fecha_aprobacion',
        'fecha_vencimiento',
        'comentarios',
        'observaciones_rechazo',
        'adjunto_firma_path',
        'adjunto_comprobante_path',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
            'fecha_aprobacion' => 'datetime',
            'fecha_vencimiento' => 'datetime',
            'nivel_aprobacion' => 'integer',
            'secuencia' => 'integer',
        ];
    }

    // ==================== CONSTANTES ====================

    public const TIPOS_DOCUMENTO = [
        'PEDIDO_COMPRA' => 'Pedido de Compra',
        'PRESUPUESTO' => 'Presupuesto',
        'ORDEN_COMPRA' => 'Orden de Compra',
        'COMPRA' => 'Compra/Factura',
        'RECEPCION' => 'Recepción',
        'PAGO' => 'Pago',
    ];

    public const ESTADOS = [
        'PENDIENTE' => 'Pendiente',
        'APROBADO' => 'Aprobado',
        'RECHAZADO' => 'Rechazado',
        'OBSERVADO' => 'Observado',
    ];

    // ==================== RELACIONES POLIMÓRFICAS ====================

    /**
     * Obtiene el documento relacionado (pedido, presupuesto, orden, compra, recepción, pago)
     */
    public function documento(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'documento_tipo', 'documento_id');
    }

    /**
     * Métodos específicos para cada tipo de documento
     */
    public function pedidoCompra(): BelongsTo
    {
        return $this->belongsTo(PedidoCompra::class, 'documento_id')
            ->where('documento_tipo', 'PEDIDO_COMPRA');
    }

    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(PresupuestoProveedor::class, 'documento_id')
            ->where('documento_tipo', 'PRESUPUESTO');
    }

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'documento_id')
            ->where('documento_tipo', 'ORDEN_COMPRA');
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'documento_id')
            ->where('documento_tipo', 'COMPRA');
    }

    public function recepcion(): BelongsTo
    {
        return $this->belongsTo(CompraRecepcion::class, 'documento_id')
            ->where('documento_tipo', 'RECEPCION');
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(PagoProveedor::class, 'documento_id')
            ->where('documento_tipo', 'PAGO');
    }

    // ==================== RELACIONES DIRECTAS ====================

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_aprobador_id');
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

    public function getDocumentoTipoTextoAttribute(): string
    {
        return self::TIPOS_DOCUMENTO[$this->documento_tipo] ?? $this->documento_tipo;
    }

    public function getEstadoTextoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getNivelAprobacionTextoAttribute(): string
    {
        return "Nivel {$this->nivel_aprobacion}";
    }

    public function getFechaAprobacionFormateadaAttribute(): string
    {
        return $this->fecha_aprobacion?->format('d/m/Y H:i') ?? '';
    }

    public function getFechaVencimientoFormateadaAttribute(): string
    {
        return $this->fecha_vencimiento?->format('d/m/Y') ?? '';
    }

    public function getEstaVencidaAttribute(): bool
    {
        if (!$this->fecha_vencimiento) {
            return false;
        }

        return now()->greaterThan($this->fecha_vencimiento) && $this->estado === 'PENDIENTE';
    }

    public function getTiempoRestanteAttribute(): ?string
    {
        if (!$this->fecha_vencimiento || $this->estado !== 'PENDIENTE') {
            return null;
        }

        $diferencia = now()->diff($this->fecha_vencimiento);
        
        if ($diferencia->invert) {
            return "Vencido hace {$diferencia->d} días";
        }

        if ($diferencia->d > 0) {
            return "{$diferencia->d} días";
        }

        return "{$diferencia->h} horas";
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->where('comentarios', 'like', "%{$search}%")
                ->orWhere('rol_requerido', 'like', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarTipoDocumento(Builder $query, $tipo): void
    {
        $query->when($tipo, function (Builder $query, string $tipo) {
            $query->where('documento_tipo', $tipo);
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
    protected function buscarNivel(Builder $query, $nivel): void
    {
        $query->when($nivel, function (Builder $query, int $nivel) {
            $query->where('nivel_aprobacion', $nivel);
        });
    }

    #[Scope]
    protected function buscarUsuarioAprobador(Builder $query, $usuarioId): void
    {
        $query->when($usuarioId, function (Builder $query, $usuarioId) {
            $query->where('usuario_aprobador_id', $usuarioId);
        });
    }

    #[Scope]
    protected function buscarRolRequerido(Builder $query, $rol): void
    {
        $query->when($rol, function (Builder $query, string $rol) {
            $query->where('rol_requerido', $rol);
        });
    }

    #[Scope]
    protected function pendientes(Builder $query): void
    {
        $query->where('estado', 'PENDIENTE');
    }

    #[Scope]
    protected function aprobados(Builder $query): void
    {
        $query->where('estado', 'APROBADO');
    }

    #[Scope]
    protected function rechazados(Builder $query): void
    {
        $query->where('estado', 'RECHAZADO');
    }

    #[Scope]
    protected function vencidas(Builder $query): void
    {
        $query->where('estado', 'PENDIENTE')
            ->where('fecha_vencimiento', '<', now());
    }

    #[Scope]
    protected function porUsuarioActual(Builder $query): void
    {
        $query->where('usuario_aprobador_id', auth()->id())
            ->where('estado', 'PENDIENTE');
    }

    // ==================== MÉTODOS ====================

    /**
     * Aprobar la solicitud
     */
    public function aprobar(string $comentarios = null, ?string $firmaPath = null): bool
    {
        return $this->update([
            'estado' => 'APROBADO',
            'fecha_aprobacion' => now(),
            'comentarios' => $comentarios ?? $this->comentarios,
            'adjunto_firma_path' => $firmaPath ?? $this->adjunto_firma_path,
        ]);
    }

    /**
     * Rechazar la solicitud
     */
    public function rechazar(string $observaciones, ?string $comprobantePath = null): bool
    {
        return $this->update([
            'estado' => 'RECHAZADO',
            'fecha_aprobacion' => now(),
            'observaciones_rechazo' => $observaciones,
            'adjunto_comprobante_path' => $comprobantePath ?? $this->adjunto_comprobante_path,
        ]);
    }

    /**
     * Marcar como observado
     */
    public function observar(string $comentarios): bool
    {
        return $this->update([
            'estado' => 'OBSERVADO',
            'comentarios' => $comentarios,
        ]);
    }

    /**
     * Verificar si está pendiente
     */
    public function estaPendiente(): bool
    {
        return $this->estado === 'PENDIENTE';
    }

    /**
     * Verificar si está aprobado
     */
    public function estaAprobado(): bool
    {
        return $this->estado === 'APROBADO';
    }

    /**
     * Verificar si está rechazado
     */
    public function estaRechazado(): bool
    {
        return $this->estado === 'RECHAZADO';
    }
}
