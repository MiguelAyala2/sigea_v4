<?php

namespace App\Models\Empresa;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ActividadEconomica extends Model
{
    use HasFactory;

    protected $table = 'empresa.ACTIVIDADES_ECONOMICAS';

    protected $fillable = [
        'codigo',
        'descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    // ==================== RELACIONES ====================

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(
            Empresa::class,
            'empresa.EMPRESA_ACTIVIDAD_ECONOMICA',
            'actividad_economica_id',
            'empresa_id'
        )->withPivot('es_principal')->withTimestamps();
    }

    // ==================== ACCESSORS ====================

    public function getNombreCompletoAttribute(): string
    {
        return $this->codigo . ' - ' . $this->descripcion;
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('codigo', "%{$search}%")
                ->orWhereLike('descripcion', "%{$search}%");
        });
    }

    #[Scope]
    protected function soloActivos(Builder $query): void
    {
        $query->where('activo', true);
    }
}