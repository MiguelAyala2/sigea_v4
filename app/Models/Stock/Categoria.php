<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use OwenIt\Auditing\Contracts\Auditable;

class Categoria extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'stock.CATEGORIAS';

    protected $fillable = [
        'parent_id', 'codigo', 'nombre', 'descripcion', 'nivel', 'activo',
        'creadoPor', 'actualizadoPor'
    ];

    protected function casts(): array
    {
        return [
            'nivel' => 'integer',
            'activo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // RELACIONES
    public function parent()
    {
        return $this->belongsTo(Categoria::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Categoria::class, 'parent_id');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
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

        static::creating(function ($categoria) {
            if (empty($categoria->codigo)) {
                $categoria->codigo = $categoria->generarCodigoAutomatico();
            }

            // Calcular nivel automáticamente
            if ($categoria->parent_id) {
                $parent = self::find($categoria->parent_id);
                $categoria->nivel = $parent ? $parent->nivel + 1 : 1;
            } else {
                $categoria->nivel = 1;
            }
        });
    }

    // ACCESSORS
    public function getNombreIndentadoAttribute(): string
    {
        return str_repeat('--', $this->nivel - 1) . ' ' . $this->nombre;
    }

    public function getEsRaizAttribute(): bool
    {
        return $this->parent_id === null;
    }

    public function getNombreCompletoAttribute(): string
    {
        $nombres = collect([$this->nombre]);
        $categoria = $this;

        while ($categoria->parent) {
            $nombres->prepend($categoria->parent->nombre);
            $categoria = $categoria->parent;
        }

        return $nombres->implode(' → ');
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
    protected function raices(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    #[Scope]
    protected function hijosDe(Builder $query, $parent_id): void
    {
        $query->where('parent_id', $parent_id);
    }

    #[Scope]
    protected function activas(Builder $query): void
    {
        $query->where('activo', true);
    }

    #[Scope]
    protected function ordenadas(Builder $query): void
    {
        $query->orderBy('codigo');
    }

    // MÉTODOS
    public function descendants()
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }

    public function ancestors()
    {
        $ancestors = collect();
        $categoria = $this;

        while ($categoria->parent) {
            $ancestors->push($categoria->parent);
            $categoria = $categoria->parent;
        }

        return $ancestors->reverse();
    }

    private function generarCodigoAutomatico(): string
    {
        if ($this->parent_id) {
            // Es una subcategoría
            $parent = self::find($this->parent_id);
            if ($parent) {
                $ultimoHijo = self::where('parent_id', $this->parent_id)
                    ->withTrashed()
                    ->orderBy('id', 'desc')
                    ->first();

                $siguienteNumero = $ultimoHijo ? ($ultimoHijo->id + 1) : 1;
                $numero = str_pad($siguienteNumero, 2, '0', STR_PAD_LEFT);
                return $parent->codigo . '.' . $numero;
            }
        }

        // Es una categoría raíz
        $ultimaRaiz = self::whereNull('parent_id')
            ->withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        $siguienteNumero = $ultimaRaiz ? ($ultimaRaiz->id + 1) : 1;
        return str_pad($siguienteNumero, 2, '0', STR_PAD_LEFT);
    }
}