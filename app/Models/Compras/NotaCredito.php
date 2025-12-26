<?php

namespace App\Models\Compras;

use App\Models\User;
use App\Models\Empresa\Empresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotaCredito extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compras.notas_credito';

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
        return $this->hasMany(NotaCreditoDetalle::class);
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
        \DB::beginTransaction();
        try {
            // Actualizar estado de la nota de crédito
            $this->update(['estado' => 'aplicada']);

            // Crear registro en cuentas_por_pagar con monto NEGATIVO
            CuentaPorPagar::create([
                'compra_id' => $this->compra_id,
                'proveedor_id' => $this->proveedor_id,
                'numero_documento' => $this->numero,
                'timbrado' => null, // Las notas de crédito no tienen timbrado
                'tipo' => 'NOTA_CREDITO',
                'fecha_emision' => $this->fecha,
                'fecha_vencimiento' => $this->fecha, // Mismo día que la emisión
                'condicion_pago' => 'CONTADO',
                'monto_total' => -abs($this->total), // NEGATIVO para reducir deuda
                'monto_pagado' => 0,
                'saldo_pendiente' => -abs($this->total), // NEGATIVO para reducir saldo
                'moneda' => 'PYG',
                'estado' => 'APLICADA',
                'observaciones' => "Nota de Crédito #{$this->numero} - {$this->motivo}",
                'creadoPor' => auth()->id(),
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
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
