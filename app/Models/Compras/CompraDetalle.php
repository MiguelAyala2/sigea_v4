<?php

namespace App\Models\Compras;

use App\Models\Stock\Producto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class CompraDetalle extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'compras.compras_detalle'; // Ajustar según el nombre real

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'descuento_porcentaje',
        'descuento_monto',
        'subtotal',
        'iva_porcentaje',
        'iva_monto',
        'total',
        'descripcion',
        'lote',
        'fecha_vencimiento',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:3',
            'precio_unitario' => 'decimal:2',
            'descuento_porcentaje' => 'decimal:2',
            'descuento_monto' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'iva_porcentaje' => 'decimal:2',
            'iva_monto' => 'decimal:2',
            'total' => 'decimal:2',
            'fecha_vencimiento' => 'date',
        ];
    }

    // ==================== RELACIONES ====================

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // ==================== ACCESSORS ====================

    public function getPrecioUnitarioFormateadoAttribute(): string
    {
        return number_format($this->precio_unitario, 0, ',', '.');
    }

    public function getTotalFormateadoAttribute(): string
    {
        return number_format($this->total, 0, ',', '.');
    }

    public function getIvaTextoAttribute(): string
    {
        return match((int) $this->iva_porcentaje) {
            10 => '10%',
            5 => '5%',
            0 => 'Exenta',
            default => "{$this->iva_porcentaje}%",
        };
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function porCompra(Builder $query, $compraId): void
    {
        $query->when($compraId, function (Builder $query, $compraId) {
            $query->where('compra_id', $compraId);
        });
    }

    #[Scope]
    protected function porProducto(Builder $query, $productoId): void
    {
        $query->when($productoId, function (Builder $query, $productoId) {
            $query->where('producto_id', $productoId);
        });
    }

    #[Scope]
    protected function conProducto(Builder $query): void
    {
        $query->whereNotNull('producto_id');
    }

    // ==================== MÉTODOS ====================

    /**
     * Calcula el subtotal antes de impuestos
     */
    public function calcularSubtotal(): float
    {
        $subtotal = $this->cantidad * $this->precio_unitario;
        
        if ($this->descuento_porcentaje > 0) {
            $descuento = $subtotal * ($this->descuento_porcentaje / 100);
            $subtotal -= $descuento;
        } elseif ($this->descuento_monto > 0) {
            $subtotal -= $this->descuento_monto;
        }
        
        return round($subtotal, 2);
    }

    /**
     * Calcula el IVA
     */
    public function calcularIva(): float
    {
        $subtotal = $this->calcularSubtotal();
        return round($subtotal * ($this->iva_porcentaje / 100), 2);
    }

    /**
     * Calcula el total
     */
    public function calcularTotal(): float
    {
        $subtotal = $this->calcularSubtotal();
        $iva = $this->calcularIva();
        return round($subtotal + $iva, 2);
    }
}
