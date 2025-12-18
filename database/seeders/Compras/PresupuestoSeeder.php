<?php

namespace Database\Seeders\Compras;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresupuestoSeeder extends Seeder
{
    public function run(): void
    {
        // Presupuesto 1: Para PC-000002 (Compra Urgente) - Proveedor: Plomería del Este
        $presupuesto1 = DB::table('compras.presupuestos')->insertGetId([
            'pedido_compra_id' => 2, // PC-000002
            'proveedor_id' => 1, // Plomería del Este
            'numero_presupuesto' => 'PRES-000001',
            'fecha_solicitud' => now()->subDays(2),
            'fecha_recepcion' => now()->subDay(),
            'fecha_vencimiento' => now()->addDays(15),
            'condicion_pago' => '30_DIAS',
            'dias_entrega' => 5,
            'descuento_general' => 0,
            'flete' => 50000,
            'subtotal' => 90000,
            'iva_10' => 9000,
            'iva_5' => 0,
            'exenta' => 0,
            'total_iva' => 9000,
            'total' => 149000,
            'estado' => 'RECIBIDO',
            'observaciones' => 'Presupuesto para pedido urgente, entrega inmediata',
            'activo' => true,
            'solicitadoPor' => 1,
            'creadoPor' => 1,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDay(),
        ]);

        DB::table('compras.presupuestos_detalle')->insert([
            'presupuesto_id' => $presupuesto1,
            'producto_id' => 4,
            'pedido_compra_detalle_id' => 3, // del PC-000002
            'cantidad_cotizada' => 2,
            'precio_unitario' => 45000,
            'subtotal' => 90000,
            'iva_porcentaje' => 10,
            'iva_monto' => 9000,
            'total' => 99000,
            'dias_entrega_item' => 5,
            'marca_ofrecida' => 'Tigre',
            'creadoPor' => 1,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        // Presupuesto 2: Para PC-000002 - Proveedor: ITA Importaciones (mejor precio)
        $presupuesto2 = DB::table('compras.presupuestos')->insertGetId([
            'pedido_compra_id' => 2,
            'proveedor_id' => 2, // ITA Importaciones
            'numero_presupuesto' => 'PRES-000002',
            'fecha_solicitud' => now()->subDays(2),
            'fecha_recepcion' => now()->subDay(),
            'fecha_vencimiento' => now()->addDays(20),
            'condicion_pago' => '60_DIAS',
            'dias_entrega' => 3,
            'descuento_general' => 5000,
            'flete' => 30000,
            'subtotal' => 80000,
            'iva_10' => 8000,
            'iva_5' => 0,
            'exenta' => 0,
            'total_iva' => 8000,
            'total' => 113000,
            'estado' => 'RECIBIDO',
            'observaciones' => 'Mejor precio, entrega más rápida',
            'activo' => true,
            'es_mejor_precio' => true,
            'es_mejor_plazo' => true,
            'solicitadoPor' => 1,
            'creadoPor' => 1,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDay(),
        ]);

        DB::table('compras.presupuestos_detalle')->insert([
            'presupuesto_id' => $presupuesto2,
            'producto_id' => 4,
            'pedido_compra_detalle_id' => 3,
            'cantidad_cotizada' => 2,
            'precio_unitario' => 40000,
            'subtotal' => 80000,
            'iva_porcentaje' => 10,
            'iva_monto' => 8000,
            'total' => 88000,
            'dias_entrega_item' => 3,
            'marca_ofrecida' => 'Hydros',
            'creadoPor' => 1,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        // Presupuesto 3: Para PC-000004 (Mantenimiento) - Proveedor: Ferretería Central
        $presupuesto3 = DB::table('compras.presupuestos')->insertGetId([
            'pedido_compra_id' => 4,
            'proveedor_id' => 3, // Ferretería Central
            'numero_presupuesto' => 'PRES-000003',
            'fecha_solicitud' => now()->subDays(7),
            'fecha_recepcion' => now()->subDays(5),
            'fecha_vencimiento' => now()->addDays(10),
            'condicion_pago' => '15_DIAS',
            'dias_entrega' => 7,
            'descuento_general' => 0,
            'flete' => 75000,
            'subtotal' => 550000,
            'iva_10' => 55000,
            'iva_5' => 0,
            'exenta' => 0,
            'total_iva' => 55000,
            'total' => 680000,
            'estado' => 'EN_EVALUACION',
            'observaciones' => 'Presupuesto para reparación instalaciones Ñemby',
            'activo' => true,
            'solicitadoPor' => 1,
            'creadoPor' => 1,
            'created_at' => now()->subDays(7),
            'updated_at' => now()->subDays(5),
        ]);

        // Detalles presupuesto 3
        DB::table('compras.presupuestos_detalle')->insert([
            [
                'presupuesto_id' => $presupuesto3,
                'producto_id' => 2,
                'pedido_compra_detalle_id' => 4, // del PC-000004
                'cantidad_cotizada' => 15,
                'precio_unitario' => 22000,
                'subtotal' => 330000,
                'iva_porcentaje' => 10,
                'iva_monto' => 33000,
                'total' => 363000,
                'dias_entrega_item' => 7,
                'marca_ofrecida' => 'Wavin',
                'creadoPor' => 1,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'presupuesto_id' => $presupuesto3,
                'producto_id' => 3,
                'pedido_compra_detalle_id' => 5,
                'cantidad_cotizada' => 44,
                'precio_unitario' => 5000,
                'subtotal' => 220000,
                'iva_porcentaje' => 10,
                'iva_monto' => 22000,
                'total' => 242000,
                'dias_entrega_item' => 5,
                'marca_ofrecida' => 'Genérico',
                'creadoPor' => 1,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
        ]);

        // Presupuesto 4: Presupuesto pendiente - Proveedor: Tigre Paraguay
        $presupuesto4 = DB::table('compras.presupuestos')->insertGetId([
            'pedido_compra_id' => 4,
            'proveedor_id' => 4, // Tigre Paraguay
            'numero_presupuesto' => 'PRES-000004',
            'fecha_solicitud' => now()->subDays(6),
            'fecha_recepcion' => null,
            'fecha_vencimiento' => now()->addDays(14),
            'condicion_pago' => '30_DIAS',
            'dias_entrega' => 10,
            'descuento_general' => 0,
            'flete' => 0,
            'subtotal' => 0,
            'iva_10' => 0,
            'iva_5' => 0,
            'exenta' => 0,
            'total_iva' => 0,
            'total' => 0,
            'estado' => 'PENDIENTE',
            'observaciones' => 'Esperando respuesta del proveedor',
            'activo' => true,
            'solicitadoPor' => 1,
            'creadoPor' => 1,
            'created_at' => now()->subDays(6),
            'updated_at' => now()->subDays(6),
        ]);

        $this->command->info('✓ 4 presupuestos creados exitosamente');
    }
}
