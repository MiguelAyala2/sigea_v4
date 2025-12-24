<?php

namespace App\Models\Servicios;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Cliente extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'servicios.CLIENTES';

    protected $fillable = [
        'tipo_cliente',
        'documento',
        'nombre',
        'telefono',
        'celular',
        'email',
        'direccion',
        'observaciones',
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

    public const TIPOS_CLIENTE = [
        'fisica' => 'Persona Física',
        'juridica' => 'Persona Jurídica',
    ];

    // ==================== RELACIONES ====================

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    // ==================== ACCESSORS ====================

    public function getTipoClienteTextoAttribute(): string
    {
        return self::TIPOS_CLIENTE[$this->tipo_cliente] ?? $this->tipo_cliente;
    }

    public function getDocumentoFormateadoAttribute(): string
    {
        return $this->documento;
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('nombre', "%{$search}%")
                ->orWhereLike('documento', "%{$search}%")
                ->orWhereLike('email', "%{$search}%")
                ->orWhereLike('telefono', "%{$search}%")
                ->orWhereLike('celular', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarTipo(Builder $query, $tipo = null): void
    {
        $query->when($tipo, function (Builder $query, string $tipo) {
            $query->where('tipo_cliente', $tipo);
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
