<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class IntegracionDocumentos extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'compras.COMPRAS_INTEGRACION';

    protected $fillable = [
        'documento_origen_tipo',
        'documento_origen_id',
        'documento_destino_tipo',
        'documento_destino_id',
        'tipo_relacion',
        'porcentaje_relacion',
        'es_completa',
        'observaciones',
        'detalles_relacion',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje_relacion' => 'decimal:2',
            'es_completa' => 'boolean',
            'detalles_relacion' => 'array',
        ];
    }

    // ==================== CONSTANTES ====================

    public const TIPOS_DOCUMENTO = [
        'PEDIDO_COMPRA' => 'Pedido de Compra',
        'PRESUPUESTO' => 'Presupuesto',
        'ORDEN_COMPRA' => 'Orden de Compra',
        'COMPRA' => 'Compra/Factura',
        'RECEPCION' => 'Recepción',
        'PAGO' => 'Pago',
    ];

    public const TIPOS_RELACION = [
        'GENERA' => 'Genera',
        'SE_CONVIERTE_EN' => 'Se convierte en',
        'DEPENDE_DE' => 'Depende de',
        'REFERENCIA' => 'Referencia',
        'CORRIGE' => 'Corrige',
        'ANULA' => 'Anula',
    ];

    // ==================== RELACIONES POLIMÓRFICAS ====================

    public function documentoOrigen(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'documento_origen_tipo', 'documento_origen_id');
    }

    public function documentoDestino(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'documento_destino_tipo', 'documento_destino_id');
    }

    /**
     * Métodos específicos para cada tipo de documento origen
     */
    public function pedidoOrigen(): BelongsTo
    {
        return $this->belongsTo(PedidoCompra::class, 'documento_origen_id')
            ->where('documento_origen_tipo', 'PEDIDO_COMPRA');
    }

    public function presupuestoOrigen(): BelongsTo
    {
        return $this->belongsTo(PresupuestoProveedor::class, 'documento_origen_id')
            ->where('documento_origen_tipo', 'PRESUPUESTO');
    }

    public function ordenOrigen(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'documento_origen_id')
            ->where('documento_origen_tipo', 'ORDEN_COMPRA');
    }

    public function compraOrigen(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'documento_origen_id')
            ->where('documento_origen_tipo', 'COMPRA');
    }

    public function recepcionOrigen(): BelongsTo
    {
        return $this->belongsTo(CompraRecepcion::class, 'documento_origen_id')
            ->where('documento_origen_tipo', 'RECEPCION');
    }

    /**
     * Métodos específicos para cada tipo de documento destino
     */
    public function pedidoDestino(): BelongsTo
    {
        return $this->belongsTo(PedidoCompra::class, 'documento_destino_id')
            ->where('documento_destino_tipo', 'PEDIDO_COMPRA');
    }

    public function presupuestoDestino(): BelongsTo
    {
        return $this->belongsTo(PresupuestoProveedor::class, 'documento_destino_id')
            ->where('documento_destino_tipo', 'PRESUPUESTO');
    }

    public function ordenDestino(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'documento_destino_id')
            ->where('documento_destino_tipo', 'ORDEN_COMPRA');
    }

    public function compraDestino(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'documento_destino_id')
            ->where('documento_destino_tipo', 'COMPRA');
    }

    public function recepcionDestino(): BelongsTo
    {
        return $this->belongsTo(CompraRecepcion::class, 'documento_destino_id')
            ->where('documento_destino_tipo', 'RECEPCION');
    }

    public function pagoDestino(): BelongsTo
    {
        return $this->belongsTo(PagoProveedor::class, 'documento_destino_id')
            ->where('documento_destino_tipo', 'PAGO');
    }

    // ==================== ACCESSORS ====================

    public function getDocumentoOrigenTipoTextoAttribute(): string
    {
        return self::TIPOS_DOCUMENTO[$this->documento_origen_tipo] ?? $this->documento_origen_tipo;
    }

    public function getDocumentoDestinoTipoTextoAttribute(): string
    {
        return self::TIPOS_DOCUMENTO[$this->documento_destino_tipo] ?? $this->documento_destino_tipo;
    }

    public function getTipoRelacionTextoAttribute(): string
    {
        return self::TIPOS_RELACION[$this->tipo_relacion] ?? $this->tipo_relacion;
    }

    public function getPorcentajeRelacionFormateadoAttribute(): string
    {
        return $this->porcentaje_relacion ? 
            number_format($this->porcentaje_relacion, 2, ',', '.') . '%' : 
            '100%';
    }

    public function getDescripcionRelacionAttribute(): string
    {
        $origen = $this->documentoOrigenTipoTexto;
        $destino = $this->documentoDestinoTipoTexto;
        $relacion = $this->tipoRelacionTexto;

        return "{$origen} {$relacion} {$destino}";
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->where('observaciones', 'like', "%{$search}%")
                ->orWhereHasMorph('documentoOrigen', ['*'], function ($q) use ($search) {
                    $q->where('numero_documento', 'like', "%{$search}%");
                })
                ->orWhereHasMorph('documentoDestino', ['*'], function ($q) use ($search) {
                    $q->where('numero_documento', 'like', "%{$search}%");
                });
        });
    }

    #[Scope]
    protected function buscarTipoOrigen(Builder $query, $tipo): void
    {
        $query->when($tipo, function (Builder $query, string $tipo) {
            $query->where('documento_origen_tipo', $tipo);
        });
    }

    #[Scope]
    protected function buscarTipoDestino(Builder $query, $tipo): void
    {
        $query->when($tipo, function (Builder $query, string $tipo) {
            $query->where('documento_destino_tipo', $tipo);
        });
    }

    #[Scope]
    protected function buscarTipoRelacion(Builder $query, $relacion): void
    {
        $query->when($relacion, function (Builder $query, string $relacion) {
            $query->where('tipo_relacion', $relacion);
        });
    }

    #[Scope]
    protected function buscarDocumentoOrigen(Builder $query, $tipo, $id): void
    {
        $query->when($tipo && $id, function (Builder $query) use ($tipo, $id) {
            $query->where('documento_origen_tipo', $tipo)
                  ->where('documento_origen_id', $id);
        });
    }

    #[Scope]
    protected function buscarDocumentoDestino(Builder $query, $tipo, $id): void
    {
        $query->when($tipo && $id, function (Builder $query) use ($tipo, $id) {
            $query->where('documento_destino_tipo', $tipo)
                  ->where('documento_destino_id', $id);
        });
    }

    #[Scope]
    protected function relacionesCompletas(Builder $query): void
    {
        $query->where('es_completa', true);
    }

    #[Scope]
    protected function relacionesParciales(Builder $query): void
    {
        $query->where('es_completa', false);
    }

    // ==================== MÉTODOS ====================

    /**
     * Verificar si la relación es completa (100% del origen procesado en destino)
     */
    public function esCompleta(): bool
    {
        return $this->es_completa || $this->porcentaje_relacion >= 99.99;
    }

    /**
     * Obtener el documento origen basado en su tipo
     */
    public function obtenerDocumentoOrigen()
    {
        $method = strtolower($this->documento_origen_tipo) . 'Origen';
        
        if (method_exists($this, $method)) {
            return $this->$method;
        }
        
        return $this->documentoOrigen;
    }

    /**
     * Obtener el documento destino basado en su tipo
     */
    public function obtenerDocumentoDestino()
    {
        $method = strtolower($this->documento_destino_tipo) . 'Destino';
        
        if (method_exists($this, $method)) {
            return $this->$method;
        }
        
        return $this->documentoDestino;
    }

    /**
     * Crear una relación entre documentos
     */
    public static function crearRelacion(
        string $origenTipo,
        int $origenId,
        string $destinoTipo,
        int $destinoId,
        string $tipoRelacion = 'GENERA',
        float $porcentaje = 100.00,
        bool $esCompleta = true,
        ?string $observaciones = null,
        ?array $detalles = null
    ): self {
        return self::create([
            'documento_origen_tipo' => $origenTipo,
            'documento_origen_id' => $origenId,
            'documento_destino_tipo' => $destinoTipo,
            'documento_destino_id' => $destinoId,
            'tipo_relacion' => $tipoRelacion,
            'porcentaje_relacion' => $porcentaje,
            'es_completa' => $esCompleta,
            'observaciones' => $observaciones,
            'detalles_relacion' => $detalles,
            'creadoPor' => auth()->id(),
        ]);
    }
}