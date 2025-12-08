<?php

namespace Database\Seeders\Stock;

use App\Models\Stock\AtributoTipo;
use App\Models\Stock\Categoria;
use App\Models\Stock\Marca;
use App\Models\Stock\Precio;
use App\Models\Stock\Producto;
use App\Models\Stock\UnidadMedida;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    private $marcas;
    private $categorias;
    private $unidades;
    private $atributos;

    public function run(): void
    {
        $this->cargarReferencias();

        $this->crearMotobombas();
        $this->crearTanques();
        $this->crearTubos();
        $this->crearConexiones();
        $this->crearValvulas();

        $this->command->info('✓ Productos creados con atributos y precios');
    }

    private function cargarReferencias(): void
    {
        $this->marcas = Marca::pluck('id', 'codigo')->toArray();
        $this->categorias = Categoria::pluck('id', 'codigo')->toArray();
        $this->unidades = UnidadMedida::pluck('id', 'codigo')->toArray();
        $this->atributos = AtributoTipo::pluck('id', 'codigo')->toArray();
    }

    private function crearMotobombas(): void
    {
        // Motobomba Grundfos JP 5 - 0.5 HP (Premium)
        $producto = $this->crearProducto([
            'codigo' => 'MOTO-GRU-JP5-05',
            'nombre' => 'Motobomba Periférica Grundfos JP 5 - 0.5 HP',
            'descripcion' => 'Motobomba periférica premium Grundfos, ideal para uso residencial',
            'marca' => 'GRU',
            'categoria' => 'MOTB-PER-05',
            'unidad' => 'UN',
            'precio_compra' => 850000,
            'precio_venta' => 1200000,
            'margen' => 41.18,
            'stock_minimo' => 2,
        ]);
        $this->agregarAtributos($producto, [
            'POT' => '0.5', 'VOLT' => '220', 'TCORR' => 'Monofásico',
            'CAUD' => '40', 'ALT' => '40', 'MATIMP' => 'Acero Inoxidable',
            'GARANT' => '24',
        ]);

        // Motobomba Pedrollo PKm 60 - 0.5 HP (Premium)
        $producto = $this->crearProducto([
            'codigo' => 'MOTO-PED-PKM60-05',
            'nombre' => 'Motobomba Periférica Pedrollo PKm 60 - 0.5 HP',
            'descripcion' => 'Motobomba periférica italiana de alta calidad',
            'marca' => 'PED',
            'categoria' => 'MOTB-PER-05',
            'unidad' => 'UN',
            'precio_compra' => 780000,
            'precio_venta' => 1100000,
            'margen' => 41.03,
            'stock_minimo' => 2,
        ]);
        $this->agregarAtributos($producto, [
            'POT' => '0.5', 'VOLT' => '220', 'TCORR' => 'Monofásico',
            'CAUD' => '50', 'ALT' => '45', 'MATIMP' => 'Bronce',
            'GARANT' => '24',
        ]);

        // Motobomba Leo XKm 60 - 0.5 HP (Económica)
        $producto = $this->crearProducto([
            'codigo' => 'MOTO-LEO-XKM60-05',
            'nombre' => 'Motobomba Periférica Leo XKm 60 - 0.5 HP',
            'descripcion' => 'Motobomba periférica línea económica, excelente relación precio-calidad',
            'marca' => 'LEO',
            'categoria' => 'MOTB-PER-05',
            'unidad' => 'UN',
            'precio_compra' => 420000,
            'precio_venta' => 650000,
            'margen' => 54.76,
            'stock_minimo' => 3,
        ]);
        $this->agregarAtributos($producto, [
            'POT' => '0.5', 'VOLT' => '220', 'TCORR' => 'Monofásico',
            'CAUD' => '45', 'ALT' => '40', 'MATIMP' => 'Acero',
            'GARANT' => '12',
        ]);

        // Motobomba Dancor CP-4A - 1 HP Monofásica
        $producto = $this->crearProducto([
            'codigo' => 'MOTO-DAN-CP4A-10',
            'nombre' => 'Motobomba Centrífuga Dancor CP-4A - 1 HP Monofásica',
            'descripcion' => 'Motobomba centrífuga brasileña para uso residencial e industrial liviano',
            'marca' => 'DAN',
            'categoria' => 'MOTB-CEN-MONO',
            'unidad' => 'UN',
            'precio_compra' => 1200000,
            'precio_venta' => 1750000,
            'margen' => 45.83,
            'stock_minimo' => 2,
        ]);
        $this->agregarAtributos($producto, [
            'POT' => '1', 'VOLT' => '220', 'TCORR' => 'Monofásico',
            'CAUD' => '120', 'ALT' => '25', 'MATIMP' => 'Bronce',
            'GARANT' => '18',
        ]);

        // Motobomba Dancor CP-4T - 1 HP Trifásica
        $producto = $this->crearProducto([
            'codigo' => 'MOTO-DAN-CP4T-10',
            'nombre' => 'Motobomba Centrífuga Dancor CP-4T - 1 HP Trifásica',
            'descripcion' => 'Motobomba centrífuga trifásica para uso industrial',
            'marca' => 'DAN',
            'categoria' => 'MOTB-CEN-TRI',
            'unidad' => 'UN',
            'precio_compra' => 1350000,
            'precio_venta' => 1950000,
            'margen' => 44.44,
            'stock_minimo' => 1,
        ]);
        $this->agregarAtributos($producto, [
            'POT' => '1', 'VOLT' => '380', 'TCORR' => 'Trifásico',
            'CAUD' => '120', 'ALT' => '25', 'MATIMP' => 'Bronce',
            'GARANT' => '18',
        ]);

        // Motobomba Sumergible Shimge 4" - 1.5 HP
        $producto = $this->crearProducto([
            'codigo' => 'MOTO-SHI-4POZ-15',
            'nombre' => 'Motobomba Sumergible Shimge 4" - 1.5 HP',
            'descripcion' => 'Motobomba sumergible para pozos profundos',
            'marca' => 'SHI',
            'categoria' => 'MOTB-SUM-POZ',
            'unidad' => 'UN',
            'precio_compra' => 1800000,
            'precio_venta' => 2600000,
            'margen' => 44.44,
            'stock_minimo' => 1,
        ]);
        $this->agregarAtributos($producto, [
            'POT' => '1.5', 'VOLT' => '220', 'TCORR' => 'Monofásico',
            'CAUD' => '60', 'ALT' => '80', 'MATIMP' => 'Acero Inoxidable',
            'GARANT' => '12',
        ]);
    }

    private function crearTanques(): void
    {
        $capacidades = [
            ['cap' => 500, 'codigo' => 'TANQ-POL-500', 'compra' => 450000, 'venta' => 650000],
            ['cap' => 600, 'codigo' => 'TANQ-POL-500', 'compra' => 520000, 'venta' => 750000],
            ['cap' => 850, 'codigo' => 'TANQ-POL-1000', 'compra' => 680000, 'venta' => 980000],
            ['cap' => 1000, 'codigo' => 'TANQ-POL-1000', 'compra' => 750000, 'venta' => 1080000],
            ['cap' => 1100, 'codigo' => 'TANQ-POL-2000', 'compra' => 820000, 'venta' => 1180000],
            ['cap' => 1500, 'codigo' => 'TANQ-POL-2000', 'compra' => 1050000, 'venta' => 1520000],
        ];

        // Tanques Rotoplas (México)
        foreach ($capacidades as $index => $datos) {
            if ($index < 4) { // Primeros 4 de Rotoplas
                $producto = $this->crearProducto([
                    'codigo' => "TANQ-ROT-{$datos['cap']}-NEG",
                    'nombre' => "Tanque Rotoplas {$datos['cap']} L Negro",
                    'descripcion' => "Tanque de agua de polietileno Rotoplas, capacidad {$datos['cap']} litros",
                    'marca' => 'ROT',
                    'categoria' => $datos['codigo'],
                    'unidad' => 'UN',
                    'precio_compra' => $datos['compra'],
                    'precio_venta' => $datos['venta'],
                    'margen' => round((($datos['venta'] - $datos['compra']) / $datos['venta']) * 100, 2),
                    'stock_minimo' => 2,
                ]);
                $this->agregarAtributos($producto, [
                    'CAP' => (string)$datos['cap'],
                    'MAT' => 'Polietileno',
                    'COLOR' => 'Negro',
                    'CERT' => 'Sí',
                    'GARANT' => '60',
                ]);
            }
        }

        // Tanques Plastisur (Paraguay)
        foreach ($capacidades as $index => $datos) {
            if ($index >= 4) { // Últimos 2 de Plastisur
                $producto = $this->crearProducto([
                    'codigo' => "TANQ-PLS-{$datos['cap']}-AZU",
                    'nombre' => "Tanque Plastisur {$datos['cap']} L Azul",
                    'descripcion' => "Tanque de agua polietileno fabricación paraguaya, {$datos['cap']} litros",
                    'marca' => 'PLS',
                    'categoria' => $datos['codigo'],
                    'unidad' => 'UN',
                    'precio_compra' => $datos['compra'] * 0.95, // 5% más económico
                    'precio_venta' => $datos['venta'] * 0.95,
                    'margen' => round((($datos['venta'] - $datos['compra']) / $datos['venta']) * 100, 2),
                    'stock_minimo' => 3,
                ]);
                $this->agregarAtributos($producto, [
                    'CAP' => (string)$datos['cap'],
                    'MAT' => 'Polietileno',
                    'COLOR' => 'Azul',
                    'CERT' => 'Sí',
                    'GARANT' => '48',
                ]);
            }
        }
    }

    private function crearTubos(): void
    {
        $diametros = [
            ['dia' => '1/2"', 'codigo' => 'TUBO-PVC-05', 'compra' => 25000, 'venta' => 38000, 'dext' => '21.3'],
            ['dia' => '3/4"', 'codigo' => 'TUBO-PVC-07', 'compra' => 35000, 'venta' => 52000, 'dext' => '26.7'],
            ['dia' => '1"', 'codigo' => 'TUBO-PVC-10', 'compra' => 48000, 'venta' => 72000, 'dext' => '33.4'],
            ['dia' => '1 1/2"', 'codigo' => 'TUBO-PVC-15', 'compra' => 85000, 'venta' => 125000, 'dext' => '48.3'],
            ['dia' => '2"', 'codigo' => 'TUBO-PVC-20', 'compra' => 120000, 'venta' => 180000, 'dext' => '60.3'],
        ];

        // Tubos Tigre (Brasil)
        foreach ($diametros as $datos) {
            $producto = $this->crearProducto([
                'codigo' => "TUBO-TIG-{$datos['codigo']}-C5",
                'nombre' => "Tubo PVC Tigre {$datos['dia']} Clase 5 x 6m",
                'descripcion' => "Tubo PVC para agua fría {$datos['dia']}, clase 5, longitud 6 metros",
                'marca' => 'TIG',
                'categoria' => $datos['codigo'],
                'unidad' => 'UN',
                'precio_compra' => $datos['compra'],
                'precio_venta' => $datos['venta'],
                'margen' => round((($datos['venta'] - $datos['compra']) / $datos['venta']) * 100, 2),
                'stock_minimo' => 10,
            ]);
            $this->agregarAtributos($producto, [
                'DNOM' => $datos['dia'],
                'DEXT' => $datos['dext'],
                'CLASE' => '5',
                'LONG' => '6',
                'MAT' => 'PVC Rígido',
            ]);
        }

        // Tubos Plastipar (Paraguay) - Más económicos
        foreach ($diametros as $index => $datos) {
            if ($index < 3) { // Solo los 3 primeros diámetros
                $producto = $this->crearProducto([
                    'codigo' => "TUBO-PLT-{$datos['codigo']}-C5",
                    'nombre' => "Tubo PVC Plastipar {$datos['dia']} Clase 5 x 6m",
                    'descripcion' => "Tubo PVC nacional para agua fría {$datos['dia']}, clase 5",
                    'marca' => 'PLT',
                    'categoria' => $datos['codigo'],
                    'unidad' => 'UN',
                    'precio_compra' => $datos['compra'] * 0.85,
                    'precio_venta' => $datos['venta'] * 0.85,
                    'margen' => round((($datos['venta'] - $datos['compra']) / $datos['venta']) * 100, 2),
                    'stock_minimo' => 15,
                ]);
                $this->agregarAtributos($producto, [
                    'DNOM' => $datos['dia'],
                    'DEXT' => $datos['dext'],
                    'CLASE' => '5',
                    'LONG' => '6',
                    'MAT' => 'PVC Rígido',
                ]);
            }
        }
    }

    private function crearConexiones(): void
    {
        $diametros = ['1/2"', '3/4"', '1"', '1 1/2"'];

        // Codos 90°
        foreach ($diametros as $index => $dia) {
            $compra = 800 + ($index * 400);
            $venta = $compra * 1.875;

            $producto = $this->crearProducto([
                'codigo' => "CONE-COD90-TIG-" . str_replace([' ', '"', '/'], '', $dia),
                'nombre' => "Codo PVC 90° Tigre {$dia} Soldable",
                'descripcion' => "Codo PVC 90 grados para soldar, diámetro {$dia}",
                'marca' => 'TIG',
                'categoria' => 'CONE-COD',
                'unidad' => 'UN',
                'precio_compra' => $compra,
                'precio_venta' => $venta,
                'margen' => 46.67,
                'stock_minimo' => 20,
            ]);
            $this->agregarAtributos($producto, [
                'DNOM' => $dia,
                'MAT' => 'PVC',
            ]);
        }

        // Tees
        foreach ($diametros as $index => $dia) {
            $compra = 1200 + ($index * 600);
            $venta = $compra * 1.667;

            $producto = $this->crearProducto([
                'codigo' => "CONE-TEE-TIG-" . str_replace([' ', '"', '/'], '', $dia),
                'nombre' => "Tee PVC Tigre {$dia} Soldable",
                'descripcion' => "Tee PVC para soldar, diámetro {$dia}",
                'marca' => 'TIG',
                'categoria' => 'CONE-TEE',
                'unidad' => 'UN',
                'precio_compra' => $compra,
                'precio_venta' => $venta,
                'margen' => 40.00,
                'stock_minimo' => 15,
            ]);
            $this->agregarAtributos($producto, [
                'DNOM' => $dia,
                'MAT' => 'PVC',
            ]);
        }
    }

    private function crearValvulas(): void
    {
        $diametros = ['1/2"', '3/4"', '1"'];

        // Llaves Esféricas
        foreach ($diametros as $index => $dia) {
            $compra = 35000 + ($index * 15000);
            $venta = $compra * 1.571;

            $producto = $this->crearProducto([
                'codigo' => "VALV-ESF-NIC-" . str_replace([' ', '"', '/'], '', $dia),
                'nombre' => "Llave Esférica Bronce {$dia} Nicoll",
                'descripcion' => "Llave esférica de bronce, diámetro {$dia}",
                'marca' => 'NIC',
                'categoria' => 'VALV-LLA-BOL',
                'unidad' => 'UN',
                'precio_compra' => $compra,
                'precio_venta' => $venta,
                'margen' => 36.36,
                'stock_minimo' => 10,
            ]);
            $this->agregarAtributos($producto, [
                'DNOM' => $dia,
                'TROSCA' => 'NPT',
                'PRESMAX' => '16',
                'MATCUE' => 'Bronce',
                'GARANT' => '12',
            ]);
        }

        // Válvulas Check
        foreach ($diametros as $index => $dia) {
            $compra = 45000 + ($index * 20000);
            $venta = $compra * 1.511;

            $producto = $this->crearProducto([
                'codigo' => "VALV-CHK-NIC-" . str_replace([' ', '"', '/'], '', $dia),
                'nombre' => "Válvula Check {$dia} Bronce",
                'descripcion' => "Válvula anti-retorno de bronce, diámetro {$dia}",
                'marca' => 'NIC',
                'categoria' => 'VALV-CHK',
                'unidad' => 'UN',
                'precio_compra' => $compra,
                'precio_venta' => $venta,
                'margen' => 33.82,
                'stock_minimo' => 8,
            ]);
            $this->agregarAtributos($producto, [
                'DNOM' => $dia,
                'TROSCA' => 'NPT',
                'PRESMAX' => '16',
                'MATCUE' => 'Bronce',
            ]);
        }
    }

    private function crearProducto(array $datos): Producto
    {
        $producto = Producto::create([
            'codigo' => $datos['codigo'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'marca_id' => $this->marcas[$datos['marca']],
            'categoria_id' => $this->categorias[$datos['categoria']],
            'unidad_medida_id' => $this->unidades[$datos['unidad']],
            'stock_minimo' => $datos['stock_minimo'],
            'activo' => true,
            'creadoPor' => null,
        ]);

        // Crear precio actual
        Precio::create([
            'producto_id' => $producto->id,
            'precio_compra' => $datos['precio_compra'],
            'precio_venta' => $datos['precio_venta'],
            'margen_porcentaje' => $datos['margen'],
            'iva' => '10',
            'moneda' => 'PYG',
            'es_actual' => true,
            'creadoPor' => null,
        ]);

        return $producto;
    }

    private function agregarAtributos(Producto $producto, array $atributos): void
    {
        foreach ($atributos as $codigo => $valor) {
            if (isset($this->atributos[$codigo])) {
                DB::table('stock.ATRIBUTOS_PRODUCTO')->insert([
                    'producto_id' => $producto->id,
                    'atributo_tipo_id' => $this->atributos[$codigo],
                    'valor' => $valor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
