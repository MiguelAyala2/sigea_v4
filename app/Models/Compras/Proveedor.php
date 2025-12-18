<?php

namespace App\Models\Compras;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Proveedor extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'compras.proveedores';

    protected $fillable = [
        'razon_social',
        'nombre_fantasia',
        'ruc',
        'dv',
        'tipo_persona',
        'tipo_proveedor',
        'telefono',
        'celular',
        'email',
        'sitio_web',
        'direccion',
        'ciudad',
        'departamento',
        'pais',
        'contacto_nombre',
        'contacto_cargo',
        'contacto_telefono',
        'contacto_email',
        'dias_plazo_pago',
        'limite_credito',
        'descuento_habitual',
        'banco',
        'tipo_cuenta',
        'numero_cuenta',
        'activo',
        'es_nacional',
        'contribuyente',
        'observaciones',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'es_nacional' => 'boolean',
            'contribuyente' => 'boolean',
            'dias_plazo_pago' => 'integer',
            'limite_credito' => 'decimal:2',
            'descuento_habitual' => 'decimal:2',
        ];
    }

    // ==================== CONSTANTES ====================

    public const TIPOS_PERSONA = [
        'FISICA' => 'Persona Física',
        'JURIDICA' => 'Persona Jurídica',
    ];

    public const TIPOS_PROVEEDOR = [
        'PRODUCTOS' => 'Productos',
        'SERVICIOS' => 'Servicios',
        'AMBOS' => 'Productos y Servicios',
    ];

    // ==================== RELACIONES ====================

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'proveedor_id');
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

    public function getTipoPersonaTextoAttribute(): string
    {
        return self::TIPOS_PERSONA[$this->tipo_persona] ?? $this->tipo_persona;
    }

    public function getTipoProveedorTextoAttribute(): string
    {
        return self::TIPOS_PROVEEDOR[$this->tipo_proveedor] ?? $this->tipo_proveedor;
    }

    public function getRucFormateadoAttribute(): string
    {
        return $this->ruc . ($this->dv ? '-' . $this->dv : '');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre_fantasia ?: $this->razon_social;
    }

    public function getEstadoTextoAttribute(): string
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    public function getCondicionPagoAttribute(): string
    {
        if ($this->dias_plazo_pago == 0) {
            return 'Contado';
        }
        return $this->dias_plazo_pago . ' días';
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function activos(Builder $query): void
    {
        $query->where('activo', true);
    }

    #[Scope]
    protected function inactivos(Builder $query): void
    {
        $query->where('activo', false);
    }

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->where('razon_social', 'like', "%{$search}%")
                ->orWhere('nombre_fantasia', 'like', "%{$search}%")
                ->orWhere('ruc', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    #[Scope]
    protected function porTipo(Builder $query, $tipo): void
    {
        $query->when($tipo, function (Builder $query, string $tipo) {
            $query->where('tipo_proveedor', $tipo);
        });
    }

    #[Scope]
    protected function contribuyentes(Builder $query): void
    {
        $query->where('contribuyente', true);
    }

    // ==================== MÉTODOS ====================

    /**
     * Verifica si el proveedor está activo
     */
    public function estaActivo(): bool
    {
        return $this->activo === true;
    }

    /**
     * Activa el proveedor
     */
    public function activar(): void
    {
        $this->update(['activo' => true]);
    }

    /**
     * Desactiva el proveedor
     */
    public function desactivar(): void
    {
        $this->update(['activo' => false]);
    }

    /**
     * Calcula el total de compras del proveedor
     */
    public function getTotalCompras(): float
    {
        return $this->compras()->sum('total');
    }

    /**
     * Obtiene la última compra realizada al proveedor
     */
    public function getUltimaCompra()
    {
        return $this->compras()->latest('fecha_emision')->first();
    }
}
