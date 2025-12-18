<?php

namespace Database\Seeders\Compras;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdenCompraSeeder extends Seeder
{
    public function run(): void
    {
        // Orden 1: Desde presupuesto PRES-000002 (SELECCIONADO)
        // Primero necesitamos marcar el presupuesto como SELECCIONADO
        DB::table('compras.presupuestos')
            ->where('numero_presupuesto', 'PRES-000002')
            ->update(['estado' => 'SELECCIONADO']);

        $presupuesto2 = DB::table('compras.presupuestos')
            ->where('numero_presupuesto', 'PRES-000002')
            ->first();

        $orden1 = DB::table('compras.ordenes_compra')->insertGetId([
            'numero_orden' => 'OC-000001',
            'fecha_orden' => now()->subDays(1),
            'fecha_entrega_esperada' => now()->addDays(3),
            'proveedor_id' => 2, // ITA Importaciones
            'sucursal_id' => 1,
            'deposito_id' => 1,
            'presupuesto_id' => $presupuesto2->id,
            'condicion_pago' => '60_DIAS',
            'tipo_orden' => 'URGENTE',
            'subtotal' => 80000,
            'total_iva' => 8000,
            'descuento_global' => 5000,
            'flete' => 30000,
            'total' => 113000,
            'estado' => 'EMITIDA',
            'direccion_entrega' => 'Av. Mariscal López 1234, Asunción',
            'contacto_recepcion' => 'Juan Pérez',
            'telefono_recepcion' => '0981-123456',
            'observaciones' => 'Orden generada desde presupuesto seleccionado',
            'creadoPor' => 1,
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);

        // Detalle orden 1
        DB::table('compras.ordenes_compra_detalle')->insert([
            'orden_compra_id' => $orden1,
            'producto_id' => 4,
            'cantidad_ordenada' => 2,
            'cantidad_pendiente' => 2,
            'precio_unitario' => 40000,
            'iva_porcentaje' => 10,
            'subtotal' => 80000,
            'iva_monto' => 8000,
            'total' => 88000,
            'observaciones' => 'Marca: Hydros',
            'estado' => 'PENDIENTE',
            'creadoPor' => 1,
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);

        // Orden 2: Orden manual (sin presupuesto)
        $orden2 = DB::table('compras.ordenes_compra')->insertGetId([
            'numero_orden' => 'OC-000002',
            'fecha_orden' => now()->subDays(5),
            'fecha_entrega_esperada' => now()->addDays(10),
            'proveedor_id' => 3, // Ferretería Central
            'sucursal_id' => 1,
            'deposito_id' => 1,
            'presupuesto_id' => null,
            'condicion_pago' => '30_DIAS',
            'tipo_orden' => 'NORMAL',
            'subtotal' => 450000,
            'total_iva' => 45000,
            'descuento_global' => 0,
            'flete' => 100000,
            'total' => 595000,
            'estado' => 'CONFIRMADA',
            'direccion_entrega' => 'Ruta Transchaco Km 12, Mariano Roque Alonso',
            'contacto_recepcion' => 'María González',
            'telefono_recepcion' => '0982-654321',
            'observaciones' => 'Orden directa, sin presupuesto previo',
            'condiciones_especiales' => 'Entrega en horario de 8:00 a 17:00',
            'creadoPor' => 1,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(3),
        ]);

        // Detalles orden 2
        DB::table('compras.ordenes_compra_detalle')->insert([
            [
                'orden_compra_id' => $orden2,
                'producto_id' => 2,
                'cantidad_ordenada' => 20,
                'cantidad_pendiente' => 20,
                'precio_unitario' => 20000,
                'iva_porcentaje' => 10,
                'subtotal' => 400000,
                'iva_monto' => 40000,
                'total' => 440000,
                'observaciones' => 'Marca: Wavin',
                'estado' => 'PENDIENTE',
                'creadoPor' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'orden_compra_id' => $orden2,
                'producto_id' => 3,
                'cantidad_ordenada' => 10,
                'cantidad_pendiente' => 10,
                'precio_unitario' => 5000,
                'iva_porcentaje' => 10,
                'subtotal' => 50000,
                'iva_monto' => 5000,
                'total' => 55000,
                'observaciones' => 'Marca: Genérico',
                'estado' => 'PENDIENTE',
                'creadoPor' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ]);

        // Orden 3: Borrador
        $orden3 = DB::table('compras.ordenes_compra')->insertGetId([
            'numero_orden' => 'OC-000003',
            'fecha_orden' => now(),
            'fecha_entrega_esperada' => now()->addDays(15),
            'proveedor_id' => 1, // Plomería del Este
            'sucursal_id' => 1,
            'deposito_id' => 1,
            'presupuesto_id' => null,
            'condicion_pago' => '15_DIAS',
            'tipo_orden' => 'NORMAL',
            'subtotal' => 150000,
            'total_iva' => 15000,
            'descuento_global' => 10000,
            'flete' => 50000,
            'total' => 205000,
            'estado' => 'BORRADOR',
            'direccion_entrega' => null,
            'contacto_recepcion' => null,
            'telefono_recepcion' => null,
            'observaciones' => 'Orden en borrador, pendiente de completar datos',
            'creadoPor' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('compras.ordenes_compra_detalle')->insert([
            'orden_compra_id' => $orden3,
            'producto_id' => 1,
            'cantidad_ordenada' => 5,
            'cantidad_pendiente' => 5,
            'precio_unitario' => 30000,
            'iva_porcentaje' => 10,
            'subtotal' => 150000,
            'iva_monto' => 15000,
            'total' => 165000,
            'estado' => 'PENDIENTE',
            'creadoPor' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Orden 4: Recibida
        $orden4 = DB::table('compras.ordenes_compra')->insertGetId([
            'numero_orden' => 'OC-000004',
            'fecha_orden' => now()->subDays(15),
            'fecha_entrega_esperada' => now()->subDays(5),
            'confirmada_en' => now()->subDays(12),
            'enviada_en' => now()->subDays(10),
            'proveedor_id' => 4, // Tigre Paraguay
            'sucursal_id' => 1,
            'deposito_id' => 1,
            'presupuesto_id' => null,
            'condicion_pago' => '30_DIAS',
            'tipo_orden' => 'NORMAL',
            'subtotal' => 300000,
            'total_iva' => 30000,
            'descuento_global' => 0,
            'flete' => 75000,
            'total' => 405000,
            'porcentaje_recibido' => 100,
            'estado' => 'COMPLETAMENTE_RECIBIDA',
            'direccion_entrega' => 'Central de Abasto, Villa Elisa',
            'contacto_recepcion' => 'Carlos Rodríguez',
            'telefono_recepcion' => '0983-789456',
            'observaciones' => 'Orden recibida completa',
            'creadoPor' => 1,
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(3),
        ]);

        DB::table('compras.ordenes_compra_detalle')->insert([
            'orden_compra_id' => $orden4,
            'producto_id' => 2,
            'cantidad_ordenada' => 15,
            'cantidad_recibida' => 15,
            'cantidad_pendiente' => 0,
            'precio_unitario' => 20000,
            'iva_porcentaje' => 10,
            'subtotal' => 300000,
            'iva_monto' => 30000,
            'total' => 330000,
            'observaciones' => 'Marca: Tigre',
            'estado' => 'COMPLETAMENTE_RECIBIDO',
            'creadoPor' => 1,
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(3),
        ]);

        $this->command->info('✓ 4 órdenes de compra creadas exitosamente');
        $this->command->info('  - OC-000001: EMITIDA (desde presupuesto)');
        $this->command->info('  - OC-000002: CONFIRMADA (orden directa)');
        $this->command->info('  - OC-000003: BORRADOR');
        $this->command->info('  - OC-000004: RECIBIDA');
    }
}
