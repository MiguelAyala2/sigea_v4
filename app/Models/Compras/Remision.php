<?php

namespace App\Models\Compras;

use App\Models\User;
use App\Models\Empresa\Empresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Remision extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compras.remisiones';

    protected $fillable = [
        'orden_compra_id',
        'proveedor_id',
        'empresa_id',
        'tipo',
        'cliente_nombre',
        'cliente_ruc',
        'cliente_direccion',
        'cliente_telefono',
        'cliente_email',
        'sucursal_destino_id',
        'deposito_destino_id',
        'numero',
        'fecha',
        'numero_guia_proveedor',
        'transportista',
        'placa_vehiculo',
        'direccion_entrega',
        'estado',
        'observaciones',
        'recibido_por',
        'fecha_recepcion',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_recepcion' => 'datetime',
    ];

    // Relaciones
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function detalles()
    {
        return $this->hasMany(RemisionDetalle::class);
    }

    public function recibidoPor()
    {
        return $this->belongsTo(User::class, 'recibido_por');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(\App\Models\Empresa\Sucursal::class, 'sucursal_destino_id');
    }

    public function depositoDestino()
    {
        return $this->belongsTo(\App\Models\Empresa\Deposito::class, 'deposito_destino_id');
    }

    // Métodos auxiliares
    public function marcarComoRecibida()
    {
        $this->update([
            'estado' => 'recibida',
            'fecha_recepcion' => now(),
            'recibido_por' => auth()->id(),
        ]);
    }

    public function anular()
    {
        $this->update(['estado' => 'anulada']);
    }

    public function verificarEstadoParcial()
    {
        $totalEnviado = $this->detalles->sum('cantidad_enviada');
        $totalRecibido = $this->detalles->sum('cantidad_recibida');

        if ($totalRecibido > 0 && $totalRecibido < $totalEnviado) {
            $this->update(['estado' => 'parcial']);
        } elseif ($totalRecibido >= $totalEnviado) {
            $this->marcarComoRecibida();
        }
    }
}
