<?php

namespace App\Models\Compras;

use App\Models\User;
use App\Models\Empresa\Deposito;
use App\Models\Empresa\Sucursal;
use App\Models\Empresa\Timbrado;
use App\Models\Compras\Proveedor;
use App\Models\Stock\Producto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Compra extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'compras.compras'; // Ajustar según el nombre real de tu tabla

    protected $fillable = [
        'proveedor_id',
        'sucursal_id',
        'deposito_id',
        'timbrado_id',
        'orden_compra_id',
        'numero_factura',
        'timbrado',
        'fecha_emision',
        'fecha_vencimiento',
        'condicion_pago',
        'tipo_factura',
        'tipo_documento',
        'subtotal',
        'iva_10',
        'iva_5',
        'exenta',
        'total_iva',
        'total',
        'estado',
        'observaciones',
        'es_electronica',
        'cdc',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'fecha_vencimiento' => 'date',
            'subtotal' => 'decimal:2',
            'iva_10' => 'decimal:2',
            'iva_5' => 'decimal:2',
            'exenta' => 'decimal:2',
            'total' => 'decimal:2',
            'es_electronica' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    // ==================== CONSTANTES ====================

    public const ESTADOS = [
        'PENDIENTE' => 'Pendiente de Aprobación',
        'APROBADO' => 'Aprobado',
        'RECHAZADO' => 'Rechazado',
    ];

    public const TIPOS_FACTURA = [
        'CONTADO' => 'Contado',
        'CREDITO' => 'Crédito',
    ];

    public const CONDICIONES_PAGO = [
        'CONTADO' => 'Contado',
        '7_DIAS' => '7 Días',
        '15_DIAS' => '15 Días',
        '30_DIAS' => '30 Días',
        '60_DIAS' => '60 Días',
        '90_DIAS' => '90 Días',
    ];

    // ==================== RELACIONES ====================

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function deposito(): BelongsTo
    {
        return $this->belongsTo(Deposito::class, 'deposito_id');
    }

    public function timbrado(): BelongsTo
    {
        return $this->belongsTo(Timbrado::class, 'timbrado_id');
    }

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CompraDetalle::class, 'compra_id');
    }

    public function recepciones(): HasMany
    {
        return $this->hasMany(CompraRecepcion::class, 'compra_id');
    }

    public function aprobaciones(): HasMany
    {
        return $this->hasMany(AprobacionFlujo::class, 'documento_id')
            ->where('documento_tipo', 'COMPRA');
    }

    // public function integracionesOrigen(): HasMany
    // {
    //     return $this->hasMany(IntegracionDocumentos::class, 'documento_origen_id')
    //         ->where('documento_origen_tipo', 'COMPRA');
    // }

    // public function integracionesDestino(): HasMany
    // {
    //     return $this->hasMany(IntegracionDocumentos::class, 'documento_destino_id')
    //         ->where('documento_destino_tipo', 'COMPRA');
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

    public function cuentaPorPagar(): BelongsTo
    {
        return $this->belongsTo(CuentaPorPagar::class, 'id', 'compra_id');
    }

    // ==================== ACCESSORS ====================

    public function getEstadoTextoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getTipoFacturaTextoAttribute(): string
    {
        return self::TIPOS_FACTURA[$this->tipo_factura] ?? $this->tipo_factura;
    }

    public function getCondicionPagoTextoAttribute(): string
    {
        return self::CONDICIONES_PAGO[$this->condicion_pago] ?? $this->condicion_pago;
    }

    public function getNumeroCompletoAttribute(): string
    {
        return $this->timbrado ? 
            "{$this->timbrado->numero_timbrado}-{$this->numero_factura}" : 
            $this->numero_factura;
    }

    public function getRecepcionCompletaAttribute(): bool
    {
        if ($this->recepciones->isEmpty()) {
            return false;
        }

        return $this->recepciones->every(function ($recepcion) {
            return $recepcion->estado === 'COMPLETA';
        });
    }

    public function getPorcentajeRecibidoAttribute(): float
    {
        if ($this->recepciones->isEmpty()) {
            return 0.0;
        }

        return $this->recepciones->avg('porcentaje_recibido');
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->where('numero_factura', 'like', "%{$search}%")
                ->orWhereHas('proveedor', function ($q) use ($search) {
                    $q->where('razon_social', 'like', "%{$search}%")
                      ->orWhere('ruc', 'like', "%{$search}%");
                });
        });
    }

    #[Scope]
    protected function buscarEstado(Builder $query, $estado): void
    {
        $query->when($estado, function (Builder $query, string $estado) {
            $query->where('estado', $estado);
        });
    }

    #[Scope]
    protected function buscarProveedor(Builder $query, $proveedorId): void
    {
        $query->when($proveedorId, function (Builder $query, $proveedorId) {
            $query->where('proveedor_id', $proveedorId);
        });
    }

    #[Scope]
    protected function buscarFechaDesde(Builder $query, $fecha): void
    {
        $query->when($fecha, function (Builder $query, string $fecha) {
            $query->whereDate('fecha_emision', '>=', $fecha);
        });
    }

    #[Scope]
    protected function buscarFechaHasta(Builder $query, $fecha): void
    {
        $query->when($fecha, function (Builder $query, string $fecha) {
            $query->whereDate('fecha_emision', '<=', $fecha);
        });
    }

    #[Scope]
    protected function pendientesRecepcion(Builder $query): void
    {
        $query->whereDoesntHave('recepciones', function ($q) {
            $q->where('estado', 'COMPLETA');
        });
    }

    #[Scope]
    protected function pendientesPago(Builder $query): void
    {
        $query->where('estado', 'APROBADO');
    }

    #[Scope]
    protected function soloActivas(Builder $query): void
    {
        $query->where('activo', true);
    }

    // ==================== MÉTODOS ====================

    /**
     * Obtiene el total pagado hasta el momento
     */
    public function getTotalPagado(): float
    {
        // Asumiendo que tienes un módulo de pagos con relación
        // Si no, esto sería un método para implementar luego
        return 0.0;
    }

    /**
     * Obtiene el saldo pendiente
     */
    public function getSaldoPendiente(): float
    {
        return $this->total - $this->getTotalPagado();
    }

    /**
     * Verifica si la compra está completamente pagada
     */
    public function estaPagada(): bool
    {
        return $this->getSaldoPendiente() <= 0.01; // Tolerancia de 0.01
    }
}
