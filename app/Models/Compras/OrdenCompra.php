<?php

namespace App\Models\Compras;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenCompra extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'compras.ordenes_compra';

    protected $fillable = [
        'numero_orden',
        'fecha_orden',
        'fecha_entrega_esperada',
        'proveedor_id',
        'sucursal_id',
        'deposito_id',
        'presupuesto_id',
        'pedido_compra_id',
        'condicion_pago',
        'tipo_orden',
        'subtotal',
        'iva_10',
        'iva_5',
        'exenta',
        'total_iva',
        'descuento_global',
        'flete',
        'total',
        'estado',
        'porcentaje_recibido',
        'direccion_entrega',
        'contacto_recepcion',
        'telefono_recepcion',
        'observaciones',
        'condiciones_especiales',
        'archivo_orden_path',
        'activo',
        'requiere_confirmacion',
        'confirmada_en',
        'enviada_en',
        'creadoPor',
        'actualizadoPor',
        'aprobadoPor',
        'confirmadaPor',
    ];

    protected $casts = [
        'fecha_orden' => 'date',
        'fecha_entrega_esperada' => 'date',
        'subtotal' => 'decimal:2',
        'iva_10' => 'decimal:2',
        'iva_5' => 'decimal:2',
        'exenta' => 'decimal:2',
        'total_iva' => 'decimal:2',
        'descuento_global' => 'decimal:2',
        'flete' => 'decimal:2',
        'total' => 'decimal:2',
        'porcentaje_recibido' => 'decimal:2',
        'activo' => 'boolean',
        'requiere_confirmacion' => 'boolean',
        'confirmada_en' => 'datetime',
        'enviada_en' => 'datetime',
    ];

    // Relaciones
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class, 'presupuesto_id');
    }

    public function pedidoCompra(): BelongsTo
    {
        return $this->belongsTo(PedidoCompra::class, 'pedido_compra_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(OrdenCompraDetalle::class, 'orden_compra_id');
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'orden_compra_id');
    }

    // Relación comentada hasta implementar el módulo de recepciones
    // public function recepciones(): HasMany
    // {
    //     return $this->hasMany(ComprasRecepcion::class, 'compra_id');
    // }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    public function aprobadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobadoPor');
    }

    public function confirmadaPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmadaPor');
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

    public function scopeEmitidas($query)
    {
        return $query->where('estado', 'APROBADO');
    }

    public function scopePendientesRecepcion($query)
    {
        return $query->where('estado', 'APROBADO')
                     ->where('porcentaje_recibido', '<', 100);
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
        $this->total = $this->subtotal + $this->total_iva + $this->flete - $this->descuento_global;

        $this->save();
    }

    public function actualizarPorcentajeRecibido()
    {
        $totalOrdenado = $this->detalles()->sum('cantidad_ordenada');
        $totalRecibido = $this->detalles()->sum('cantidad_recibida');

        if ($totalOrdenado > 0) {
            $this->porcentaje_recibido = ($totalRecibido / $totalOrdenado) * 100;

            // Con el nuevo sistema simplificado, todas las órdenes aprobadas se mantienen como APROBADO
            // independientemente del porcentaje de recepción
            if ($this->estado !== 'APROBADO') {
                $this->estado = 'APROBADO';
            }

            $this->save();
        }
    }

    public function emitir()
    {
        $this->estado = 'APROBADO';
        $this->save();
    }

    public function enviar()
    {
        $this->estado = 'APROBADO';
        $this->enviada_en = now();
        $this->save();
    }

    public function confirmar($usuarioId)
    {
        $this->estado = 'APROBADO';
        $this->confirmadaPor = $usuarioId;
        $this->confirmada_en = now();
        $this->save();
    }

    public function cancelar()
    {
        $this->estado = 'RECHAZADO';
        $this->save();

        // Cancelar todos los detalles
        $this->detalles()->update(['estado' => 'RECHAZADO']);
    }
}
