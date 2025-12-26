<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Empresa\Deposito;

class AjusteStock extends Model
{
    use SoftDeletes;

    protected $table = 'stock.ajustes_stock';

    protected $fillable = [
        'producto_id',
        'deposito_id',
        'usuario_id',
        'tipo_ajuste',
        'motivo_ajuste',
        'observaciones',
        'cantidad',
        'stock_anterior',
        'stock_posterior',
        'fecha_ajuste',
        'movimiento_stock_id',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_ajuste' => 'datetime',
        'cantidad' => 'decimal:2',
        'stock_anterior' => 'decimal:2',
        'stock_posterior' => 'decimal:2',
    ];

    // Relaciones
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function deposito(): BelongsTo
    {
        return $this->belongsTo(Deposito::class, 'deposito_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function movimientoStock(): BelongsTo
    {
        return $this->belongsTo(MovimientoStock::class, 'movimiento_stock_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }
}
