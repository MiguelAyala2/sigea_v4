<?php

namespace App\Models\Ventas;

use App\Models\Empresa\Timbrado;
use App\Models\Empresa\PuntoExpedicion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class NotaDebito extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'ventas.NOTAS_DEBITO';

    protected $fillable = [
        'numero_nota',
        'factura_id',
        'cliente_id',
        'timbrado_id',
        'punto_expedicion_id',
        'numero_timbrado',
        'fecha_emision',
        'motivo',
        'subtotal',
        'iva_10',
        'iva_5',
        'exenta',
        'total_iva',
        'total',
        'es_electronica',
        'cdc',
        'qr_data',
        'xml_firmado',
        'estado_set',
        'fecha_envio_set',
        'fecha_respuesta_set',
        'mensaje_set',
        'estado',
        'observaciones',
        'motivo_anulacion',
        'creado_por',
        'actualizado_por',
        'emitido_por',
        'emitido_en',
        'anulado_por',
        'anulado_en',
        'activo',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'subtotal' => 'decimal:2',
        'iva_10' => 'decimal:2',
        'iva_5' => 'decimal:2',
        'exenta' => 'decimal:2',
        'total_iva' => 'decimal:2',
        'total' => 'decimal:2',
        'es_electronica' => 'boolean',
        'fecha_envio_set' => 'datetime',
        'fecha_respuesta_set' => 'datetime',
        'emitido_en' => 'datetime',
        'anulado_en' => 'datetime',
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

    public function timbrado()
    {
        return $this->belongsTo(Timbrado::class, 'timbrado_id');
    }

    public function puntoExpedicion()
    {
        return $this->belongsTo(PuntoExpedicion::class, 'punto_expedicion_id');
    }

    public function detalles()
    {
        return $this->hasMany(NotaDebitoDetalle::class, 'nota_debito_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'actualizado_por');
    }

    public function emitidoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'emitido_por');
    }

    public function anuladoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'anulado_por');
    }

    // Métodos de negocio

    /**
     * Calcula los totales de la nota de débito basándose en los detalles
     */
    public function calcularTotales()
    {
        $detalles = $this->detalles;

        $this->subtotal = $detalles->sum('subtotal');
        $this->iva_10 = $detalles->where('iva_porcentaje', 10)->sum('iva_monto');
        $this->iva_5 = $detalles->where('iva_porcentaje', 5)->sum('iva_monto');
        $this->exenta = $detalles->where('iva_porcentaje', 0)->sum('subtotal');
        $this->total_iva = $this->iva_10 + $this->iva_5;
        $this->total = $this->subtotal + $this->total_iva;

        $this->save();
    }

    /**
     * Emite la nota de débito
     */
    public function emitir($usuarioId = null)
    {
        DB::beginTransaction();

        try {
            // Validar que tenga al menos un detalle
            if ($this->detalles()->count() == 0) {
                throw new \Exception('La nota de débito debe tener al menos un detalle.');
            }

            // Validar que la factura esté emitida
            if ($this->factura->estado !== 'EMITIDA') {
                throw new \Exception('La factura debe estar emitida para aplicar una nota de débito.');
            }

            $this->estado = 'EMITIDA';
            $this->emitido_por = $usuarioId ?? auth()->user()->id;
            $this->emitido_en = now();
            $this->save();

            // Actualizar el estado de pago de la factura
            $this->factura->actualizarEstadoPago();

            DB::commit();
            return $this;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Anula la nota de débito
     */
    public function anular(string $motivo, $usuarioId = null)
    {
        DB::beginTransaction();

        try {
            $this->estado = 'ANULADA';
            $this->motivo_anulacion = $motivo;
            $this->anulado_por = $usuarioId ?? auth()->user()->id;
            $this->anulado_en = now();
            $this->save();

            // Actualizar estado de pago de la factura
            $this->factura->actualizarEstadoPago();

            DB::commit();
            return $this;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Scopes
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeFactura($query, $facturaId)
    {
        return $query->where('factura_id', $facturaId);
    }

    public function scopeFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_emision', [$fechaInicio, $fechaFin]);
        }

        return $query->whereDate('fecha_emision', $fechaInicio);
    }
}
