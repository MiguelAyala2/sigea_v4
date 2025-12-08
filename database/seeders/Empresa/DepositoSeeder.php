<?php

namespace Database\Seeders\Empresa;

use App\Models\Empresa\Deposito;
use App\Models\Empresa\Sucursal;
use Illuminate\Database\Seeder;

class DepositoSeeder extends Seeder
{
    public function run(): void
    {
        $sucursales = Sucursal::all();

        if ($sucursales->isEmpty()) {
            $this->command->error('× Error: No hay sucursales. Ejecutar SucursalSeeder primero.');
            return;
        }

        $depositos = [
            [
                'codigo' => 'DEP-001',
                'nombre' => 'Depósito Casa Matriz',
                'sucursal_codigo_est' => '001',
                'descripcion' => 'Depósito principal - Grande - 500m²',
                'es_principal' => true,
            ],
            [
                'codigo' => 'DEP-002',
                'nombre' => 'Depósito San Lorenzo',
                'sucursal_codigo_est' => '002',
                'descripcion' => 'Depósito sucursal - Media - 300m²',
                'es_principal' => true,
            ],
            [
                'codigo' => 'DEP-003',
                'nombre' => 'Depósito Ñemby',
                'sucursal_codigo_est' => '003',
                'descripcion' => 'Depósito sucursal - Media - 250m²',
                'es_principal' => true,
            ],
        ];

        foreach ($depositos as $depositoData) {
            $sucursal = $sucursales->where('codigo_establecimiento', $depositoData['sucursal_codigo_est'])->first();

            if (!$sucursal) {
                $this->command->warn("× Sucursal {$depositoData['sucursal_codigo_est']} no encontrada");
                continue;
            }

            Deposito::create([
                'sucursal_id' => $sucursal->id,
                'codigo' => $depositoData['codigo'],
                'nombre' => $depositoData['nombre'],
                'descripcion' => $depositoData['descripcion'],
                'es_principal' => $depositoData['es_principal'],
                'permite_venta' => true,
                'activo' => true,
                'creadoPor' => null,
            ]);

            $this->command->info("✓ Depósito creado: {$depositoData['nombre']}");
        }
    }
}