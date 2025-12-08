<?php

namespace Database\Seeders\Stock;

use App\Models\Stock\AtributoTipo;
use Illuminate\Database\Seeder;

class AtributoTipoSeeder extends Seeder
{
    public function run(): void
    {
        $atributos = [
            // ATRIBUTOS PARA MOTOBOMBAS
            ['codigo' => 'POT', 'nombre' => 'Potencia', 'unidad' => 'HP', 'descripcion' => 'Potencia del motor', 'orden' => 1, 'filtrable' => true, 'requerido' => true],
            ['codigo' => 'VOLT', 'nombre' => 'Voltaje', 'unidad' => 'V', 'descripcion' => 'Voltaje de operación', 'orden' => 2, 'filtrable' => true, 'requerido' => true],
            ['codigo' => 'TCORR', 'nombre' => 'Tipo de Corriente', 'unidad' => null, 'descripcion' => 'Monofásico o Trifásico', 'orden' => 3, 'filtrable' => true, 'requerido' => true],
            ['codigo' => 'CAUD', 'nombre' => 'Caudal Máximo', 'unidad' => 'L/min', 'descripcion' => 'Caudal máximo de bombeo', 'orden' => 4, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'ALT', 'nombre' => 'Altura Manométrica', 'unidad' => 'm', 'descripcion' => 'Altura máxima de bombeo', 'orden' => 5, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'MATIMP', 'nombre' => 'Material del Impulsor', 'unidad' => null, 'descripcion' => 'Material del impulsor', 'orden' => 6, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'DIASUC', 'nombre' => 'Diámetro Succión', 'unidad' => '"', 'descripcion' => 'Diámetro de succión', 'orden' => 7, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'DIADES', 'nombre' => 'Diámetro Descarga', 'unidad' => '"', 'descripcion' => 'Diámetro de descarga', 'orden' => 8, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'RPM', 'nombre' => 'RPM', 'unidad' => 'rpm', 'descripcion' => 'Revoluciones por minuto', 'orden' => 9, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'CONS', 'nombre' => 'Consumo Eléctrico', 'unidad' => 'W', 'descripcion' => 'Consumo eléctrico', 'orden' => 10, 'filtrable' => false, 'requerido' => false],

            // ATRIBUTOS PARA TANQUES
            ['codigo' => 'CAP', 'nombre' => 'Capacidad', 'unidad' => 'L', 'descripcion' => 'Capacidad del tanque', 'orden' => 11, 'filtrable' => true, 'requerido' => true],
            ['codigo' => 'MAT', 'nombre' => 'Material', 'unidad' => null, 'descripcion' => 'Material de fabricación', 'orden' => 12, 'filtrable' => true, 'requerido' => true],
            ['codigo' => 'DIAM', 'nombre' => 'Diámetro', 'unidad' => 'cm', 'descripcion' => 'Diámetro del tanque', 'orden' => 13, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'ALTURA', 'nombre' => 'Altura', 'unidad' => 'cm', 'descripcion' => 'Altura del tanque', 'orden' => 14, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'PESO', 'nombre' => 'Peso Vacío', 'unidad' => 'kg', 'descripcion' => 'Peso del tanque vacío', 'orden' => 15, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'CERT', 'nombre' => 'Certificación INTN', 'unidad' => null, 'descripcion' => 'Certificación INTN Paraguay', 'orden' => 16, 'filtrable' => true, 'requerido' => false],
            ['codigo' => 'COLOR', 'nombre' => 'Color', 'unidad' => null, 'descripcion' => 'Color del producto', 'orden' => 17, 'filtrable' => true, 'requerido' => false],
            ['codigo' => 'TTAPA', 'nombre' => 'Tipo de Tapa', 'unidad' => null, 'descripcion' => 'Tipo de tapa del tanque', 'orden' => 18, 'filtrable' => false, 'requerido' => false],

            // ATRIBUTOS PARA TUBOS
            ['codigo' => 'DNOM', 'nombre' => 'Diámetro Nominal', 'unidad' => '"', 'descripcion' => 'Diámetro nominal', 'orden' => 19, 'filtrable' => true, 'requerido' => true],
            ['codigo' => 'DINT', 'nombre' => 'Diámetro Interno', 'unidad' => 'mm', 'descripcion' => 'Diámetro interno', 'orden' => 20, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'DEXT', 'nombre' => 'Diámetro Externo', 'unidad' => 'mm', 'descripcion' => 'Diámetro externo', 'orden' => 21, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'CLASE', 'nombre' => 'Clase de Presión', 'unidad' => 'PN', 'descripcion' => 'Presión nominal', 'orden' => 22, 'filtrable' => true, 'requerido' => false],
            ['codigo' => 'LONG', 'nombre' => 'Longitud', 'unidad' => 'm', 'descripcion' => 'Longitud del tubo', 'orden' => 23, 'filtrable' => false, 'requerido' => true],
            ['codigo' => 'ESPES', 'nombre' => 'Espesor', 'unidad' => 'mm', 'descripcion' => 'Espesor de pared', 'orden' => 24, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'NORM', 'nombre' => 'Normativa', 'unidad' => null, 'descripcion' => 'Normativa aplicable', 'orden' => 25, 'filtrable' => false, 'requerido' => false],

            // ATRIBUTOS PARA VÁLVULAS/CONEXIONES
            ['codigo' => 'TROSCA', 'nombre' => 'Tipo de Rosca', 'unidad' => null, 'descripcion' => 'NPT o BSP', 'orden' => 26, 'filtrable' => true, 'requerido' => false],
            ['codigo' => 'PRESMAX', 'nombre' => 'Presión Máxima', 'unidad' => 'bar', 'descripcion' => 'Presión máxima de trabajo', 'orden' => 27, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'TEMPMAX', 'nombre' => 'Temperatura Máxima', 'unidad' => '°C', 'descripcion' => 'Temperatura máxima de operación', 'orden' => 28, 'filtrable' => false, 'requerido' => false],
            ['codigo' => 'MATCUE', 'nombre' => 'Cuerpo Material', 'unidad' => null, 'descripcion' => 'Material del cuerpo', 'orden' => 29, 'filtrable' => true, 'requerido' => false],

            // ATRIBUTO GENERAL
            ['codigo' => 'GARANT', 'nombre' => 'Garantía', 'unidad' => 'meses', 'descripcion' => 'Período de garantía', 'orden' => 30, 'filtrable' => false, 'requerido' => false],
        ];

        foreach ($atributos as $atributo) {
            AtributoTipo::create([
                'codigo' => $atributo['codigo'],
                'nombre' => $atributo['nombre'],
                'unidad' => $atributo['unidad'],
                'descripcion' => $atributo['descripcion'],
                'orden' => $atributo['orden'],
                'es_filtrable' => $atributo['filtrable'],
                'es_requerido' => $atributo['requerido'],
                'activo' => true,
                'creadoPor' => null,
            ]);
        }

        $this->command->info('✓ ' . count($atributos) . ' atributos tipo creados');
    }
}
