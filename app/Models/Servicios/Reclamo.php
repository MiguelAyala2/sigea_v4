<?php

namespace App\Models\Servicios;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reclamo extends Model
{
    protected $table = 'servicios.RECLAMOS';

    protected $fillable = [
        'codigo',
        'fecha_reclamo',
        'cliente_id',
        'orden_servicio_id',
        'tipo_reclamo',
        'prioridad',
        'estado',
        'canal_recepcion',
        'descripcion',
        'responsable_id',
        'solucion',
        'fecha_resolucion',
        'fecha_cierre',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_reclamo' => 'date',
        'fecha_resolucion' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    // Generar código automático
    public static function generarCodigo()
    {
        $ultimoReclamo = self::orderBy('id', 'desc')->first();
        $numero = $ultimoReclamo ? intval(substr($ultimoReclamo->codigo, 4)) + 1 : 1;
        return 'RCL-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function ordenServicio(): BelongsTo
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_servicio_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
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
            'en_revision' => '<span class="badge badge-info">En Revisión</span>',
            'en_proceso' => '<span class="badge badge-warning">En Proceso</span>',
            'resuelto' => '<span class="badge badge-success">Resuelto</span>',
            'cerrado' => '<span class="badge badge-primary">Cerrado</span>',
            'rechazado' => '<span class="badge badge-danger">Rechazado</span>',
            default => '<span class="badge badge-secondary">Desconocido</span>',
        };
    }

    public function getPrioridadBadgeAttribute()
    {
        return match($this->prioridad) {
            'baja' => '<span class="badge badge-secondary">Baja</span>',
            'media' => '<span class="badge badge-info">Media</span>',
            'alta' => '<span class="badge badge-warning">Alta</span>',
            'urgente' => '<span class="badge badge-danger">Urgente</span>',
            default => '<span class="badge badge-secondary">Desconocida</span>',
        };
    }

    public function getTipoReclamoTextAttribute()
    {
        return match($this->tipo_reclamo) {
            'calidad_servicio' => 'Calidad del Servicio',
            'demora_entrega' => 'Demora en Entrega',
            'falla_post_servicio' => 'Falla Post-Servicio',
            'atencion_cliente' => 'Atención al Cliente',
            'costo_facturacion' => 'Costo/Facturación',
            'otro' => 'Otro',
            default => 'Desconocido',
        };
    }

    public function getCanalRecepcionTextAttribute()
    {
        return match($this->canal_recepcion) {
            'presencial' => 'Presencial',
            'telefono' => 'Teléfono',
            'email' => 'Email',
            'whatsapp' => 'WhatsApp',
            'web' => 'Web',
            default => 'Desconocido',
        };
    }

    public function getClienteNombreAttribute()
    {
        return $this->cliente?->nombre ?? 'N/A';
    }

    public function getResponsableNombreAttribute()
    {
        return $this->responsable?->name ?? 'Sin asignar';
    }
}
