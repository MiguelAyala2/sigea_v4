<?php

namespace App\Models\Compras;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoCompra extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'compras.pedidos_compra';

    protected $fillable = [
        'numero_pedido',
        'fecha_pedido',
        'fecha_necesaria',
        'sucursal_id',
        'deposito_destino_id',
        'usuario_solicitante_id',
        'tipo_pedido',
        'prioridad',
        'estado',
        'total_estimado',
        'porcentaje_ordenado',
        'justificacion',
        'observaciones',
        'requiere_aprobacion',
        'urgente',
        'activo',
        'creadoPor',
        'actualizadoPor',
        'aprobadoPor',
        'aprobado_en',
    ];

    protected $casts = [
        'fecha_pedido' => 'date',
        'fecha_necesaria' => 'date',
        'total_estimado' => 'decimal:2',
        'porcentaje_ordenado' => 'decimal:2',
        'requiere_aprobacion' => 'boolean',
        'urgente' => 'boolean',
        'activo' => 'boolean',
        'aprobado_en' => 'datetime',
    ];

    // Relaciones
    public function detalles(): HasMany
    {
        return $this->hasMany(PedidoCompraDetalle::class, 'pedido_compra_id');
    }

    public function usuarioSolicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_solicitante_id');
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

    public function presupuestos(): HasMany
    {
        return $this->hasMany(Presupuesto::class, 'pedido_compra_id');
    }

    public function ordenesCompra(): HasMany
    {
        return $this->hasMany(OrdenCompra::class, 'pedido_compra_id');
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

    public function scopeUrgentes($query)
    {
        return $query->where('urgente', true);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'PENDIENTE');
    }

    // Métodos auxiliares
    public function calcularTotalEstimado()
    {
        $this->total_estimado = $this->detalles()->sum('subtotal_estimado');
        $this->save();
    }

    public function actualizarPorcentajeOrdenado()
    {
        $totalSolicitado = $this->detalles()->sum('cantidad_solicitada');
        $totalOrdenado = $this->detalles()->sum('cantidad_ordenada');

        if ($totalSolicitado > 0) {
            $this->porcentaje_ordenado = ($totalOrdenado / $totalSolicitado) * 100;
            $this->save();
        }
    }

    public function aprobar($usuarioId)
    {
        $this->estado = 'APROBADO';
        $this->aprobadoPor = $usuarioId;
        $this->aprobado_en = now();
        $this->save();

        // Aprobar todos los detalles
        $this->detalles()->update([
            'estado' => 'APROBADO',
            'cantidad_aprobada' => \DB::raw('cantidad_solicitada')
        ]);
    }

    public function rechazar()
    {
        $this->estado = 'RECHAZADO';
        $this->save();

        // Rechazar todos los detalles
        $this->detalles()->update(['estado' => 'RECHAZADO']);
    }
}
