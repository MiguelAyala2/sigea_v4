<?php

namespace Database\Seeders\Empresa;

use App\Models\Empresa\Empresa;
use App\Models\Empresa\PuntoExpedicion;
use App\Models\Empresa\Sucursal;
use App\Models\Empresa\Timbrado;
use Illuminate\Database\Seeder;

class TimbradoSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();
        $sucursales = Sucursal::all();

        if (!$empresa || $sucursales->isEmpty()) {
            $this->command->error('× Error: No hay empresa o sucursales. Ejecutar seeders previos primero.');
            return;
        }

        $timbrados = [
            [
                'sucursal_codigo_est' => '001',
                'numero_timbrado' => '15234567',
                'punto_expedicion_codigo' => '001',
                'numero_desde' => 1,
                'numero_hasta' => 9999999,
            ],
            [
                'sucursal_codigo_est' => '002',
                'numero_timbrado' => '15234568',
                'punto_expedicion_codigo' => '001',
                'numero_desde' => 1,
                'numero_hasta' => 9999999,
            ],
            [
                'sucursal_codigo_est' => '003',
                'numero_timbrado' => '15234569',
                'punto_expedicion_codigo' => '001',
                'numero_desde' => 1,
                'numero_hasta' => 9999999,
            ],
        ];

        foreach ($timbrados as $timbradoData) {
            $sucursal = $sucursales->where('codigo_establecimiento', $timbradoData['sucursal_codigo_est'])->first();

            if (!$sucursal) {
                $this->command->warn("× Sucursal {$timbradoData['sucursal_codigo_est']} no encontrada");
                continue;
            }

            // Crear punto de expedición
            $puntoExpedicion = PuntoExpedicion::create([
                'sucursal_id' => $sucursal->id,
                'codigo' => $timbradoData['punto_expedicion_codigo'],
                'nombre' => "Caja Principal - {$sucursal->nombre}",
                'tipo' => 'caja',
                'activo' => true,
                'creadoPor' => null,
            ]);

            // Crear timbrado - CORREGIDO: 'numero_desde' en lugar de 'numero_desdo'
            Timbrado::create([
                'empresa_id' => $empresa->id,
                'numero_timbrado' => $timbradoData['numero_timbrado'],
                'fecha_inicio_vigencia' => '2025-01-15',
                'fecha_fin_vigencia' => '2025-12-31',
                'tipo_documento' => 'factura',
                'numero_desde' => $timbradoData['numero_desde'],  // ← CORREGIDO AQUÍ
                'numero_hasta' => $timbradoData['numero_hasta'],
                'numero_actual' => 0,
                'es_electronico' => true,
                'cdc_ambiente' => 'produccion',
                'activo' => true,
                'creadoPor' => null,
            ]);

            $this->command->info("✓ Timbrado creado: {$timbradoData['numero_timbrado']} ({$sucursal->nombre})");
        }
    }
}