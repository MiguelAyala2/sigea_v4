<?php

namespace App\Models\Empresa;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Sucursal extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'empresa.SUCURSALES';

    protected $fillable = [
        'empresa_id',
        'codigo_establecimiento',
        'nombre',
        'direccion',
        'departamento',
        'ciudad',
        'telefono',
        'email',
        'es_casa_central',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'es_casa_central' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    // ==================== RELACIONES ====================

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function depositos(): HasMany
    {
        return $this->hasMany(Deposito::class, 'sucursal_id');
    }

    public function puntosExpedicion(): HasMany
    {
        return $this->hasMany(PuntoExpedicion::class, 'sucursal_id');
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
        return $this->codigo_establecimiento . ' - ' . $this->nombre;
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('nombre', "%{$search}%")
                ->orWhereLike('codigo_establecimiento', "%{$search}%")
                ->orWhereLike('ciudad', "%{$search}%");
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
    protected function buscarCodigo(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('codigo_establecimiento', "%{$search}%");
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
