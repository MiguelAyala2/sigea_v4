<?php

namespace Database\Seeders;

use App\Models\Servicios\Promocion;
use Illuminate\Database\Seeder;

class PromocionSeeder extends Seeder
{
    public function run(): void
    {
        Promocion::create([
            'codigo' => 'PROM-001',
            'nombre' => 'MANTENIMIENTO PREVENTIVO 2X1',
            'tipo' => 'servicio',
            'descuento' => 50.00,
            'fecha_inicio' => '2025-01-12',
            'fecha_fin' => '2025-12-31',
            'descripcion' => 'PROMOCIÓN ESPECIAL MANTENIMIENTO PREVENTIVO AL 50% DE DESCUENTO',
            'activo' => true,
            'creadoPor' => 1,
        ]);

        Promocion::create([
            'codigo' => 'PROM-002',
            'nombre' => 'BLACK FRIDAY SERVICIOS',
            'tipo' => 'general',
            'descuento' => 30.00,
            'fecha_inicio' => '2025-11-24',
            'fecha_fin' => '2025-11-24',
            'descripcion' => 'PROMOCIÓN BLACK FRIDAY 30% EN TODOS LOS SERVICIOS',
            'activo' => false,
            'creadoPor' => 1,
        ]);
    }
}
