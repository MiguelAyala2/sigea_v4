<?php

namespace Database\Seeders;

use App\Models\Servicios\Descuento;
use Illuminate\Database\Seeder;

class DescuentoSeeder extends Seeder
{
    public function run(): void
    {
        Descuento::create([
            'codigo' => 'DESC-001',
            'descripcion' => 'CLIENTE FRECUENTE',
            'tipo_descuento' => 'porcentaje',
            'valor' => 15.00,
            'aplicable_a' => 'TODOS LOS SERVICIOS',
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'observaciones' => 'DESCUENTO PARA CLIENTES FRECUENTES',
            'activo' => true,
            'creadoPor' => 1,
        ]);

        Descuento::create([
            'codigo' => 'DESC-002',
            'descripcion' => 'PAGO AL CONTADO',
            'tipo_descuento' => 'monto_fijo',
            'valor' => 50000.00,
            'aplicable_a' => 'SERVICIOS > ₲ 500.000',
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'observaciones' => 'DESCUENTO POR PAGO AL CONTADO EN SERVICIOS SUPERIORES A 500.000',
            'activo' => true,
            'creadoPor' => 1,
        ]);
    }
}
