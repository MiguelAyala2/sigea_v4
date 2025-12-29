<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaPorCobrar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas.CUENTAS_POR_COBRAR';

    protected $fillable = [
        'factura_id',
        'numero_factura',
        'cliente_id',
        'fecha_emision',
        'fecha_vencimiento',
        'monto_total',
        'monto_pagado',
        'saldo_pendiente',
        'estado',
        'observaciones',
        'creado_por',
        'actualizado_por',
        'activo',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'monto_total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Servicios\Cliente::class, 'cliente_id');
    }

    // Métodos de negocio
    public function registrarPago($monto)
    {
        $this->monto_pagado += $monto;
        $this->saldo_pendiente = $this->monto_total - $this->monto_pagado;

        // Actualizar estado
        if ($this->saldo_pendiente <= 0) {
            $this->estado = 'PAGADA';
        } elseif ($this->monto_pagado > 0) {
            $this->estado = 'PARCIALMENTE_PAGADA';
        }

        $this->save();
    }

    public function verificarVencimiento()
    {
        if ($this->fecha_vencimiento < now() && $this->saldo_pendiente > 0) {
            $this->estado = 'VENCIDA';
            $this->save();
        }
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['PENDIENTE', 'PARCIALMENTE_PAGADA']);
    }

    public function scopeVencidas($query)
    {
        return $query->where('estado', 'VENCIDA');
    }
}
