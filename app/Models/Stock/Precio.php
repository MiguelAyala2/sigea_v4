<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    protected $table = 'stock.PRECIOS';

    protected $fillable = [
        'producto_id',
        'precio_compra',
        'precio_venta',
        'margen_porcentaje',
        'iva',
        'moneda',
        'tipo_cambio',
        'es_actual',
        'creadoPor',
    ];

    protected function casts(): array
    {
        return [
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'margen_porcentaje' => 'decimal:2',
            'tipo_cambio' => 'decimal:2',
            'es_actual' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // RELACIONES
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // ACCESSORS
    public function getIvaTextoAttribute(): string
    {
        return match($this->iva) {
            '10' => '10%',
            '5' => '5%',
            'exenta' => 'Exenta',
            default => $this->iva,
        };
    }

    public function getMonedaSimboloAttribute(): string
    {
        return match($this->moneda) {
            'PYG' => 'Gs.',
            'USD' => 'US$',
            default => $this->moneda,
        };
    }

    public function getPrecioCompraFormateadoAttribute(): string
    {
        return $this->moneda_simbolo . ' ' . number_format($this->precio_compra, 0, ',', '.');
    }

    public function getPrecioVentaFormateadoAttribute(): string
    {
        return $this->moneda_simbolo . ' ' . number_format($this->precio_venta, 0, ',', '.');
    }

    // SCOPES
    public function scopeActual($query)
    {
        return $query->where('es_actual', true);
    }

    public function scopePorMoneda($query, $moneda)
    {
        return $query->where('moneda', $moneda);
    }

    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    // MÉTODOS
    public function calcularMargen(): void
    {
        if ($this->precio_compra > 0) {
            $this->margen_porcentaje = (($this->precio_venta - $this->precio_compra) / $this->precio_compra) * 100;
        } else {
            $this->margen_porcentaje = 0;
        }
    }

    public function establecerComoActual(): void
    {
        // Desactivar todos los precios actuales de este producto
        static::where('producto_id', $this->producto_id)
              ->where('id', '!=', $this->id)
              ->update(['es_actual' => false]);

        // Activar este precio
        $this->es_actual = true;
        $this->save();
    }

    // BOOT
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($precio) {
            // Calcular margen automáticamente antes de guardar
            $precio->calcularMargen();
        });
    }
}
