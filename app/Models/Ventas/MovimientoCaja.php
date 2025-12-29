<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use OwenIt\Auditing\Contracts\Auditable;

class MovimientoCaja extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'ventas.MOVIMIENTOS_CAJA';

    protected $fillable = [
        'apertura_caja_id',
        'usuario_responsable_id',
        'tipo_movimiento',
        'concepto',
        'monto',
        'forma_pago',
        'comprobante_numero',
        'referencia',
        'fecha_movimiento',
        'hora_movimiento',
        'venta_id',
        'compra_id',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_movimiento' => 'date',
        'monto' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Constantes
    const TIPOS_MOVIMIENTO = [
        'INGRESO' => 'Ingreso',
        'EGRESO' => 'Egreso',
        'DEPOSITO' => 'Depósito',
        'RETIRO' => 'Retiro',
    ];

    const FORMAS_PAGO = [
        'EFECTIVO' => 'Efectivo',
        'CHEQUE' => 'Cheque',
        'TARJETA_DEBITO' => 'Tarjeta Débito',
        'TARJETA_CREDITO' => 'Tarjeta Crédito',
        'TRANSFERENCIA' => 'Transferencia',
        'QR' => 'QR',
        'OTRO' => 'Otro',
    ];

    // Relaciones
    public function aperturaCaja(): BelongsTo
    {
        return $this->belongsTo(AperturaCaja::class, 'apertura_caja_id');
    }

    public function usuarioResponsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_responsable_id');
    }

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'venta_id');
    }

    // Accessors
    public function getTipoMovimientoTextoAttribute(): string
    {
        return self::TIPOS_MOVIMIENTO[$this->tipo_movimiento] ?? $this->tipo_movimiento;
    }

    public function getFormaPagoTextoAttribute(): string
    {
        return self::FORMAS_PAGO[$this->forma_pago] ?? $this->forma_pago;
    }

    public function getEsIngresoAttribute(): bool
    {
        return $this->tipo_movimiento === 'INGRESO';
    }

    public function getEsEgresoAttribute(): bool
    {
        return $this->tipo_movimiento === 'EGRESO';
    }

    public function getMontoFormateadoAttribute(): string
    {
        $signo = $this->es_ingreso ? '+' : '-';
        return "{$signo} ₲ " . number_format($this->monto, 0, ',', '.');
    }

    // Scopes
    public function scopeBuscador($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('concepto', 'ILIKE', "%{$search}%")
                  ->orWhere('comprobante_numero', 'ILIKE', "%{$search}%")
                  ->orWhere('referencia', 'ILIKE', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeBuscarApertura($query, $aperturaId)
    {
        if ($aperturaId) {
            return $query->where('apertura_caja_id', $aperturaId);
        }
        return $query;
    }

    public function scopeBuscarTipoMovimiento($query, $tipo)
    {
        if ($tipo) {
            return $query->where('tipo_movimiento', $tipo);
        }
        return $query;
    }

    public function scopeBuscarFormaPago($query, $formaPago)
    {
        if ($formaPago) {
            return $query->where('forma_pago', $formaPago);
        }
        return $query;
    }

    public function scopeBuscarFecha($query, $fecha)
    {
        if ($fecha) {
            return $query->whereDate('fecha_movimiento', $fecha);
        }
        return $query;
    }

    public function scopeBuscarRangoFechas($query, $fechaDesde, $fechaHasta)
    {
        if ($fechaDesde && $fechaHasta) {
            return $query->whereBetween('fecha_movimiento', [$fechaDesde, $fechaHasta]);
        }
        return $query;
    }

    public function scopeSoloActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeSoloIngresos($query)
    {
        return $query->where('tipo_movimiento', 'INGRESO');
    }

    public function scopeSoloEgresos($query)
    {
        return $query->where('tipo_movimiento', 'EGRESO');
    }

    public function scopePorFormaPago($query, $formaPago)
    {
        return $query->where('forma_pago', $formaPago);
    }
}
