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

class PuntoExpedicion extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'empresa.PUNTOS_EXPEDICION';

    protected $fillable = [
        'sucursal_id',
        'codigo',
        'nombre',
        'tipo',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    // ==================== CONSTANTES ====================

    public const TIPOS = [
        'caja' => 'Caja',
        'terminal' => 'Terminal',
        'web' => 'Web',
    ];

    // ==================== RELACIONES ====================

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
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

    public function getNombreCompletoAttribute(): string
    {
        return $this->codigo . ' - ' . $this->nombre;
    }

    public function getTipoTextoAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getCodigoCompletoAttribute(): string
    {
        return $this->sucursal->codigo_establecimiento . '-' . $this->codigo;
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('nombre', "%{$search}%")
                ->orWhereLike('codigo', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarTipo(Builder $query, $tipo = null): void
    {
        $query->when($tipo, function (Builder $query, $tipo) {
            $query->where('tipo', $tipo);
        });
    }

    #[Scope]
    protected function buscarSucursal(Builder $query, $sucursalId = null): void
    {
        $query->when($sucursalId, function (Builder $query, $sucursalId) {
            $query->where('sucursal_id', $sucursalId);
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
}
