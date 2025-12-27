<?php

namespace App\Models\Servicios;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenServicio extends Model
{
    protected $table = 'servicios.ORDENES_SERVICIO';

    protected $fillable = [
        'codigo',
        'fecha_orden',
        'presupuesto_id',
        'tecnico_id',
        'estado',
        'fecha_inicio',
        'fecha_finalizacion',
        'fecha_entrega',
        'progreso',
        'observaciones',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_orden' => 'date',
        'fecha_inicio' => 'datetime',
        'fecha_finalizacion' => 'datetime',
        'fecha_entrega' => 'datetime',
        'progreso' => 'integer',
        'activo' => 'boolean',
    ];

    // Generar código automático
    public static function generarCodigo()
    {
        $ultimaOrden = self::orderBy('id', 'desc')->first();
        $numero = $ultimaOrden ? intval(substr($ultimaOrden->codigo, 8)) + 1 : 1;
        return 'ORD-SRV-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    // Relaciones
    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class, 'presupuesto_id');
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    // Accesorios
    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'pendiente' => '<span class="badge badge-secondary">Pendiente</span>',
            'en_proceso' => '<span class="badge badge-info">En Proceso</span>',
            'pausada' => '<span class="badge badge-warning">Pausada</span>',
            'finalizada' => '<span class="badge badge-success">Finalizada</span>',
            'entregada' => '<span class="badge badge-primary">Entregada</span>',
            'cancelada' => '<span class="badge badge-danger">Cancelada</span>',
            default => '<span class="badge badge-secondary">Desconocido</span>',
        };
    }

    public function getClienteAttribute()
    {
        return $this->presupuesto?->diagnostico?->recepcion?->solicitud?->cliente?->nombre ?? 'N/A';
    }

    public function getEquipoAttribute()
    {
        return $this->presupuesto?->diagnostico?->recepcion?->producto?->nombre ?? 'N/A';
    }

    public function getTecnicoNombreAttribute()
    {
        return $this->tecnico?->name ?? 'Sin asignar';
    }
}
