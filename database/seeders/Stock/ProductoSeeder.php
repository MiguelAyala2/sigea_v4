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

        $this->crearMotobombas(); // 5 productos
        $this->crearTanques(); // 5 productos
        $this->crearTubos(); // 10 productos (caños varios)
        $this->crearRepuestos(); // 5 productos de 10 unidades

        $this->command->info('✓ 25 productos creados con atributos y precios');
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
        // 1. Motobomba Grundfos JP 5 - 0.5 HP
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

        // 2. Motobomba Pedrollo PKm 60 - 0.5 HP
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

        // 3. Motobomba Leo XKm 60 - 0.5 HP
        $producto = $this->crearProducto([
            'codigo' => 'MOTO-LEO-XKM60-05',
            'nombre' => 'Motobomba Periférica Leo XKm 60 - 0.5 HP',
            'descripcion' => 'Motobomba periférica línea económica',
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

        // 4. Motobomba Dancor CP-4A - 1 HP Monofásica
        $producto = $this->crearProducto([
            'codigo' => 'MOTO-DAN-CP4A-10',
            'nombre' => 'Motobomba Centrífuga Dancor CP-4A - 1 HP',
            'descripcion' => 'Motobomba centrífuga para uso residencial',
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

        // 5. Motobomba Shimge Sumergible 4" - 1.5 HP
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
        // 1. Tanque Rotoplas 500 L
        $producto = $this->crearProducto([
            'codigo' => 'TANQ-ROT-500-NEG',
            'nombre' => 'Tanque Rotoplas 500 L Negro',
            'descripcion' => 'Tanque de agua de polietileno Rotoplas, capacidad 500 litros',
            'marca' => 'ROT',
            'categoria' => 'TANQ-POL-500',
            'unidad' => 'UN',
            'precio_compra' => 450000,
            'precio_venta' => 650000,
            'margen' => 30.77,
            'stock_minimo' => 2,
        ]);
        $this->agregarAtributos($producto, [
            'CAP' => '500',
            'MAT' => 'Polietileno',
            'COLOR' => 'Negro',
            'CERT' => 'Sí',
            'GARANT' => '60',
        ]);

        // 2. Tanque Rotoplas 1000 L
        $producto = $this->crearProducto([
            'codigo' => 'TANQ-ROT-1000-NEG',
            'nombre' => 'Tanque Rotoplas 1000 L Negro',
            'descripcion' => 'Tanque de agua de polietileno Rotoplas, capacidad 1000 litros',
            'marca' => 'ROT',
            'categoria' => 'TANQ-POL-1000',
            'unidad' => 'UN',
            'precio_compra' => 750000,
            'precio_venta' => 1080000,
            'margen' => 30.56,
            'stock_minimo' => 2,
        ]);
        $this->agregarAtributos($producto, [
            'CAP' => '1000',
            'MAT' => 'Polietileno',
            'COLOR' => 'Negro',
            'CERT' => 'Sí',
            'GARANT' => '60',
        ]);

        // 3. Tanque Rotoplas 1500 L
        $producto = $this->crearProducto([
            'codigo' => 'TANQ-ROT-1500-NEG',
            'nombre' => 'Tanque Rotoplas 1500 L Negro',
            'descripcion' => 'Tanque de agua de polietileno Rotoplas, capacidad 1500 litros',
            'marca' => 'ROT',
            'categoria' => 'TANQ-POL-2000',
            'unidad' => 'UN',
            'precio_compra' => 1050000,
            'precio_venta' => 1520000,
            'margen' => 30.92,
            'stock_minimo' => 1,
        ]);
        $this->agregarAtributos($producto, [
            'CAP' => '1500',
            'MAT' => 'Polietileno',
            'COLOR' => 'Negro',
            'CERT' => 'Sí',
            'GARANT' => '60',
        ]);

        // 4. Tanque Plastisur 600 L
        $producto = $this->crearProducto([
            'codigo' => 'TANQ-PLS-600-AZU',
            'nombre' => 'Tanque Plastisur 600 L Azul',
            'descripcion' => 'Tanque de agua polietileno fabricación paraguaya, 600 litros',
            'marca' => 'PLS',
            'categoria' => 'TANQ-POL-500',
            'unidad' => 'UN',
            'precio_compra' => 494000,
            'precio_venta' => 712500,
            'margen' => 30.68,
            'stock_minimo' => 3,
        ]);
        $this->agregarAtributos($producto, [
            'CAP' => '600',
            'MAT' => 'Polietileno',
            'COLOR' => 'Azul',
            'CERT' => 'Sí',
            'GARANT' => '48',
        ]);

        // 5. Tanque Plastisur 1100 L
        $producto = $this->crearProducto([
            'codigo' => 'TANQ-PLS-1100-AZU',
            'nombre' => 'Tanque Plastisur 1100 L Azul',
            'descripcion' => 'Tanque de agua polietileno fabricación paraguaya, 1100 litros',
            'marca' => 'PLS',
            'categoria' => 'TANQ-POL-2000',
            'unidad' => 'UN',
            'precio_compra' => 779000,
            'precio_venta' => 1121000,
            'margen' => 30.51,
            'stock_minimo' => 2,
        ]);
        $this->agregarAtributos($producto, [
            'CAP' => '1100',
            'MAT' => 'Polietileno',
            'COLOR' => 'Azul',
            'CERT' => 'Sí',
            'GARANT' => '48',
        ]);
    }

    private function crearTubos(): void
    {
        // Tubos PVC Tigre (Brasil) - 5 productos
        $tubos = [
            ['dia' => '1/2"', 'codigo' => 'PVC-05', 'compra' => 25000, 'venta' => 38000],
            ['dia' => '3/4"', 'codigo' => 'PVC-07', 'compra' => 35000, 'venta' => 52000],
            ['dia' => '1"', 'codigo' => 'PVC-10', 'compra' => 48000, 'venta' => 72000],
            ['dia' => '1 1/2"', 'codigo' => 'PVC-15', 'compra' => 85000, 'venta' => 125000],
            ['dia' => '2"', 'codigo' => 'PVC-20', 'compra' => 120000, 'venta' => 180000],
        ];

        foreach ($tubos as $index => $tubo) {
            $producto = $this->crearProducto([
                'codigo' => "TIG-{$tubo['codigo']}-C5",
                'nombre' => "Tubo PVC Tigre {$tubo['dia']} Clase 5 x 6m",
                'descripcion' => "Tubo PVC para agua fría {$tubo['dia']}, clase 5, longitud 6 metros",
                'marca' => 'TIG',
                'categoria' => "TUBO-{$tubo['codigo']}",
                'unidad' => 'UN',
                'precio_compra' => $tubo['compra'],
                'precio_venta' => $tubo['venta'],
                'margen' => round((($tubo['venta'] - $tubo['compra']) / $tubo['venta']) * 100, 2),
                'stock_minimo' => 10,
            ]);
        }

        // Conexiones PVC - 5 productos
        $conexiones = [
            ['tipo' => 'Codo 90°', 'dia' => '1/2"', 'compra' => 800, 'venta' => 1500],
            ['tipo' => 'Codo 90°', 'dia' => '3/4"', 'compra' => 1200, 'venta' => 2250],
            ['tipo' => 'Tee', 'dia' => '1/2"', 'compra' => 1200, 'venta' => 2000],
            ['tipo' => 'Tee', 'dia' => '3/4"', 'compra' => 1800, 'venta' => 3000],
            ['tipo' => 'Llave Esférica', 'dia' => '1/2"', 'compra' => 35000, 'venta' => 55000],
        ];

        foreach ($conexiones as $index => $conn) {
            $codigoTipo = str_replace(['Codo 90°', 'Tee', 'Llave Esférica'], ['COD90', 'TEE', 'LLAVE'], $conn['tipo']);
            $codigoDia = str_replace([' ', '"', '/'], '', $conn['dia']);

            $producto = $this->crearProducto([
                'codigo' => "CONE-{$codigoTipo}-{$codigoDia}",
                'nombre' => "{$conn['tipo']} PVC {$conn['dia']}",
                'descripcion' => "{$conn['tipo']} de PVC, diámetro {$conn['dia']}",
                'marca' => 'TIG',
                'categoria' => 'CONE-COD',
                'unidad' => 'UN',
                'precio_compra' => $conn['compra'],
                'precio_venta' => $conn['venta'],
                'margen' => round((($conn['venta'] - $conn['compra']) / $conn['venta']) * 100, 2),
                'stock_minimo' => 20,
            ]);
        }
    }

    private function crearRepuestos(): void
    {
        // 1. Junta para Motor - Grundfos
        $producto = $this->crearProducto([
            'codigo' => 'REP-JUNTA-MTR',
            'nombre' => 'Junta para Motor de Bomba',
            'descripcion' => 'Junta de goma para sello de motor de motobomba',
            'marca' => 'GRU',
            'categoria' => 'MOTB-PER-05', // Usar categoría de motobombas
            'unidad' => 'UN',
            'precio_compra' => 12000,
            'precio_venta' => 18000,
            'margen' => 33.33,
            'stock_minimo' => 10,
        ]);

        // 2. Sello Mecánico - Pedrollo
        $producto = $this->crearProducto([
            'codigo' => 'REP-SELLO-MEC',
            'nombre' => 'Sello Mecánico para Bomba',
            'descripcion' => 'Sello mecánico universal para motobombas 0.5-1 HP',
            'marca' => 'PED',
            'categoria' => 'MOTB-PER-05', // Usar categoría de motobombas
            'unidad' => 'UN',
            'precio_compra' => 45000,
            'precio_venta' => 68000,
            'margen' => 33.82,
            'stock_minimo' => 10,
        ]);

        // 3. Rodamiento - Leo
        $producto = $this->crearProducto([
            'codigo' => 'REP-ROD-6201',
            'nombre' => 'Rodamiento 6201 para Motor',
            'descripcion' => 'Rodamiento tipo 6201 para eje de motor',
            'marca' => 'LEO',
            'categoria' => 'MOTB-CEN-MONO', // Usar categoría de motobombas
            'unidad' => 'UN',
            'precio_compra' => 25000,
            'precio_venta' => 38000,
            'margen' => 34.21,
            'stock_minimo' => 10,
        ]);

        // 4. Capacitor de Arranque - Dancor
        $producto = $this->crearProducto([
            'codigo' => 'REP-CAP-50UF',
            'nombre' => 'Capacitor de Arranque 50uF',
            'descripcion' => 'Capacitor de arranque 50uF 220V para motobomba',
            'marca' => 'DAN',
            'categoria' => 'MOTB-CEN-MONO', // Usar categoría de motobombas
            'unidad' => 'UN',
            'precio_compra' => 35000,
            'precio_venta' => 52000,
            'margen' => 32.69,
            'stock_minimo' => 10,
        ]);

        // 5. Impulsor de Bomba - Shimge
        $producto = $this->crearProducto([
            'codigo' => 'REP-IMP-PER',
            'nombre' => 'Impulsor para Bomba Periférica',
            'descripcion' => 'Impulsor de bronce para bomba periférica 0.5 HP',
            'marca' => 'SHI',
            'categoria' => 'MOTB-SUM-POZ', // Usar categoría de motobombas
            'unidad' => 'UN',
            'precio_compra' => 85000,
            'precio_venta' => 125000,
            'margen' => 32.00,
            'stock_minimo' => 10,
        ]);
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
