<?php

namespace App\Models\Compras;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotaDebito extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compras.notas_debito';

    protected $fillable = [
        'compra_id',
        'proveedor_id',
        'empresa_id',
        'numero',
        'fecha',
        'numero_factura_afectada',
        'motivo',
        'subtotal',
        'impuesto',
        'total',
        'estado',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function detalles()
    {
        return $this->hasMany(NotaDebitoDetalle::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Métodos auxiliares
    public function aplicar()
    {
        $this->update(['estado' => 'aplicada']);
    }

    public function anular()
    {
        $this->update(['estado' => 'anulada']);
    }

    public function recalcularTotales()
    {
        $subtotal = $this->detalles->sum('subtotal');
        $impuesto = $this->detalles->sum('impuesto');
        $total = $this->detalles->sum('total');

        $this->update([
            'subtotal' => $subtotal,
            'impuesto' => $impuesto,
            'total' => $total,
        ]);
    }
}
