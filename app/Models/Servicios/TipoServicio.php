<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;

class TipoServicio extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'servicios.TIPOS_SERVICIO';

    protected $fillable = [
        'codigo',
        'descripcion',
        'costo',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Scopes
    public function scopeBuscador(Builder $query, ?string $termino): void
    {
        if (!empty($termino)) {
            $query->where(function ($q) use ($termino) {
                $q->where('codigo', 'ILIKE', "%{$termino}%")
                  ->orWhere('descripcion', 'ILIKE', "%{$termino}%");
            });
        }
    }

    public function scopeBuscarActivo(Builder $query, ?string $activo): void
    {
        if ($activo !== '' && $activo !== null) {
            $query->where('activo', (bool) $activo);
        }
    }

    public function scopeSoloActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    // Generar código automático
    public static function generarCodigo(): string
    {
        $ultimo = self::orderBy('id', 'desc')->first();

        if ($ultimo) {
            // Extraer el número del último código (TDS001 -> 001)
            $ultimoNumero = (int) substr($ultimo->codigo, 3);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return 'TDS' . str_pad($nuevoNumero, 3, '0', STR_PAD_LEFT);
    }
}
