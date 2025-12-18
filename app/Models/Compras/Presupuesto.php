<?php

namespace App\Models\Compras;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presupuesto extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'compras.presupuestos';

    protected $fillable = [
        'pedido_compra_id',
        'proveedor_id',
        'numero_presupuesto',
        'fecha_solicitud',
        'fecha_recepcion',
        'fecha_vencimiento',
        'condicion_pago',
        'dias_entrega',
        'descuento_general',
        'flete',
        'subtotal',
        'iva_10',
        'iva_5',
        'exenta',
        'total_iva',
        'total',
        'estado',
        'puntuacion',
        'observaciones_evaluacion',
        'observaciones',
        'archivo_presupuesto_path',
        'activo',
        'es_mejor_precio',
        'es_mejor_plazo',
        'solicitadoPor',
        'evaluadoPor',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_recepcion' => 'date',
        'fecha_vencimiento' => 'date',
        'dias_entrega' => 'integer',
        'descuento_general' => 'decimal:2',
        'flete' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva_10' => 'decimal:2',
        'iva_5' => 'decimal:2',
        'exenta' => 'decimal:2',
        'total_iva' => 'decimal:2',
        'total' => 'decimal:2',
        'puntuacion' => 'integer',
        'activo' => 'boolean',
        'es_mejor_precio' => 'boolean',
        'es_mejor_plazo' => 'boolean',
    ];

    // Relaciones
    public function pedidoCompra(): BelongsTo
    {
        return $this->belongsTo(PedidoCompra::class, 'pedido_compra_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(PresupuestoDetalle::class, 'presupuesto_id');
    }

    public function ordenesCompra(): HasMany
    {
        return $this->hasMany(OrdenCompra::class, 'presupuesto_id');
    }

    public function solicitadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitadoPor');
    }

    public function evaluadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluadoPor');
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeVigentes($query)
    {
        return $query->where('fecha_vencimiento', '>=', now()->toDateString())
                     ->whereIn('estado', ['RECIBIDO', 'EN_EVALUACION']);
    }

    // Métodos auxiliares
    public function calcularTotales()
    {
        $this->subtotal = $this->detalles()->sum('subtotal');

        $iva10 = $this->detalles()
            ->where('iva_porcentaje', 10)
            ->sum('iva_monto');

        $iva5 = $this->detalles()
            ->where('iva_porcentaje', 5)
            ->sum('iva_monto');

        $exenta = $this->detalles()
            ->where('iva_porcentaje', 0)
            ->sum('subtotal');

        $this->iva_10 = $iva10;
        $this->iva_5 = $iva5;
        $this->exenta = $exenta;
        $this->total_iva = $iva10 + $iva5;
        $this->total = $this->subtotal + $this->total_iva + $this->flete - $this->descuento_general;

        $this->save();
    }

    public function marcarComoSeleccionado()
    {
        $this->estado = 'APROBADO';
        $this->save();
    }

    public function verificarVencimiento()
    {
        if ($this->fecha_vencimiento && $this->fecha_vencimiento < now()->toDateString()) {
            $this->estado = 'RECHAZADO';
            $this->save();
        }
    }
}
