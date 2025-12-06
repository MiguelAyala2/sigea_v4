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

class Deposito extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'empresa.DEPOSITOS';

    protected $fillable = [
        'sucursal_id',
        'codigo',
        'nombre',
        'descripcion',
        'es_principal',
        'permite_venta',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
            'permite_venta' => 'boolean',
            'activo' => 'boolean',
        ];
    }

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

    public function getUbicacionCompletaAttribute(): string
    {
        return $this->sucursal->nombre . ' / ' . $this->nombre;
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
    protected function buscarNombre(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('nombre', "%{$search}%");
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

    #[Scope]
    protected function permiteVenta(Builder $query): void
    {
        $query->where('permite_venta', true);
    }
}