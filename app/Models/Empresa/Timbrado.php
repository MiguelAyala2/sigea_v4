<?php

namespace App\Models\Empresa;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Timbrado extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'empresa.TIMBRADOS';

    protected $fillable = [
        'empresa_id',
        'numero_timbrado',
        'fecha_inicio_vigencia',
        'fecha_fin_vigencia',
        'tipo_documento',
        'numero_desde',
        'numero_hasta',
        'numero_actual',
        'es_electronico',
        'cdc_ambiente',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio_vigencia' => 'date',
            'fecha_fin_vigencia' => 'date',
            'es_electronico' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    // ==================== CONSTANTES ====================

    public const TIPOS_DOCUMENTO = [
        'factura' => 'Factura',
        'nota_credito' => 'Nota de Crédito',
        'nota_debito' => 'Nota de Débito',
        'remision' => 'Nota de Remisión',
        'retencion' => 'Comprobante de Retención',
    ];

    public const AMBIENTES = [
        'produccion' => 'Producción',
        'test' => 'Test/Pruebas',
    ];

    // ==================== RELACIONES ====================

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
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

    public function getTipoDocumentoTextoAttribute(): string
    {
        return self::TIPOS_DOCUMENTO[$this->tipo_documento] ?? $this->tipo_documento;
    }

    public function getAmbienteTextoAttribute(): string
    {
        return self::AMBIENTES[$this->cdc_ambiente] ?? $this->cdc_ambiente ?? 'N/A';
    }

    public function getRangoNumeracionAttribute(): string
    {
        return number_format($this->numero_desde, 0, '', '.') . ' - ' . number_format($this->numero_hasta, 0, '', '.');
    }

    public function getNumerosDisponiblesAttribute(): int
    {
        return $this->numero_hasta - $this->numero_actual;
    }

    public function getPorcentajeUsoAttribute(): float
    {
        $total = $this->numero_hasta - $this->numero_desde + 1;
        $usado = $this->numero_actual - $this->numero_desde + 1;

        if ($usado <= 0 || $total <= 0) {
            return 0;
        }

        return round(($usado / $total) * 100, 2);
    }

    public function getEstaVigenteAttribute(): bool
    {
        $hoy = now()->toDateString();
        return $this->fecha_inicio_vigencia <= $hoy && $this->fecha_fin_vigencia >= $hoy;
    }

    public function getEstaAgotadoAttribute(): bool
    {
        return $this->numero_actual >= $this->numero_hasta;
    }

    public function getProximoAVencerAttribute(): bool
    {
        return $this->fecha_fin_vigencia->diffInDays(now()) <= 30;
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('numero_timbrado', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarTipoDocumento(Builder $query, $tipo = null): void
    {
        $query->when($tipo, function (Builder $query, $tipo) {
            $query->where('tipo_documento', $tipo);
        });
    }

    #[Scope]
    protected function buscarActivo(Builder $query, $search): void
    {
        $query->when($search !== null && $search !== '', function (Builder $query) use ($search) {
            $query->where('activo', $search);
        });
    }

    #[Scope]
    protected function soloActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    #[Scope]
    protected function vigentes(Builder $query): void
    {
        $hoy = now()->toDateString();
        $query->where('fecha_inicio_vigencia', '<=', $hoy)
            ->where('fecha_fin_vigencia', '>=', $hoy);
    }

    // ==================== MÉTODOS ====================

    public function obtenerSiguienteNumero(): ?int
    {
        if ($this->esta_agotado || !$this->esta_vigente) {
            return null;
        }

        $siguiente = $this->numero_actual + 1;

        if ($siguiente > $this->numero_hasta) {
            return null;
        }

        $this->update(['numero_actual' => $siguiente]);

        return $siguiente;
    }
}