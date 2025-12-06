<?php

namespace App\Models\Empresa;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Empresa extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'empresa.EMPRESA';

    protected $fillable = [
        'razon_social',
        'nombre_fantasia',
        'ruc',
        'dv',
        'tipo_contribuyente',
        'regimen_tributario',
        'obligado_factura_electronica',
        'direccion',
        'departamento',
        'ciudad',
        'telefono',
        'email',
        'sitio_web',
        'logo_path',
        'fecha_inicio_actividad',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'obligado_factura_electronica' => 'boolean',
            'activo' => 'boolean',
            'fecha_inicio_actividad' => 'date',
        ];
    }

    // ==================== CONSTANTES ====================

    public const TIPOS_CONTRIBUYENTE = [
        'fisica' => 'Persona Física',
        'juridica' => 'Persona Jurídica',
    ];

    public const REGIMENES_TRIBUTARIOS = [
        'general' => 'Régimen General',
        'simplificado' => 'Régimen Simplificado',
        'resimple' => 'RESIMPLE',
    ];

    // ==================== RELACIONES ====================

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class, 'empresa_id');
    }

    public function timbrados(): HasMany
    {
        return $this->hasMany(Timbrado::class, 'empresa_id');
    }

    public function actividadesEconomicas(): BelongsToMany
    {
        return $this->belongsToMany(
            ActividadEconomica::class,
            'empresa.EMPRESA_ACTIVIDAD_ECONOMICA',
            'empresa_id',
            'actividad_economica_id'
        )->withPivot('es_principal')->withTimestamps();
    }

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    // ==================== ACCESSORS ====================

    public function getRucCompletoAttribute(): string
    {
        return $this->ruc . '-' . $this->dv;
    }

    public function getTipoContribuyenteTextoAttribute(): string
    {
        return self::TIPOS_CONTRIBUYENTE[$this->tipo_contribuyente] ?? $this->tipo_contribuyente;
    }

    public function getRegimenTributarioTextoAttribute(): string
    {
        return self::REGIMENES_TRIBUTARIOS[$this->regimen_tributario] ?? $this->regimen_tributario;
    }

    // ==================== MÉTODOS ESTÁTICOS ====================

    /**
     * Obtiene la empresa actual (singleton)
     */
    public static function actual(): ?self
    {
        return self::first();
    }

    /**
     * Calcula el dígito verificador del RUC paraguayo
     */
    public static function calcularDV(string $ruc): string
    {
        $ruc = preg_replace('/[^0-9]/', '', $ruc);
        $baseMax = 11;
        $k = 2;
        $total = 0;

        for ($i = strlen($ruc) - 1; $i >= 0; $i--) {
            $total += intval($ruc[$i]) * $k;
            $k++;
            if ($k > $baseMax) {
                $k = 2;
            }
        }

        $resto = $total % 11;
        $dv = $resto > 1 ? 11 - $resto : 0;

        return (string) $dv;
    }
}
