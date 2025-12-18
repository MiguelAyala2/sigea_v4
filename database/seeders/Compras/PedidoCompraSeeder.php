<?php

namespace Database\Seeders\Compras;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedidoCompraSeeder extends Seeder
{
    public function run(): void
    {
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
                'producto_id' => 1,
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
                'producto_id' => 3,
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
                'producto_id' => 4,
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
                'producto_id' => 1,
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
                'producto_id' => 2,
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

        // Pedido 4: Mantenimiento
        $pedido4 = DB::table('compras.pedidos_compra')->insertGetId([
            'numero_pedido' => 'PC-000004',
            'fecha_pedido' => now()->subDays(10),
            'fecha_necesaria' => now()->addDays(15),
            'sucursal_id' => 3,
            'deposito_destino_id' => 3,
            'usuario_solicitante_id' => 1,
            'tipo_pedido' => 'MANTENIMIENTO',
            'prioridad' => 'CRITICA',
            'estado' => 'EN_COTIZACION',
            'total_estimado' => 550000,
            'porcentaje_ordenado' => 0,
            'observaciones' => 'Reparación de instalaciones sanitarias sucursal Ñemby',
            'urgente' => true,
            'activo' => true,
            'creadoPor' => 1,
            'aprobadoPor' => 1,
            'aprobado_en' => now()->subDays(9),
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(8),
        ]);

        DB::table('compras.pedidos_compra_detalle')->insert([
            [
                'pedido_compra_id' => $pedido4,
                'producto_id' => 2,
                'cantidad_solicitada' => 15,
                'cantidad_aprobada' => 15,
                'precio_estimado' => 22000,
                'stock_actual' => 75,
                'stock_minimo' => 10,
                'subtotal_estimado' => 330000,
                'estado' => 'APROBADO',
                'creadoPor' => 1,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'pedido_compra_id' => $pedido4,
                'producto_id' => 3,
                'cantidad_solicitada' => 44,
                'cantidad_aprobada' => 44,
                'precio_estimado' => 5000,
                'stock_actual' => 150,
                'stock_minimo' => 20,
                'subtotal_estimado' => 220000,
                'estado' => 'APROBADO',
                'creadoPor' => 1,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
        ]);

        // Pedido 5: Insumos
        $pedido5 = DB::table('compras.pedidos_compra')->insertGetId([
            'numero_pedido' => 'PC-000005',
            'fecha_pedido' => now()->subDay(),
            'fecha_necesaria' => now()->addDays(20),
            'sucursal_id' => 1,
            'deposito_destino_id' => 1,
            'usuario_solicitante_id' => 1,
            'tipo_pedido' => 'INSUMOS',
            'prioridad' => 'NORMAL',
            'estado' => 'BORRADOR',
            'total_estimado' => 705000,
            'porcentaje_ordenado' => 0,
            'observaciones' => 'Insumos generales para todas las sucursales',
            'urgente' => false,
            'activo' => true,
            'creadoPor' => 1,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        DB::table('compras.pedidos_compra_detalle')->insert([
            [
                'pedido_compra_id' => $pedido5,
                'producto_id' => 1,
                'cantidad_solicitada' => 15,
                'cantidad_aprobada' => 0,
                'precio_estimado' => 15000,
                'stock_actual' => 100,
                'stock_minimo' => 10,
                'subtotal_estimado' => 225000,
                'estado' => 'PENDIENTE',
                'creadoPor' => 1,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'pedido_compra_id' => $pedido5,
                'producto_id' => 3,
                'cantidad_solicitada' => 60,
                'cantidad_aprobada' => 0,
                'precio_estimado' => 5000,
                'stock_actual' => 150,
                'stock_minimo' => 20,
                'subtotal_estimado' => 300000,
                'estado' => 'PENDIENTE',
                'creadoPor' => 1,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'pedido_compra_id' => $pedido5,
                'producto_id' => 4,
                'cantidad_solicitada' => 4,
                'cantidad_aprobada' => 0,
                'precio_estimado' => 45000,
                'stock_actual' => 50,
                'stock_minimo' => 15,
                'subtotal_estimado' => 180000,
                'estado' => 'PENDIENTE',
                'creadoPor' => 1,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ]);

        $this->command->info('✓ 5 pedidos de compra creados con sus detalles exitosamente');
    }
}
