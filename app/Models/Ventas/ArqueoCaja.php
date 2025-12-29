<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use OwenIt\Auditing\Contracts\Auditable;

class ArqueoCaja extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'ventas.ARQUEOS_CAJA';

    protected $fillable = [
        'apertura_caja_id',
        'usuario_id',
        'fecha_arqueo',
        'hora_arqueo',
        'moneda',
        'cantidad_billetes_100000',
        'cantidad_billetes_50000',
        'cantidad_billetes_20000',
        'cantidad_billetes_10000',
        'cantidad_billetes_5000',
        'cantidad_billetes_2000',
        'cantidad_monedas_1000',
        'cantidad_monedas_500',
        'cantidad_monedas_100',
        'cantidad_monedas_50',
        'subtotal_billetes',
        'subtotal_monedas',
        'total_arqueo',
        'cheques_recibidos',
        'cantidad_cheques',
        'observaciones',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_arqueo' => 'date',
        'cantidad_billetes_100000' => 'integer',
        'cantidad_billetes_50000' => 'integer',
        'cantidad_billetes_20000' => 'integer',
        'cantidad_billetes_10000' => 'integer',
        'cantidad_billetes_5000' => 'integer',
        'cantidad_billetes_2000' => 'integer',
        'cantidad_monedas_1000' => 'integer',
        'cantidad_monedas_500' => 'integer',
        'cantidad_monedas_100' => 'integer',
        'cantidad_monedas_50' => 'integer',
        'subtotal_billetes' => 'decimal:2',
        'subtotal_monedas' => 'decimal:2',
        'total_arqueo' => 'decimal:2',
        'cheques_recibidos' => 'decimal:2',
        'cantidad_cheques' => 'integer',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function aperturaCaja(): BelongsTo
    {
        return $this->belongsTo(AperturaCaja::class, 'apertura_caja_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    // Accessors
    public function getTotalGeneralAttribute(): float
    {
        return $this->total_arqueo + $this->cheques_recibidos;
    }

    // Scopes
    public function scopeBuscarApertura($query, $aperturaId)
    {
        if ($aperturaId) {
            return $query->where('apertura_caja_id', $aperturaId);
        }
        return $query;
    }

    public function scopeBuscarFecha($query, $fecha)
    {
        if ($fecha) {
            return $query->whereDate('fecha_arqueo', $fecha);
        }
        return $query;
    }

    public function scopeSoloActivos($query)
    {
        return $query->where('activo', true);
    }

    // Métodos de negocio
    public function calcularSubtotalBilletes(): float
    {
        return ($this->cantidad_billetes_100000 * 100000) +
               ($this->cantidad_billetes_50000 * 50000) +
               ($this->cantidad_billetes_20000 * 20000) +
               ($this->cantidad_billetes_10000 * 10000) +
               ($this->cantidad_billetes_5000 * 5000) +
               ($this->cantidad_billetes_2000 * 2000);
    }

    public function calcularSubtotalMonedas(): float
    {
        return ($this->cantidad_monedas_1000 * 1000) +
               ($this->cantidad_monedas_500 * 500) +
               ($this->cantidad_monedas_100 * 100) +
               ($this->cantidad_monedas_50 * 50);
    }

    public function calcularTotal(): float
    {
        $this->subtotal_billetes = $this->calcularSubtotalBilletes();
        $this->subtotal_monedas = $this->calcularSubtotalMonedas();
        $this->total_arqueo = $this->subtotal_billetes + $this->subtotal_monedas;

        return $this->total_arqueo;
    }

    public function getDetalleArqueo(): array
    {
        $detalle = [];

        if ($this->cantidad_billetes_100000 > 0) {
            $detalle[] = [
                'denominacion' => '100.000',
                'tipo' => 'Billete',
                'cantidad' => $this->cantidad_billetes_100000,
                'total' => $this->cantidad_billetes_100000 * 100000,
            ];
        }

        if ($this->cantidad_billetes_50000 > 0) {
            $detalle[] = [
                'denominacion' => '50.000',
                'tipo' => 'Billete',
                'cantidad' => $this->cantidad_billetes_50000,
                'total' => $this->cantidad_billetes_50000 * 50000,
            ];
        }

        if ($this->cantidad_billetes_20000 > 0) {
            $detalle[] = [
                'denominacion' => '20.000',
                'tipo' => 'Billete',
                'cantidad' => $this->cantidad_billetes_20000,
                'total' => $this->cantidad_billetes_20000 * 20000,
            ];
        }

        if ($this->cantidad_billetes_10000 > 0) {
            $detalle[] = [
                'denominacion' => '10.000',
                'tipo' => 'Billete',
                'cantidad' => $this->cantidad_billetes_10000,
                'total' => $this->cantidad_billetes_10000 * 10000,
            ];
        }

        if ($this->cantidad_billetes_5000 > 0) {
            $detalle[] = [
                'denominacion' => '5.000',
                'tipo' => 'Billete',
                'cantidad' => $this->cantidad_billetes_5000,
                'total' => $this->cantidad_billetes_5000 * 5000,
            ];
        }

        if ($this->cantidad_billetes_2000 > 0) {
            $detalle[] = [
                'denominacion' => '2.000',
                'tipo' => 'Billete',
                'cantidad' => $this->cantidad_billetes_2000,
                'total' => $this->cantidad_billetes_2000 * 2000,
            ];
        }

        if ($this->cantidad_monedas_1000 > 0) {
            $detalle[] = [
                'denominacion' => '1.000',
                'tipo' => 'Moneda',
                'cantidad' => $this->cantidad_monedas_1000,
                'total' => $this->cantidad_monedas_1000 * 1000,
            ];
        }

        if ($this->cantidad_monedas_500 > 0) {
            $detalle[] = [
                'denominacion' => '500',
                'tipo' => 'Moneda',
                'cantidad' => $this->cantidad_monedas_500,
                'total' => $this->cantidad_monedas_500 * 500,
            ];
        }

        if ($this->cantidad_monedas_100 > 0) {
            $detalle[] = [
                'denominacion' => '100',
                'tipo' => 'Moneda',
                'cantidad' => $this->cantidad_monedas_100,
                'total' => $this->cantidad_monedas_100 * 100,
            ];
        }

        if ($this->cantidad_monedas_50 > 0) {
            $detalle[] = [
                'denominacion' => '50',
                'tipo' => 'Moneda',
                'cantidad' => $this->cantidad_monedas_50,
                'total' => $this->cantidad_monedas_50 * 50,
            ];
        }

        return $detalle;
    }
}
