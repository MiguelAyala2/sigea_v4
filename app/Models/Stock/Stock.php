<?php

namespace App\Models\Stock;

use App\Models\Empresa\Deposito;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use SoftDeletes;

    protected $table = 'stock.STOCK';

    protected $fillable = [
        'producto_id',
        'deposito_id',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
        'ubicacion',
        'pasillo',
        'estante',
        'lote',
        'fecha_vencimiento',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'stock_actual' => 'decimal:2',
            'stock_minimo' => 'decimal:2',
            'stock_maximo' => 'decimal:2',
            'fecha_vencimiento' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
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

    public function movimientos()
    {
        return $this->hasMany(MovimientoStock::class, 'producto_id', 'producto_id')
                    ->where('deposito_id', $this->deposito_id);
    }

    // ACCESSORS
    public function getEstadoAttribute(): string
    {
        if ($this->stock_actual <= 0) {
            return 'agotado';
        }

        if ($this->stock_actual <= $this->stock_minimo) {
            return 'bajo';
        }

        if ($this->stock_maximo && $this->stock_actual >= $this->stock_maximo) {
            return 'alto';
        }

        return 'normal';
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'agotado' => 'danger',
            'bajo' => 'warning',
            'alto' => 'info',
            'normal' => 'success',
        };
    }

    // SCOPES
    public function scopeBuscador($query, $search)
    {
        if (!empty($search)) {
            return $query->whereHas('producto', function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('codigo', 'ILIKE', "%{$search}%");
            })->orWhereHas('deposito', function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeStockBajo($query)
    {
        return $query->whereRaw('stock_actual <= stock_minimo');
    }

    public function scopeStockAgotado($query)
    {
        return $query->where('stock_actual', '<=', 0);
    }

    public function scopePorDeposito($query, $depositoId)
    {
        return $query->where('deposito_id', $depositoId);
    }
}
