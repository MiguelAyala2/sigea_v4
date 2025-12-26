<?php

namespace Database\Seeders\Stock;

use App\Models\Empresa\Deposito;
use App\Models\Stock\Producto;
use App\Models\Stock\Stock;
use Illuminate\Database\Seeder;

class StockInicialSeeder extends Seeder
{
    public function run(): void
    {
        $productos = Producto::with('marca')->get();
        $depositos = Deposito::all();

        if ($productos->isEmpty() || $depositos->isEmpty()) {
            $this->command->error('× Error: No hay productos o depósitos creados.');
            return;
        }

        $depositoCasaMatriz = $depositos->where('codigo', 'DEP-001')->first();
        $depositoSanLorenzo = $depositos->where('codigo', 'DEP-002')->first();
        $depositoNemby = $depositos->where('codigo', 'DEP-003')->first();

        $contador = 0;

        foreach ($productos as $producto) {
            $categoria = $this->categorizarProducto($producto);

            // Stock según categoría y marca
            $stocks = $this->determinarStock($producto, $categoria);

            // Crear stock SOLO en depósito Casa Matriz (principal)
            if ($stocks['casa_matriz'] > 0) {
                $this->crearStock($producto, $depositoCasaMatriz, $stocks['casa_matriz']);
                $contador++;
            }
        }

        $this->command->info("✓ {$contador} registros de stock inicial creados");
    }

    private function categorizarProducto(Producto $producto): string
    {
        $marca = $producto->marca->nombre ?? '';
        $nombre = strtolower($producto->nombre);

        // Categorizar por marca (premium vs económica)
        $marcasPremium = ['Grundfos', 'Pedrollo', 'Nicoll', 'Wavin', 'Rotoplas'];
        $marcasEconomicas = ['Leo', 'Shimge', 'Dancor', 'Anauger'];
        $marcasNacionales = ['Plastisur', 'Plastipar'];

        if (in_array($marca, $marcasPremium)) {
            return 'premium';
        } elseif (in_array($marca, $marcasEconomicas)) {
            return 'economica';
        } elseif (in_array($marca, $marcasNacionales)) {
            return 'nacional';
        }

        // Categorizar por tipo de producto
        if (str_contains($nombre, 'tubo') || str_contains($nombre, 'codo') || str_contains($nombre, 'tee')) {
            return 'alta_rotacion';
        } elseif (str_contains($nombre, 'motobomba')) {
            return 'motobomba';
        } elseif (str_contains($nombre, 'tanque')) {
            return 'tanque';
        } elseif (str_contains($nombre, 'válvula') || str_contains($nombre, 'llave')) {
            return 'valvula';
        }

        return 'general';
    }

    private function determinarStock(Producto $producto, string $categoria): array
    {
        switch ($categoria) {
            case 'premium':
                // Productos premium: menos stock, más en casa matriz
                return [
                    'casa_matriz' => rand(4, 6),
                    'san_lorenzo' => rand(2, 3),
                    'nemby' => rand(1, 2),
                ];

            case 'economica':
                // Productos económicos: más stock
                return [
                    'casa_matriz' => rand(6, 10),
                    'san_lorenzo' => rand(4, 6),
                    'nemby' => rand(3, 5),
                ];

            case 'nacional':
                // Productos nacionales: buen stock
                return [
                    'casa_matriz' => rand(8, 12),
                    'san_lorenzo' => rand(5, 7),
                    'nemby' => rand(4, 6),
                ];

            case 'alta_rotacion':
                // Tubos y conexiones: mucho stock
                return [
                    'casa_matriz' => rand(30, 50),
                    'san_lorenzo' => rand(15, 25),
                    'nemby' => rand(10, 20),
                ];

            case 'motobomba':
                // Motobombas: stock moderado
                return [
                    'casa_matriz' => rand(3, 5),
                    'san_lorenzo' => rand(2, 3),
                    'nemby' => rand(1, 2),
                ];

            case 'tanque':
                // Tanques: stock moderado
                return [
                    'casa_matriz' => rand(5, 8),
                    'san_lorenzo' => rand(3, 5),
                    'nemby' => rand(2, 4),
                ];

            case 'valvula':
                // Válvulas: buen stock
                return [
                    'casa_matriz' => rand(12, 18),
                    'san_lorenzo' => rand(8, 12),
                    'nemby' => rand(6, 10),
                ];

            default:
                return [
                    'casa_matriz' => rand(5, 10),
                    'san_lorenzo' => rand(3, 6),
                    'nemby' => rand(2, 4),
                ];
        }
    }

    private function crearStock(Producto $producto, Deposito $deposito, int $cantidad): void
    {
        Stock::create([
            'producto_id' => $producto->id,
            'deposito_id' => $deposito->id,
            'stock_actual' => $cantidad,
            'creadoPor' => null,
        ]);
    }
}