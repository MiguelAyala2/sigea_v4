<?php

namespace Database\Seeders\Empresa;

use App\Models\Empresa\Empresa;
use App\Models\Empresa\Sucursal;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::where('ruc', '80012345-6')->first();

        if (!$empresa) {
            $this->command->error('× Error: Empresa no encontrada. Ejecutar EmpresaSeeder primero.');
            return;
        }

        $sucursales = [
            [
                'empresa_id' => $empresa->id,
                'codigo_establecimiento' => '001',
                'nombre' => 'Casa Matriz Asunción',
                'direccion' => 'Av. Eusebio Ayala Km 4.5',
                'departamento' => 'Capital',
                'ciudad' => 'Asunción',
                'telefono' => '(021) 555-1234',
                'email' => 'matriz@aguateriasigea.com.py',
                'es_casa_central' => true,
                'activo' => true,
                'creadoPor' => null,
            ],
            [
                'empresa_id' => $empresa->id,
                'codigo_establecimiento' => '002',
                'nombre' => 'Sucursal San Lorenzo',
                'direccion' => 'Ruta 2 Km 15',
                'departamento' => 'Central',
                'ciudad' => 'San Lorenzo',
                'telefono' => '(021) 555-5678',
                'email' => 'sanlorenzo@aguateriasigea.com.py',
                'es_casa_central' => false,
                'activo' => true,
                'creadoPor' => null,
            ],
            [
                'empresa_id' => $empresa->id,
                'codigo_establecimiento' => '003',
                'nombre' => 'Sucursal Ñemby',
                'direccion' => 'Av. Cacique Lambaré c/ Ruta Luque',
                'departamento' => 'Central',
                'ciudad' => 'Ñemby',
                'telefono' => '(021) 555-9012',
                'email' => 'nemby@aguateriasigea.com.py',
                'es_casa_central' => false,
                'activo' => true,
                'creadoPor' => null,
            ],
        ];

        foreach ($sucursales as $sucursal) {
            Sucursal::create($sucursal);
            $this->command->info("✓ Sucursal creada: {$sucursal['nombre']}");
        }
    }
}