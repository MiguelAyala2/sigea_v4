<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use OwenIt\Auditing\Contracts\Auditable;

class Marca extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'stock.MARCAS';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'activo',
        'creadoPor', 'actualizadoPor'
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // RELACIONES
    public function productos()
    {
        return $this->hasMany(Producto::class, 'marca_id');
    }

    public function creadoPorUsuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'actualizadoPor');
    }

    // BOOT
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($marca) {
            if (empty($marca->codigo)) {
                $ultimoId = static::withTrashed()->max('id') ?? 0;
                $marca->codigo = 'MRC-' . str_pad($ultimoId + 1, 6, '0', STR_PAD_LEFT);
            }
        });
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
    protected function activas(Builder $query): void
    {
        $query->where('activo', true);
    }
}