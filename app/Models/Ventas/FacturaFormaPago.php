<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaFormaPago extends Model
{
    use HasFactory;

    protected $table = 'ventas.FACTURAS_FORMAS_PAGO';

    protected $fillable = [
        'factura_id',
        'forma_pago',
        'monto',
        'referencia',
        'banco',
        'fecha_pago',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    // Relaciones
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    // Constantes
    const FORMAS_PAGO = [
        'EFECTIVO',
        'CHEQUE',
        'TARJETA_DEBITO',
        'TARJETA_CREDITO',
        'TRANSFERENCIA',
        'QR',
        'OTRO',
    ];

    // Accessors
    public function getFormaPagoTextoAttribute(): string
    {
        return match($this->forma_pago) {
            'EFECTIVO' => 'Efectivo',
            'CHEQUE' => 'Cheque',
            'TARJETA_DEBITO' => 'Tarjeta de Débito',
            'TARJETA_CREDITO' => 'Tarjeta de Crédito',
            'TRANSFERENCIA' => 'Transferencia Bancaria',
            'QR' => 'QR / Billetera Digital',
            'OTRO' => 'Otro',
            default => $this->forma_pago,
        };
    }
}
