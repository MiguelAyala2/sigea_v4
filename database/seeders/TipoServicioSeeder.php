<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposServicio = [
            ['descripcion' => 'VERIFICACIÓN (DIAGNÓSTICO)', 'costo' => 80000],
            ['descripcion' => 'MANTENIMIENTO PREVENTIVO', 'costo' => 250000],
            ['descripcion' => 'REPARACIÓN CORRECTIVA', 'costo' => 350000],
            ['descripcion' => 'MANTENIMIENTO CORRECTIVO', 'costo' => 300000],
            ['descripcion' => 'REACONDICIONAMIENTO', 'costo' => 550000],
            ['descripcion' => 'REPARACIÓN MAYOR', 'costo' => 900000],
            ['descripcion' => 'INSTALACIÓN', 'costo' => 180000],
            ['descripcion' => 'PUESTA EN MARCHA', 'costo' => 120000],
            ['descripcion' => 'SERVICIO DE EMERGENCIA', 'costo' => 400000],
            ['descripcion' => 'INSPECCIÓN TÉCNICA CERTIFICADA', 'costo' => 150000],
            ['descripcion' => 'OPTIMIZACIÓN / MEJORA', 'costo' => 320000],
            ['descripcion' => 'ASESORÍA TÉCNICA', 'costo' => 100000],
        ];

        foreach ($tiposServicio as $index => $tipo) {
            $codigo = 'TDS' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            DB::table('servicios.TIPOS_SERVICIO')->insert([
                'codigo' => $codigo,
                'descripcion' => $tipo['descripcion'],
                'costo' => $tipo['costo'],
                'activo' => true,
                'creadoPor' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "✓ Tipos de Servicio creados exitosamente!\n";
        echo "  Total de tipos creados: " . count($tiposServicio) . "\n";
    }
}
