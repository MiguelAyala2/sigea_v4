<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

class ProductoProveedor extends Model
{
    protected $table = 'stock.PRODUCTO_PROVEEDOR';

    protected $fillable = [
        'producto_id',
        'proveedor_id',
        'codigo_proveedor',
        'precio_referencia',
        'tiempo_entrega_dias',
        'es_principal',
        'activo',
        'ultimo_precio_compra',
        'ultima_compra_fecha',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'precio_referencia' => 'decimal:2',
            'ultimo_precio_compra' => 'decimal:2',
            'tiempo_entrega_dias' => 'integer',
            'es_principal' => 'boolean',
            'activo' => 'boolean',
            'ultima_compra_fecha' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // RELACIONES
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Nota: Cuando se cree el módulo Empresa con Proveedores, descomentar esta relación
    // public function proveedor()
    // {
    //     return $this->belongsTo(\App\Models\Empresa\Proveedor::class, 'proveedor_id');
    // }

    // ACCESSORS
    public function getTiempoEntregaTextoAttribute(): string
    {
        if (!$this->tiempo_entrega_dias) {
            return 'No especificado';
        }

        if ($this->tiempo_entrega_dias == 1) {
            return '1 día';
        }

        return $this->tiempo_entrega_dias . ' días';
    }

    public function getPrecioReferenciaFormateadoAttribute(): string
    {
        if (!$this->precio_referencia) {
            return 'No especificado';
        }

        return 'Gs. ' . number_format($this->precio_referencia, 0, ',', '.');
    }

    // SCOPES
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePrincipales($query)
    {
        return $query->where('es_principal', true);
    }

    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopePorProveedor($query, $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
    }

    // MÉTODOS
    public function establecerComoPrincipal(): void
    {
        // Desactivar todos los proveedores principales de este producto
        static::where('producto_id', $this->producto_id)
              ->where('id', '!=', $this->id)
              ->update(['es_principal' => false]);

        // Activar este proveedor
        $this->es_principal = true;
        $this->save();
    }

    public function actualizarHistorialCompra(float $precioCompra): void
    {
        $this->ultimo_precio_compra = $precioCompra;
        $this->ultima_compra_fecha = now();
        $this->save();
    }
}
