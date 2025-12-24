<?php

namespace App\Models\Servicios;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    protected $table = 'servicios.descuentos';

    protected $fillable = [
        'codigo',
        'descripcion',
        'tipo_descuento',
        'valor',
        'aplicable_a',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public static function generarCodigo()
    {
        $ultimoDescuento = self::orderBy('id', 'desc')->first();
        $numero = $ultimoDescuento ? intval(substr($ultimoDescuento->codigo, 5)) + 1 : 1;
        return 'DESC-' . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }

    public function estaVigente()
    {
        if (!$this->activo) {
            return false;
        }

        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return true; // Sin límite de fechas
        }

        $hoy = now()->startOfDay();
        return $this->fecha_inicio <= $hoy && $this->fecha_fin >= $hoy;
    }

    public function getValorFormateadoAttribute()
    {
        if ($this->tipo_descuento === 'porcentaje') {
            return number_format($this->valor, 0) . '%';
        }
        return '₲ ' . number_format($this->valor, 0, ',', '.');
    }
}
