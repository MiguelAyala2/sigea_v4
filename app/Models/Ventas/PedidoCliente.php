<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PedidoCliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas.pedidos_clientes';

    protected $fillable = [
        'numero_pedido',
        'fecha_pedido',
        'cliente_id',
        'sucursal_id',
        'deposito_id',
        'vendedor_id',
        'cotizacion_id',
        'tipo_entrega',
        'subtotal',
        'iva_10',
        'iva_5',
        'exenta',
        'total_iva',
        'descuento_global',
        'flete',
        'total',
        'estado',
        'porcentaje_entregado',
        'observaciones',
        'condiciones_especiales',
        'direccion_entrega',
        'creado_por',
        'actualizado_por',
        'confirmado_por',
        'confirmado_en',
        'facturado_por',
        'facturado_en',
    ];

    protected $casts = [
        'fecha_pedido' => 'date',
        'subtotal' => 'decimal:2',
        'iva_10' => 'decimal:2',
        'iva_5' => 'decimal:2',
        'exenta' => 'decimal:2',
        'total_iva' => 'decimal:2',
        'descuento_global' => 'decimal:2',
        'flete' => 'decimal:2',
        'total' => 'decimal:2',
        'porcentaje_entregado' => 'decimal:2',
        'confirmado_en' => 'datetime',
        'facturado_en' => 'datetime',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Servicios\Cliente::class, 'cliente_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(\App\Models\Sucursal::class, 'sucursal_id');
    }

    public function deposito()
    {
        return $this->belongsTo(\App\Models\Deposito::class, 'deposito_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(\App\Models\User::class, 'vendedor_id');
    }

    public function cotizacion()
    {
        return $this->belongsTo(\App\Models\Ventas\Cotizacion::class, 'cotizacion_id');
    }

    public function detalles()
    {
        return $this->hasMany(PedidoClienteDetalle::class, 'pedido_cliente_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'actualizado_por');
    }

    public function confirmadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'confirmado_por');
    }

    public function facturadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'facturado_por');
    }

    // Métodos de negocio

    /**
     * Calcula los totales del pedido basándose en los detalles
     * IMPORTANTE: Los precios incluyen IVA
     */
    public function calcularTotales()
    {
        $detalles = $this->detalles;

        // El subtotal ya incluye IVA
        $this->subtotal = $detalles->sum('subtotal');

        // IVA es solo informativo (ya está incluido en el subtotal)
        $this->iva_10 = $detalles->where('iva_porcentaje', 10)->sum('iva_monto');
        $this->iva_5 = $detalles->where('iva_porcentaje', 5)->sum('iva_monto');
        $this->exenta = $detalles->where('iva_porcentaje', 0)->sum('subtotal');
        $this->total_iva = $this->iva_10 + $this->iva_5;

        // Total = Subtotal (ya incluye IVA, no se suma nuevamente)
        $this->total = $this->subtotal;

        $this->save();
    }

    /**
     * Actualiza el porcentaje entregado del pedido
     */
    public function actualizarPorcentajeEntregado()
    {
        $detalles = $this->detalles;

        if ($detalles->count() == 0) {
            $this->porcentaje_entregado = 0;
            $this->save();
            return;
        }

        $totalSolicitado = $detalles->sum('cantidad_solicitada');
        $totalEntregado = $detalles->sum('cantidad_entregada');

        if ($totalSolicitado > 0) {
            $this->porcentaje_entregado = ($totalEntregado / $totalSolicitado) * 100;
        } else {
            $this->porcentaje_entregado = 0;
        }

        // Actualizar estado según porcentaje
        if ($this->porcentaje_entregado >= 100) {
            $this->estado = 'COMPLETAMENTE_ENTREGADO';
        } elseif ($this->porcentaje_entregado > 0) {
            $this->estado = 'PARCIALMENTE_ENTREGADO';
        }

        $this->save();
    }

    /**
     * Confirma el pedido
     */
    public function confirmar($usuarioId = null)
    {
        $this->estado = 'CONFIRMADO';
        $this->confirmado_por = $usuarioId ?? auth()->id();
        $this->confirmado_en = now();
        $this->save();

        return $this;
    }

    /**
     * Marca el pedido como en preparación
     */
    public function prepararPedido()
    {
        $this->estado = 'EN_PREPARACION';
        $this->save();

        return $this;
    }

    /**
     * Marca el pedido como listo para entregar
     */
    public function listoParaEntregar()
    {
        $this->estado = 'LISTO_ENTREGAR';
        $this->save();

        return $this;
    }

    /**
     * Marca el pedido como facturado
     */
    public function facturar($usuarioId = null)
    {
        $this->estado = 'FACTURADO';
        $this->facturado_por = $usuarioId ?? auth()->id();
        $this->facturado_en = now();
        $this->save();

        return $this;
    }

    /**
     * Cancela el pedido
     */
    public function cancelar()
    {
        DB::beginTransaction();

        try {
            $this->estado = 'CANCELADO';
            $this->save();

            // Cancelar todos los detalles
            $this->detalles()->update(['estado' => 'CANCELADO']);

            DB::commit();
            return $this;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Anula el pedido
     */
    public function anular()
    {
        DB::beginTransaction();

        try {
            $this->estado = 'ANULADO';
            $this->save();

            // Cancelar todos los detalles
            $this->detalles()->update(['estado' => 'CANCELADO']);

            DB::commit();
            return $this;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Genera el siguiente número de pedido
     */
    public static function generarNumeroPedido()
    {
        $ultimo = self::orderBy('id', 'desc')->first();

        if ($ultimo) {
            $numero = (int) substr($ultimo->numero_pedido, 3) + 1;
        } else {
            $numero = 1;
        }

        return 'PC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para filtrar por cliente
     */
    public function scopeCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Scope para filtrar por fecha
     */
    public function scopeFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_pedido', [$fechaInicio, $fechaFin]);
        }

        return $query->whereDate('fecha_pedido', $fechaInicio);
    }
}
