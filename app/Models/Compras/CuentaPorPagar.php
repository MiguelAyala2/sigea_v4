<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class CuentaPorPagar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compras.cuentas_por_pagar';

    protected $fillable = [
        'compra_id',
        'proveedor_id',
        'numero_documento',
        'timbrado',
        'fecha_emision',
        'fecha_vencimiento',
        'condicion_pago',
        'monto_total',
        'monto_pagado',
        'saldo_pendiente',
        'estado',
        'observaciones',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'monto_total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
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

    public function creador()
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    // Métodos
    public function estaVencida()
    {
        return $this->fecha_vencimiento < now() && $this->saldo_pendiente > 0;
    }

    public function diasVencimiento()
    {
        if (!$this->estaVencida()) {
            return 0;
        }

        return now()->diffInDays($this->fecha_vencimiento);
    }

    public function marcarComoVencida()
    {
        if ($this->estaVencida() && $this->estado !== 'APROBADO') {
            $this->update(['estado' => 'RECHAZADO']);
        }
    }
}
