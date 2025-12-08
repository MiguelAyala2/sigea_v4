<?php

namespace Database\Seeders\Stock;

use App\Models\Stock\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            // Unidad
            ['codigo' => 'UN', 'nombre' => 'Unidad', 'simbolo' => 'un', 'tipo' => 'UNIDAD'],
            ['codigo' => 'PAR', 'nombre' => 'Par', 'simbolo' => 'par', 'tipo' => 'UNIDAD'],
            ['codigo' => 'JGO', 'nombre' => 'Juego', 'simbolo' => 'jgo', 'tipo' => 'UNIDAD'],
            ['codigo' => 'CJA', 'nombre' => 'Caja', 'simbolo' => 'cja', 'tipo' => 'UNIDAD'],
            ['codigo' => 'ROL', 'nombre' => 'Rollo', 'simbolo' => 'rol', 'tipo' => 'UNIDAD'],

            // Longitud
            ['codigo' => 'M', 'nombre' => 'Metro', 'simbolo' => 'm', 'tipo' => 'LONGITUD'],
            ['codigo' => 'CM', 'nombre' => 'Centímetro', 'simbolo' => 'cm', 'tipo' => 'LONGITUD'],
            ['codigo' => 'MM', 'nombre' => 'Milímetro', 'simbolo' => 'mm', 'tipo' => 'LONGITUD'],
            ['codigo' => 'PLG', 'nombre' => 'Pulgada', 'simbolo' => '"', 'tipo' => 'LONGITUD'],
            ['codigo' => 'PIE', 'nombre' => 'Pie', 'simbolo' => '\'', 'tipo' => 'LONGITUD'],

            // Volumen/Capacidad
            ['codigo' => 'L', 'nombre' => 'Litro', 'simbolo' => 'L', 'tipo' => 'VOLUMEN'],
            ['codigo' => 'ML', 'nombre' => 'Mililitro', 'simbolo' => 'ml', 'tipo' => 'VOLUMEN'],
            ['codigo' => 'M3', 'nombre' => 'Metro Cúbico', 'simbolo' => 'm³', 'tipo' => 'VOLUMEN'],
            ['codigo' => 'GAL', 'nombre' => 'Galón', 'simbolo' => 'gal', 'tipo' => 'VOLUMEN'],

            // Peso
            ['codigo' => 'KG', 'nombre' => 'Kilogramo', 'simbolo' => 'kg', 'tipo' => 'PESO'],
            ['codigo' => 'G', 'nombre' => 'Gramo', 'simbolo' => 'g', 'tipo' => 'PESO'],
            ['codigo' => 'TON', 'nombre' => 'Tonelada', 'simbolo' => 'ton', 'tipo' => 'PESO'],

            // Potencia
            ['codigo' => 'HP', 'nombre' => 'Caballo de Fuerza', 'simbolo' => 'HP', 'tipo' => 'POTENCIA'],
            ['codigo' => 'W', 'nombre' => 'Watt', 'simbolo' => 'W', 'tipo' => 'POTENCIA'],
            ['codigo' => 'KW', 'nombre' => 'Kilowatt', 'simbolo' => 'kW', 'tipo' => 'POTENCIA'],

            // Presión
            ['codigo' => 'BAR', 'nombre' => 'Bar', 'simbolo' => 'bar', 'tipo' => 'PRESION'],
            ['codigo' => 'PSI', 'nombre' => 'Libras por Pulgada Cuadrada', 'simbolo' => 'psi', 'tipo' => 'PRESION'],
            ['codigo' => 'MCA', 'nombre' => 'Metros de Columna de Agua', 'simbolo' => 'mca', 'tipo' => 'PRESION'],

            // Caudal
            ['codigo' => 'LPM', 'nombre' => 'Litros por Minuto', 'simbolo' => 'L/min', 'tipo' => 'CAUDAL'],
            ['codigo' => 'M3H', 'nombre' => 'Metros Cúbicos por Hora', 'simbolo' => 'm³/h', 'tipo' => 'CAUDAL'],

            // Velocidad
            ['codigo' => 'RPM', 'nombre' => 'Revoluciones por Minuto', 'simbolo' => 'rpm', 'tipo' => 'VELOCIDAD'],

            // Temperatura
            ['codigo' => 'CEL', 'nombre' => 'Grados Celsius', 'simbolo' => '°C', 'tipo' => 'TEMPERATURA'],

            // Eléctrico
            ['codigo' => 'V', 'nombre' => 'Voltio', 'simbolo' => 'V', 'tipo' => 'ELECTRICO'],
            ['codigo' => 'A', 'nombre' => 'Amperio', 'simbolo' => 'A', 'tipo' => 'ELECTRICO'],
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::create([
                'codigo' => $unidad['codigo'],
                'nombre' => $unidad['nombre'],
                'simbolo' => $unidad['simbolo'],
                'activo' => true,
                'creadoPor' => null,
            ]);
        }

        $this->command->info('✓ ' . count($unidades) . ' unidades de medida creadas');
    }
}
