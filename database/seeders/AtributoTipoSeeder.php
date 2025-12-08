<?php

namespace Database\Seeders;

use App\Models\Stock\AtributoTipo;
use Illuminate\Database\Seeder;

class AtributoTipoSeeder extends Seeder
{
    public function run(): void
    {
        $atributos = [
            // Atributos para Motobombas
            ['nombre' => 'Potencia', 'unidad' => 'HP', 'orden' => 1, 'es_filtrable' => true],
            ['nombre' => 'Caudal', 'unidad' => 'L/min', 'orden' => 2, 'es_filtrable' => true],
            ['nombre' => 'Altura Máxima', 'unidad' => 'm', 'orden' => 3, 'es_filtrable' => true],
            ['nombre' => 'Diámetro Succión', 'unidad' => 'pulgadas', 'orden' => 4, 'es_filtrable' => true],
            ['nombre' => 'Diámetro Descarga', 'unidad' => 'pulgadas', 'orden' => 5, 'es_filtrable' => true],
            ['nombre' => 'Voltaje', 'unidad' => 'V', 'orden' => 6, 'es_filtrable' => true],
            ['nombre' => 'Fases', 'unidad' => null, 'orden' => 7, 'es_filtrable' => true],

            // Atributos generales
            ['nombre' => 'Material', 'unidad' => null, 'orden' => 10, 'es_filtrable' => true],
            ['nombre' => 'Peso', 'unidad' => 'kg', 'orden' => 11, 'es_filtrable' => false],
            ['nombre' => 'Longitud', 'unidad' => 'm', 'orden' => 12, 'es_filtrable' => false],
            ['nombre' => 'Diámetro', 'unidad' => 'mm', 'orden' => 13, 'es_filtrable' => true],
            ['nombre' => 'Capacidad', 'unidad' => 'L', 'orden' => 14, 'es_filtrable' => true],
            ['nombre' => 'Presión Máxima', 'unidad' => 'PSI', 'orden' => 15, 'es_filtrable' => true],
            ['nombre' => 'Temperatura Máxima', 'unidad' => '°C', 'orden' => 16, 'es_filtrable' => false],
        ];

        foreach ($atributos as $index => $atributo) {
            AtributoTipo::create([
                'codigo' => 'ATR-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'nombre' => $atributo['nombre'],
                'unidad' => $atributo['unidad'],
                'orden' => $atributo['orden'],
                'es_filtrable' => $atributo['es_filtrable'],
                'es_requerido' => false,
                'activo' => true,
                'creadoPor' => 1,
            ]);
        }
    }
}
