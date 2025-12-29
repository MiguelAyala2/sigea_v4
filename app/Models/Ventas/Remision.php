<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Remision extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'ventas.REMISIONES';

    protected $fillable = [
        'numero_remision',
        'fecha_emision',
        'fecha_entrega',
        'cliente_id',
        'factura_id',
        'pedido_cliente_id',
        'sucursal_id',
        'deposito_id',
        'responsable_id',
        'observaciones',
        'direccion_entrega',
        'receptor_nombre',
        'receptor_ci',
        'fecha_recepcion',
        'estado',
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
        'fecha_entrega' => 'date',
        'fecha_recepcion' => 'datetime',
        'emitido_en' => 'datetime',
        'anulado_en' => 'datetime',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Servicios\Cliente::class, 'cliente_id');
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    public function pedidoCliente()
    {
        return $this->belongsTo(PedidoCliente::class, 'pedido_cliente_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(\App\Models\Empresa\Sucursal::class, 'sucursal_id');
    }

    public function deposito()
    {
        return $this->belongsTo(\App\Models\Empresa\Deposito::class, 'deposito_id');
    }

    public function responsable()
    {
        return $this->belongsTo(\App\Models\User::class, 'responsable_id');
    }

    public function detalles()
    {
        return $this->hasMany(RemisionDetalle::class, 'remision_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
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
    public function generarNumeroRemision()
    {
        $year = date('Y');
        $ultimaRemision = static::where('numero_remision', 'like', "REM-{$year}-%")
            ->orderBy('numero_remision', 'desc')
            ->first();

        if ($ultimaRemision) {
            $lastNumber = (int) substr($ultimaRemision->numero_remision, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'REM-' . $year . '-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function emitir()
    {
        if ($this->estado !== 'BORRADOR') {
            throw new \Exception('Solo se pueden emitir remisiones en estado BORRADOR');
        }

        $this->estado = 'EMITIDA';
        $this->emitido_por = auth()->id();
        $this->emitido_en = now();
        $this->save();

        return $this;
    }

    public function marcarEnTransito()
    {
        if ($this->estado !== 'EMITIDA') {
            throw new \Exception('Solo se pueden marcar en tránsito remisiones EMITIDAS');
        }

        $this->estado = 'EN_TRANSITO';
        $this->save();

        return $this;
    }

    public function marcarEntregada($receptorNombre, $receptorCi)
    {
        if ($this->estado !== 'EN_TRANSITO' && $this->estado !== 'EMITIDA') {
            throw new \Exception('Solo se pueden marcar como entregadas remisiones en tránsito o emitidas');
        }

        $this->estado = 'ENTREGADA';
        $this->receptor_nombre = $receptorNombre;
        $this->receptor_ci = $receptorCi;
        $this->fecha_recepcion = now();
        $this->save();

        return $this;
    }

    public function anular($motivo)
    {
        if ($this->estado === 'ANULADA') {
            throw new \Exception('La remisión ya está anulada');
        }

        $this->estado = 'ANULADA';
        $this->motivo_anulacion = $motivo;
        $this->anulado_por = auth()->id();
        $this->anulado_en = now();
        $this->save();

        return $this;
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activo', true)->whereNull('deleted_at');
    }

    public function scopeEmitidas($query)
    {
        return $query->whereIn('estado', ['EMITIDA', 'EN_TRANSITO', 'ENTREGADA']);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['EMITIDA', 'EN_TRANSITO']);
    }
}
