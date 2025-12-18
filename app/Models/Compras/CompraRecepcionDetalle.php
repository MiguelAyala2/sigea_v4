<?php

namespace App\Models\Compras;

use App\Models\Stock\Producto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class CompraRecepcionDetalle extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'compras.compras_recepcion_detalle';

    protected $fillable = [
        'recepcion_id',
        'compra_detalle_id',
        'producto_id',
        'cantidad_esperada',
        'cantidad_recibida',
        'cantidad_aceptada',
        'cantidad_rechazada',
        'diferencia',
        'motivo_diferencia',
        'lote',
        'fecha_vencimiento',
        'observaciones',
        'ubicacion_almacen',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'cantidad_esperada' => 'decimal:3',
            'cantidad_recibida' => 'decimal:3',
            'cantidad_aceptada' => 'decimal:3',
            'cantidad_rechazada' => 'decimal:3',
            'diferencia' => 'decimal:3',
            'fecha_vencimiento' => 'date',
        ];
    }

    // ==================== RELACIONES ====================

    public function recepcion(): BelongsTo
    {
        return $this->belongsTo(CompraRecepcion::class, 'recepcion_id');
    }

    public function compraDetalle(): BelongsTo
    {
        return $this->belongsTo(CompraDetalle::class, 'compra_detalle_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // ==================== ACCESSORS ====================

    public function getCantidadEsperadaFormateadaAttribute(): string
    {
        return number_format($this->cantidad_esperada, 2, ',', '.');
    }

    public function getCantidadRecibidaFormateadaAttribute(): string
    {
        return number_format($this->cantidad_recibida, 2, ',', '.');
    }

    public function getCantidadAceptadaFormateadaAttribute(): string
    {
        return number_format($this->cantidad_aceptada, 2, ',', '.');
    }

    public function getCantidadRechazadaFormateadaAttribute(): string
    {
        return number_format($this->cantidad_rechazada, 2, ',', '.');
    }

    public function getDiferenciaFormateadaAttribute(): string
    {
        return number_format($this->diferencia, 2, ',', '.');
    }

    public function getPorcentajeRecibidoAttribute(): float
    {
        if ($this->cantidad_esperada <= 0) {
            return 0.00;
        }

        return round(($this->cantidad_recibida / $this->cantidad_esperada) * 100, 2);
    }

    public function getPorcentajeAceptadoAttribute(): float
    {
        if ($this->cantidad_recibida <= 0) {
            return 0.00;
        }

        return round(($this->cantidad_aceptada / $this->cantidad_recibida) * 100, 2);
    }

    public function getTieneDiferenciaAttribute(): bool
    {
        return abs($this->diferencia) > 0.001; // Tolerancia de 0.001
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function porRecepcion(Builder $query, $recepcionId): void
    {
        $query->when($recepcionId, function (Builder $query, $recepcionId) {
            $query->where('recepcion_id', $recepcionId);
        });
    }

    #[Scope]
    protected function porProducto(Builder $query, $productoId): void
    {
        $query->when($productoId, function (Builder $query, $productoId) {
            $query->where('producto_id', $productoId);
        });
    }

    #[Scope]
    protected function conDiferencias(Builder $query): void
    {
        $query->whereRaw('ABS(diferencia) > 0.001');
    }

    #[Scope]
    protected function conRechazo(Builder $query): void
    {
        $query->where('cantidad_rechazada', '>', 0);
    }

    #[Scope]
    protected function aceptados(Builder $query): void
    {
        $query->whereColumn('cantidad_recibida', '=', 'cantidad_aceptada');
    }

    // ==================== MÉTODOS ====================

    /**
     * Calcula la diferencia entre lo esperado y lo recibido
     */
    public function calcularDiferencia(): float
    {
        return $this->cantidad_recibida - $this->cantidad_esperada;
    }

    /**
     * Verifica si hay diferencia significativa
     */
    public function tieneDiferenciaSignificativa(float $tolerancia = 0.001): bool
    {
        return abs($this->calcularDiferencia()) > $tolerancia;
    }

    /**
     * Actualiza las cantidades y calcula la diferencia
     */
    public function actualizarCantidades(
        float $cantidadRecibida,
        float $cantidadAceptada = null,
        float $cantidadRechazada = null
    ): void {
        $cantidadAceptada = $cantidadAceptada ?? $cantidadRecibida;
        $cantidadRechazada = $cantidadRechazada ?? 0;

        $this->update([
            'cantidad_recibida' => $cantidadRecibida,
            'cantidad_aceptada' => $cantidadAceptada,
            'cantidad_rechazada' => $cantidadRechazada,
            'diferencia' => $this->calcularDiferencia(),
        ]);
    }
}
