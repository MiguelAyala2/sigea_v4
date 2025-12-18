<?php

namespace App\Models\Compras;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotaDebitoDetalle extends Model
{
    use HasFactory;

    protected $table = 'compras.notas_debito_detalle';

    protected $fillable = [
        'nota_debito_id',
        'producto_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'impuesto',
        'total',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function notaDebito()
    {
        return $this->belongsTo(NotaDebito::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // Métodos auxiliares
    public function calcularTotales()
    {
        $this->subtotal = ($this->cantidad * $this->precio_unitario) - $this->descuento;
        $this->impuesto = $this->subtotal * 0.19; // IVA 19%
        $this->total = $this->subtotal + $this->impuesto;
        $this->save();
    }
}
