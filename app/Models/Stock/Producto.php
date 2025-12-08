<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use OwenIt\Auditing\Contracts\Auditable;

class Producto extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'stock.PRODUCTOS';

    protected $fillable = [
        'codigo', 'codigo_barras', 'codigo_fabricante',
        'nombre', 'descripcion', 'modelo', 'aplicacion',
        'tipo', 'origen',
        'categoria_id', 'marca_id', 'unidad_medida_id',
        'permite_venta', 'permite_compra', 'maneja_stock', 'activo',
        'stock_minimo', 'stock_maximo',
        'creadoPor', 'actualizadoPor'
    ];

    protected function casts(): array
    {
        return [
            'permite_venta' => 'boolean',
            'permite_compra' => 'boolean',
            'maneja_stock' => 'boolean',
            'activo' => 'boolean',
            'stock_minimo' => 'decimal:2',
            'stock_maximo' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // CONSTANTES
    public const TIPOS = [
        'PRODUCTO' => 'Producto',
        'INSUMO' => 'Insumo',
        'SERVICIO' => 'Servicio',
        'KIT' => 'Kit'
    ];

    public const ORIGENES = [
        'NACIONAL' => 'Nacional',
        'IMPORTADO' => 'Importado'
    ];

    // BOOT
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($producto) {
            if (empty($producto->codigo)) {
                $ultimoId = static::withTrashed()->max('id') ?? 0;
                $producto->codigo = 'PRD-' . str_pad($ultimoId + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    // RELACIONES
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function atributos()
    {
        return $this->belongsToMany(
            AtributoTipo::class,
            'stock.ATRIBUTOS_PRODUCTO',
            'producto_id',
            'atributo_tipo_id'
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

    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class, 'producto_id')->orderBy('orden');
    }

    public function imagenPrincipal()
    {
        return $this->hasOne(ImagenProducto::class, 'producto_id')->where('es_principal', true);
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'producto_id');
    }

    public function movimientosStock()
    {
        return $this->hasMany(MovimientoStock::class, 'producto_id');
    }

    public function precios()
    {
        return $this->hasMany(Precio::class, 'producto_id');
    }

    public function precioActual()
    {
        return $this->hasOne(Precio::class, 'producto_id')->where('es_actual', true);
    }

    public function proveedores()
    {
        return $this->belongsToMany(
            ProductoProveedor::class,
            'stock.PRODUCTO_PROVEEDOR',
            'producto_id',
            'proveedor_id'
        )->withPivot([
            'codigo_proveedor',
            'precio_referencia',
            'tiempo_entrega_dias',
            'es_principal',
            'activo',
            'ultimo_precio_compra',
            'ultima_compra_fecha'
        ])->withTimestamps();
    }

    public function productosProveedores()
    {
        return $this->hasMany(ProductoProveedor::class, 'producto_id');
    }

    // ACCESSORS
    public function getTipoTextoAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getOrigenTextoAttribute(): string
    {
        return self::ORIGENES[$this->origen] ?? $this->origen;
    }

    public function getNombreCompletoAttribute(): string
    {
        $partes = [$this->nombre];

        if ($this->marca) {
            $partes[] = "({$this->marca->nombre})";
        }

        if ($this->modelo) {
            $partes[] = "- {$this->modelo}";
        }

        return implode(' ', $partes);
    }

    // SCOPES
    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->where('nombre', 'ILIKE', "%{$search}%")
                ->orWhere('codigo', 'ILIKE', "%{$search}%")
                ->orWhere('codigo_barras', 'ILIKE', "%{$search}%")
                ->orWhere('modelo', 'ILIKE', "%{$search}%");
        });
    }

    #[Scope]
    protected function activos(Builder $query): void
    {
        $query->where('activo', true);
    }

    #[Scope]
    protected function porTipo(Builder $query, $tipo): void
    {
        $query->where('tipo', $tipo);
    }

    #[Scope]
    protected function porCategoria(Builder $query, $categoriaId): void
    {
        $query->where('categoria_id', $categoriaId);
    }

    #[Scope]
    protected function porMarca(Builder $query, $marcaId): void
    {
        $query->where('marca_id', $marcaId);
    }
}
