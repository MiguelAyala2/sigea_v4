<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promocion extends Model
{
    protected $table = 'servicios.promociones';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo',
        'descuento',
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'descuento' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public static function generarCodigo()
    {
        $ultimaPromocion = self::orderBy('id', 'desc')->first();
        $numero = $ultimaPromocion ? intval(substr($ultimaPromocion->codigo, 5)) + 1 : 1;
        return 'PROM-' . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }

    public function estaVigente()
    {
        $hoy = now()->startOfDay();
        return $this->activo
            && $this->fecha_inicio <= $hoy
            && $this->fecha_fin >= $hoy;
    }

    public function scopeVigentes($query)
    {
        $hoy = now()->startOfDay();
        return $query->where('activo', true)
            ->where('fecha_inicio', '<=', $hoy)
            ->where('fecha_fin', '>=', $hoy);
    }
}
