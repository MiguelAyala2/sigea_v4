<?php

namespace Database\Seeders\Stock;

use App\Models\Stock\Marca;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            // Marcas Premium Internacionales
            ['codigo' => 'GRU', 'nombre' => 'Grundfos', 'descripcion' => 'Motobombas y sistemas de bombeo premium - Dinamarca'],
            ['codigo' => 'PED', 'nombre' => 'Pedrollo', 'descripcion' => 'Motobombas de alta calidad - Italia'],
            ['codigo' => 'NIC', 'nombre' => 'Nicoll', 'descripcion' => 'Accesorios y conexiones premium - Francia'],
            ['codigo' => 'WAV', 'nombre' => 'Wavin', 'descripcion' => 'Sistemas de tuberías - Holanda'],

            // Marcas Brasileñas
            ['codigo' => 'DAN', 'nombre' => 'Dancor', 'descripcion' => 'Motobombas residenciales e industriales - Brasil'],
            ['codigo' => 'ANA', 'nombre' => 'Anauger', 'descripcion' => 'Motobombas solares y convencionales - Brasil'],
            ['codigo' => 'TIG', 'nombre' => 'Tigre', 'descripcion' => 'Tubos y conexiones PVC - Brasil'],
            ['codigo' => 'AMA', 'nombre' => 'Amanco', 'descripcion' => 'Tubos y conexiones - Brasil'],
            ['codigo' => 'ETE', 'nombre' => 'Eternit', 'descripcion' => 'Tanques de fibrocemento - Brasil'],

            // Marcas Mexicanas
            ['codigo' => 'ROT', 'nombre' => 'Rotoplas', 'descripcion' => 'Tanques de agua de polietileno - México'],

            // Marcas Paraguayas
            ['codigo' => 'PLS', 'nombre' => 'Plastisur', 'descripcion' => 'Tanques de polietileno fabricación nacional - Paraguay'],
            ['codigo' => 'PLT', 'nombre' => 'Plastipar', 'descripcion' => 'Tubos PVC fabricación nacional - Paraguay'],

            // Marcas Chinas (Económicas)
            ['codigo' => 'LEO', 'nombre' => 'Leo', 'descripcion' => 'Motobombas línea económica - China'],
            ['codigo' => 'SHI', 'nombre' => 'Shimge', 'descripcion' => 'Motobombas y equipos de bombeo - China'],
            ['codigo' => 'VIQ', 'nombre' => 'Viqua', 'descripcion' => 'Purificadores y tratamiento de agua - China'],
        ];

        foreach ($marcas as $marca) {
            Marca::create([
                'codigo' => $marca['codigo'],
                'nombre' => $marca['nombre'],
                'descripcion' => $marca['descripcion'],
                'activo' => true,
                'creadoPor' => null,
            ]);
        }

        $this->command->info('✓ ' . count($marcas) . ' marcas creadas');
    }
}