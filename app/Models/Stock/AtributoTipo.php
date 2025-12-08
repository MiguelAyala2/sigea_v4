<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use OwenIt\Auditing\Contracts\Auditable;

class AtributoTipo extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'stock.ATRIBUTOS_TIPO';

    protected $fillable = [
        'codigo', 'nombre', 'unidad', 'descripcion',
        'orden', 'es_filtrable', 'es_requerido', 'activo',
        'creadoPor', 'actualizadoPor'
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
            'es_filtrable' => 'boolean',
            'es_requerido' => 'boolean',
            'activo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // BOOT
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($atributo) {
            if (empty($atributo->codigo)) {
                $ultimoId = static::withTrashed()->max('id') ?? 0;
                $atributo->codigo = 'ATR-' . str_pad($ultimoId + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    // RELACIONES
    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'stock.ATRIBUTOS_PRODUCTO',
            'atributo_tipo_id',
            'producto_id'
        )->withPivot('valor')->withTimestamps();
    }

    public function creadoPorUsuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'actualizadoPor');
    }

    // ACCESSORS
    public function getNombreConUnidadAttribute(): string
    {
        if ($this->unidad) {
            return "{$this->nombre} ({$this->unidad})";
        }
        return $this->nombre;
    }

    // SCOPES
    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->where('nombre', 'ILIKE', "%{$search}%")
                ->orWhere('codigo', 'ILIKE', "%{$search}%");
        });
    }

    #[Scope]
    protected function activos(Builder $query): void
    {
        $query->where('activo', true);
    }

    #[Scope]
    protected function filtrables(Builder $query): void
    {
        $query->where('es_filtrable', true);
    }

    #[Scope]
    protected function requeridos(Builder $query): void
    {
        $query->where('es_requerido', true);
    }

    #[Scope]
    protected function ordenados(Builder $query): void
    {
        $query->orderBy('orden')->orderBy('nombre');
    }
}
