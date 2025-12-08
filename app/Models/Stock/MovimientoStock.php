<?php

namespace App\Models\Stock;

use App\Models\Empresa\Deposito;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    protected $table = 'stock.MOVIMIENTOS_STOCK';

    protected $fillable = [
        'producto_id',
        'deposito_id',
        'usuario_id',
        'tipo',
        'cantidad',
        'stock_anterior',
        'stock_posterior',
        'costo_unitario',
        'costo_total',
        'documento_tipo',
        'documento_id',
        'motivo',
        'fecha_movimiento',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
            'stock_anterior' => 'decimal:2',
            'stock_posterior' => 'decimal:2',
            'costo_unitario' => 'decimal:2',
            'costo_total' => 'decimal:2',
            'fecha_movimiento' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // RELACIONES
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function deposito()
    {
        return $this->belongsTo(Deposito::class, 'deposito_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ACCESSORS
    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'ENTRADA_COMPRA' => 'Entrada por Compra',
            'SALIDA_VENTA' => 'Salida por Venta',
            'AJUSTE_POSITIVO' => 'Ajuste Positivo',
            'AJUSTE_NEGATIVO' => 'Ajuste Negativo',
            'TRANSFERENCIA_ORIGEN' => 'Transferencia (Salida)',
            'TRANSFERENCIA_DESTINO' => 'Transferencia (Entrada)',
            default => $this->tipo,
        };
    }

    public function getTipoBadgeAttribute(): string
    {
        return match($this->tipo) {
            'ENTRADA_COMPRA', 'AJUSTE_POSITIVO', 'TRANSFERENCIA_DESTINO' => 'success',
            'SALIDA_VENTA', 'AJUSTE_NEGATIVO', 'TRANSFERENCIA_ORIGEN' => 'danger',
            default => 'secondary',
        };
    }

    public function getEsEntradaAttribute(): bool
    {
        return in_array($this->tipo, ['ENTRADA_COMPRA', 'AJUSTE_POSITIVO', 'TRANSFERENCIA_DESTINO']);
    }

    public function getEsSalidaAttribute(): bool
    {
        return in_array($this->tipo, ['SALIDA_VENTA', 'AJUSTE_NEGATIVO', 'TRANSFERENCIA_ORIGEN']);
    }

    // SCOPES
    public function scopeBuscador($query, $search)
    {
        if (!empty($search)) {
            return $query->whereHas('producto', function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('codigo', 'ILIKE', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorDeposito($query, $depositoId)
    {
        return $query->where('deposito_id', $depositoId);
    }

    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopeEntradas($query)
    {
        return $query->whereIn('tipo', ['ENTRADA_COMPRA', 'AJUSTE_POSITIVO', 'TRANSFERENCIA_DESTINO']);
    }

    public function scopeSalidas($query)
    {
        return $query->whereIn('tipo', ['SALIDA_VENTA', 'AJUSTE_NEGATIVO', 'TRANSFERENCIA_ORIGEN']);
    }

    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_movimiento', [$desde, $hasta]);
    }
}
