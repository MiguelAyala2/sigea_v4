<?php

namespace Database\Seeders\Compras;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedidoCompraSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar y crear productos básicos si no existen
        $productos = [];

        // Producto 1: Tubo PVC
        $producto1 = DB::table('stock.PRODUCTOS')->where('codigo', 'PRD-000001')->first();
        if (!$producto1) {
            $productos[1] = DB::table('stock.PRODUCTOS')->insertGetId([
                'codigo' => 'PRD-000001',
                'nombre' => 'Tubo PVC 1/2"',
                'descripcion' => 'Tubo PVC para agua fría',
                'marca_id' => 1,
                'categoria_id' => 1,
                'unidad_medida_id' => 1,
                'stock_minimo' => 10,
                'activo' => true,
                'creadoPor' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $productos[1] = $producto1->id;
        }

        // Producto 2: Codo PVC
        $producto2 = DB::table('stock.PRODUCTOS')->where('codigo', 'PRD-000002')->first();
        if (!$producto2) {
            $productos[2] = DB::table('stock.PRODUCTOS')->insertGetId([
                'codigo' => 'PRD-000002',
                'nombre' => 'Codo PVC 90° 1/2"',
                'descripcion' => 'Codo PVC 90 grados',
                'marca_id' => 1,
                'categoria_id' => 1,
                'unidad_medida_id' => 1,
                'stock_minimo' => 10,
                'activo' => true,
                'creadoPor' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $productos[2] = $producto2->id;
        }

        // Producto 3: Tee PVC
        $producto3 = DB::table('stock.PRODUCTOS')->where('codigo', 'PRD-000003')->first();
        if (!$producto3) {
            $productos[3] = DB::table('stock.PRODUCTOS')->insertGetId([
                'codigo' => 'PRD-000003',
                'nombre' => 'Tee PVC 1/2"',
                'descripcion' => 'Conexión tipo T PVC',
                'marca_id' => 1,
                'categoria_id' => 1,
                'unidad_medida_id' => 1,
                'stock_minimo' => 20,
                'activo' => true,
                'creadoPor' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $productos[3] = $producto3->id;
        }

        // Producto 4: Llave de paso
        $producto4 = DB::table('stock.PRODUCTOS')->where('codigo', 'PRD-000004')->first();
        if (!$producto4) {
            $productos[4] = DB::table('stock.PRODUCTOS')->insertGetId([
                'codigo' => 'PRD-000004',
                'nombre' => 'Llave de paso 1/2"',
                'descripcion' => 'Llave de paso metálica',
                'marca_id' => 1,
                'categoria_id' => 1,
                'unidad_medida_id' => 1,
                'stock_minimo' => 15,
                'activo' => true,
                'creadoPor' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $productos[4] = $producto4->id;
        }

        // Pedido 1: Reposición de Stock
        $pedido1 = DB::table('compras.pedidos_compra')->insertGetId([
            'numero_pedido' => 'PC-000001',
            'fecha_pedido' => now()->subDays(5),
            'fecha_necesaria' => now()->addDays(5),
            'sucursal_id' => 1,
            'deposito_destino_id' => 1,
            'usuario_solicitante_id' => 1,
            'tipo_pedido' => 'REPOSICION_STOCK',
            'prioridad' => 'NORMAL',
            'estado' => 'PENDIENTE_APROBACION',
            'total_estimado' => 450000,
            'porcentaje_ordenado' => 0,
            'observaciones' => 'Reposición de stock para sucursal matriz',
            'urgente' => false,
            'activo' => true,
            'creadoPor' => 1,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        // Detalles pedido 1
        DB::table('compras.pedidos_compra_detalle')->insert([
            [
                'pedido_compra_id' => $pedido1,
                'producto_id' => $productos[1],
                'cantidad_solicitada' => 20,
                'cantidad_aprobada' => 0,
                'precio_estimado' => 15000,
                'stock_actual' => 100,
                'stock_minimo' => 10,
                'subtotal_estimado' => 300000,
                'estado' => 'PENDIENTE',
                'creadoPor' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'pedido_compra_id' => $pedido1,
                'producto_id' => $productos[3],
                'cantidad_solicitada' => 30,
                'cantidad_aprobada' => 0,
                'precio_estimado' => 5000,
                'stock_actual' => 150,
                'stock_minimo' => 20,
                'subtotal_estimado' => 150000,
                'estado' => 'PENDIENTE',
                'creadoPor' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ]);

        // Pedido 2: Compra Urgente
        $pedido2 = DB::table('compras.pedidos_compra')->insertGetId([
            'numero_pedido' => 'PC-000002',
            'fecha_pedido' => now()->subDays(3),
            'fecha_necesaria' => now()->addDay(),
            'sucursal_id' => 1,
            'deposito_destino_id' => 1,
            'usuario_solicitante_id' => 1,
            'tipo_pedido' => 'COMPRA_DIRECTA',
            'prioridad' => 'URGENTE',
            'estado' => 'APROBADO',
            'total_estimado' => 90000,
            'porcentaje_ordenado' => 0,
            'observaciones' => 'Pedido urgente para proyecto cliente',
            'urgente' => true,
            'activo' => true,
            'creadoPor' => 1,
            'aprobadoPor' => 1,
            'aprobado_en' => now()->subDays(2),
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(2),
        ]);

        DB::table('compras.pedidos_compra_detalle')->insert([
            [
                'pedido_compra_id' => $pedido2,
                'producto_id' => $productos[4],
                'cantidad_solicitada' => 2,
                'cantidad_aprobada' => 2,
                'precio_estimado' => 45000,
                'stock_actual' => 50,
                'stock_minimo' => 15,
                'subtotal_estimado' => 90000,
                'estado' => 'APROBADO',
                'creadoPor' => 1,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
        ]);

        // Pedido 3: Proyecto Específico
        $pedido3 = DB::table('compras.pedidos_compra')->insertGetId([
            'numero_pedido' => 'PC-000003',
            'fecha_pedido' => now()->subDays(7),
            'fecha_necesaria' => now()->addDays(10),
            'sucursal_id' => 2,
            'deposito_destino_id' => 2,
            'usuario_solicitante_id' => 1,
            'tipo_pedido' => 'PROYECTO_ESPECIFICO',
            'prioridad' => 'NORMAL',
            'estado' => 'APROBADO',
            'total_estimado' => 1050000,
            'porcentaje_ordenado' => 50,
            'observaciones' => 'Materiales para instalación edificio nuevo',
            'urgente' => false,
            'activo' => true,
            'creadoPor' => 1,
            'aprobadoPor' => 1,
            'aprobado_en' => now()->subDays(6),
            'created_at' => now()->subDays(7),
            'updated_at' => now()->subDays(4),
        ]);

        DB::table('compras.pedidos_compra_detalle')->insert([
            [
                'pedido_compra_id' => $pedido3,
                'producto_id' => $productos[1],
                'cantidad_solicitada' => 50,
                'cantidad_aprobada' => 50,
                'cantidad_ordenada' => 50,
                'precio_estimado' => 15000,
                'stock_actual' => 100,
                'stock_minimo' => 10,
                'subtotal_estimado' => 750000,
                'estado' => 'COMPLETAMENTE_ORDENADO',
                'creadoPor' => 1,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(4),
            ],
            [
                'pedido_compra_id' => $pedido3,
                'producto_id' => $productos[2],
                'cantidad_solicitada' => 30,
                'cantidad_aprobada' => 30,
                'cantidad_ordenada' => 0,
                'precio_estimado' => 22000,
                'stock_actual' => 75,
                'stock_minimo' => 10,
                'subtotal_estimado' => 660000,
                'estado' => 'APROBADO',
                'creadoPor' => 1,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
        ]);

        $this->command->info('✓ Pedidos de compra creados exitosamente');
    }
}
