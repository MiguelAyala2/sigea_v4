<?php

namespace Database\Seeders;

use App\Models\Compras\Compra;
use App\Models\Compras\CompraDetalle;
use App\Models\Compras\OrdenCompra;
use App\Models\Compras\OrdenCompraDetalle;
use App\Models\Compras\PedidoCompra;
use App\Models\Compras\PedidoCompraDetalle;
use App\Models\Compras\Presupuesto;
use App\Models\Compras\PresupuestoDetalle;
use App\Models\Stock\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompraRepuestosMotobombasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 1. Obtener todos los repuestos de motobombas creados
            $repuestos = Producto::where(function ($query) {
                $query->where('modelo', 'LIKE', 'IMP-%')
                    ->orWhere('modelo', 'LIKE', 'CAM-%')
                    ->orWhere('modelo', 'LIKE', 'SEL-%')
                    ->orWhere('modelo', 'LIKE', 'PACK-%')
                    ->orWhere('modelo', 'LIKE', 'RET-%')
                    ->orWhere('modelo', 'LIKE', 'JUN-%')
                    ->orWhere('modelo', 'LIKE', 'VAL-%')
                    ->orWhere('modelo', 'LIKE', 'FILT-%')
                    ->orWhere('modelo', 'LIKE', 'REJ-%')
                    ->orWhere('modelo', 'LIKE', 'ROD-%')
                    ->orWhere('modelo', 'LIKE', 'EJE-%')
                    ->orWhere('modelo', 'LIKE', 'ACOP-%')
                    ->orWhere('modelo', 'LIKE', 'CHAV-%')
                    ->orWhere('modelo', 'LIKE', 'TORN-%')
                    ->orWhere('modelo', 'LIKE', 'ABRAZ-%')
                    ->orWhere('modelo', 'LIKE', 'MANG-%')
                    ->orWhere('modelo', 'LIKE', 'BORN-%')
                    ->orWhere('modelo', 'LIKE', 'CAB-%')
                    ->orWhere('modelo', 'LIKE', 'CONT-%')
                    ->orWhere('modelo', 'LIKE', 'FUS-%')
                    ->orWhere('modelo', 'LIKE', 'TERM-%')
                    ->orWhere('modelo', 'LIKE', 'CAP-%')
                    ->orWhere('modelo', 'LIKE', 'BAT-%')
                    ->orWhere('modelo', 'LIKE', 'BUJ-%')
                    ->orWhere('modelo', 'LIKE', 'PROT-%');
            })->with('precioActual')->get();

            if ($repuestos->isEmpty()) {
                echo "No se encontraron repuestos de motobombas. Ejecute primero RepuestosMotobombasSeeder.\n";
                DB::rollBack();
                return;
            }

            $proveedorId = 7; // Distrimotor
            $usuarioId = 1;
            $sucursalId = 1;
            $depositoId = 1;

            // 2. CREAR PEDIDO DE COMPRA
            echo "Creando Pedido de Compra...\n";

            $pedido = PedidoCompra::create([
                'numero_pedido' => 'PC-' . str_pad(PedidoCompra::count() + 1, 6, '0', STR_PAD_LEFT),
                'fecha_pedido' => now(),
                'fecha_necesaria' => now()->addDays(15),
                'sucursal_id' => $sucursalId,
                'deposito_destino_id' => $depositoId,
                'usuario_solicitante_id' => $usuarioId,
                'tipo_pedido' => 'REPOSICION_STOCK',
                'prioridad' => 'URGENTE',
                'estado' => 'APROBADO',
                'total_estimado' => 0,
                'porcentaje_ordenado' => 100,
                'justificacion' => 'COMPRA DE REPUESTOS PARA MOTOBOMBAS - STOCK INICIAL',
                'observaciones' => 'PEDIDO COMPLETO DE REPUESTOS COMUNES PARA MOTOBOMBAS',
                'requiere_aprobacion' => false,
                'urgente' => false,
                'activo' => true,
                'creadoPor' => $usuarioId,
                'aprobadoPor' => $usuarioId,
                'aprobado_en' => now(),
            ]);

            $totalEstimado = 0;

            // Crear detalles del pedido
            foreach ($repuestos as $repuesto) {
                $precioUnitario = $repuesto->precioActual->precio_compra ?? 0;
                $cantidad = 10; // Cantidad inicial por repuesto
                $subtotal = $cantidad * $precioUnitario;
                $totalEstimado += $subtotal;

                PedidoCompraDetalle::create([
                    'pedido_compra_id' => $pedido->id,
                    'producto_id' => $repuesto->id,
                    'cantidad_solicitada' => $cantidad,
                    'cantidad_aprobada' => $cantidad,
                    'cantidad_ordenada' => $cantidad,
                    'precio_estimado' => $precioUnitario,
                    'subtotal_estimado' => $subtotal,
                    'estado' => 'APROBADO',
                    'observaciones' => 'STOCK INICIAL',
                    'creadoPor' => $usuarioId,
                ]);
            }

            $pedido->update(['total_estimado' => $totalEstimado]);

            // 3. CREAR PRESUPUESTO
            echo "Creando Presupuesto...\n";

            $presupuesto = Presupuesto::create([
                'pedido_compra_id' => $pedido->id,
                'proveedor_id' => $proveedorId,
                'numero_presupuesto' => 'PRE-' . str_pad(Presupuesto::count() + 1, 6, '0', STR_PAD_LEFT),
                'fecha_solicitud' => now(),
                'fecha_recepcion' => now(),
                'fecha_vencimiento' => now()->addDays(30),
                'condicion_pago' => '30_DIAS',
                'dias_entrega' => 15,
                'descuento_general' => 0,
                'flete' => 0,
                'subtotal' => 0,
                'iva_10' => 0,
                'iva_5' => 0,
                'exenta' => 0,
                'total_iva' => 0,
                'total' => 0,
                'estado' => 'APROBADO',
                'puntuacion' => 100,
                'observaciones_evaluacion' => 'PRESUPUESTO APROBADO - MEJOR PRECIO Y PLAZO',
                'observaciones' => 'PRESUPUESTO PARA COMPRA DE REPUESTOS DE MOTOBOMBAS',
                'activo' => true,
                'es_mejor_precio' => true,
                'es_mejor_plazo' => true,
                'solicitadoPor' => $usuarioId,
                'evaluadoPor' => $usuarioId,
                'creadoPor' => $usuarioId,
            ]);

            $subtotalPresupuesto = 0;
            $iva10Total = 0;

            // Crear detalles del presupuesto
            foreach ($repuestos as $repuesto) {
                $precioUnitario = $repuesto->precioActual->precio_compra ?? 0;
                $cantidad = 10;
                $subtotal = $cantidad * $precioUnitario;
                $ivaPorcentaje = 10;
                $ivaMonto = $subtotal * 0.10;

                $subtotalPresupuesto += $subtotal;
                $iva10Total += $ivaMonto;

                PresupuestoDetalle::create([
                    'presupuesto_id' => $presupuesto->id,
                    'producto_id' => $repuesto->id,
                    'cantidad_cotizada' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                    'iva_porcentaje' => $ivaPorcentaje,
                    'iva_monto' => $ivaMonto,
                    'total' => $subtotal + $ivaMonto,
                    'observaciones_proveedor' => 'REPUESTO PARA MOTOBOMBA',
                    'creadoPor' => $usuarioId,
                ]);
            }

            $presupuesto->update([
                'subtotal' => $subtotalPresupuesto,
                'iva_10' => $iva10Total,
                'total_iva' => $iva10Total,
                'total' => $subtotalPresupuesto + $iva10Total,
            ]);

            // 4. CREAR ORDEN DE COMPRA
            echo "Creando Orden de Compra...\n";

            $ordenCompra = OrdenCompra::create([
                'numero_orden' => 'OC-' . str_pad(OrdenCompra::count() + 1, 6, '0', STR_PAD_LEFT),
                'fecha_orden' => now(),
                'fecha_entrega_esperada' => now()->addDays(15),
                'proveedor_id' => $proveedorId,
                'sucursal_id' => $sucursalId,
                'deposito_id' => $depositoId,
                'presupuesto_id' => $presupuesto->id,
                'pedido_compra_id' => $pedido->id,
                'condicion_pago' => '30_DIAS',
                'tipo_orden' => 'NORMAL',
                'subtotal' => 0,
                'iva_10' => 0,
                'iva_5' => 0,
                'exenta' => 0,
                'total_iva' => 0,
                'descuento_global' => 0,
                'flete' => 0,
                'total' => 0,
                'estado' => 'APROBADO',
                'porcentaje_recibido' => 100,
                'direccion_entrega' => 'DEPOSITO CENTRAL',
                'contacto_recepcion' => 'ADMINISTRADOR',
                'telefono_recepcion' => '0981-123456',
                'observaciones' => 'ORDEN DE COMPRA PARA REPUESTOS DE MOTOBOMBAS',
                'condiciones_especiales' => 'ENTREGA EN DEPOSITO CENTRAL - HORARIO 08:00 A 17:00',
                'activo' => true,
                'requiere_confirmacion' => false,
                'confirmada_en' => now(),
                'enviada_en' => now(),
                'creadoPor' => $usuarioId,
                'aprobadoPor' => $usuarioId,
                'confirmadaPor' => $usuarioId,
            ]);

            $subtotalOrden = 0;
            $iva10Orden = 0;

            // Crear detalles de la orden de compra
            foreach ($repuestos as $repuesto) {
                $precioUnitario = $repuesto->precioActual->precio_compra ?? 0;
                $cantidad = 10;
                $subtotal = $cantidad * $precioUnitario;
                $ivaPorcentaje = 10;
                $ivaMonto = $subtotal * 0.10;

                $subtotalOrden += $subtotal;
                $iva10Orden += $ivaMonto;

                OrdenCompraDetalle::create([
                    'orden_compra_id' => $ordenCompra->id,
                    'producto_id' => $repuesto->id,
                    'cantidad_ordenada' => $cantidad,
                    'cantidad_recibida' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                    'iva_porcentaje' => $ivaPorcentaje,
                    'iva_monto' => $ivaMonto,
                    'total' => $subtotal + $ivaMonto,
                    'estado' => 'APROBADO',
                    'observaciones' => 'REPUESTO RECIBIDO',
                    'creadoPor' => $usuarioId,
                ]);
            }

            $ordenCompra->update([
                'subtotal' => $subtotalOrden,
                'iva_10' => $iva10Orden,
                'total_iva' => $iva10Orden,
                'total' => $subtotalOrden + $iva10Orden,
            ]);

            // 5. CREAR FACTURA DE COMPRA
            echo "Creando Factura de Compra...\n";

            $compra = Compra::create([
                'proveedor_id' => $proveedorId,
                'sucursal_id' => $sucursalId,
                'deposito_id' => $depositoId,
                'timbrado_id' => null,
                'orden_compra_id' => $ordenCompra->id,
                'numero_factura' => '001-001-0000123',
                'timbrado' => '12345678',
                'fecha_emision' => now(),
                'fecha_vencimiento' => now()->addDays(30),
                'condicion_pago' => '30_DIAS',
                'tipo_factura' => 'CREDITO',
                'tipo_documento' => 'FACTURA',
                'subtotal' => 0,
                'iva_10' => 0,
                'iva_5' => 0,
                'exenta' => 0,
                'total_iva' => 0,
                'total' => 0,
                'estado' => 'APROBADO',
                'observaciones' => 'FACTURA DE COMPRA DE REPUESTOS PARA MOTOBOMBAS',
                'es_electronica' => false,
                'cdc' => null,
                'activo' => true,
                'creadoPor' => $usuarioId,
            ]);

            $subtotalCompra = 0;
            $iva10Compra = 0;

            // Crear detalles de la compra/factura
            foreach ($repuestos as $repuesto) {
                $precioUnitario = $repuesto->precioActual->precio_compra ?? 0;
                $cantidad = 10;
                $subtotal = $cantidad * $precioUnitario;
                $ivaPorcentaje = 10;
                $ivaMonto = $subtotal * 0.10;

                $subtotalCompra += $subtotal;
                $iva10Compra += $ivaMonto;

                CompraDetalle::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $repuesto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                    'iva_porcentaje' => $ivaPorcentaje,
                    'iva_monto' => $ivaMonto,
                    'total' => $subtotal + $ivaMonto,
                    'descripcion' => 'REPUESTO PARA MOTOBOMBA',
                    'creadoPor' => $usuarioId,
                ]);
            }

            $compra->update([
                'subtotal' => $subtotalCompra,
                'iva_10' => $iva10Compra,
                'total_iva' => $iva10Compra,
                'total' => $subtotalCompra + $iva10Compra,
            ]);

            DB::commit();

            echo "\n✓ Proceso completado exitosamente!\n";
            echo "  - Pedido de Compra: {$pedido->numero_pedido}\n";
            echo "  - Presupuesto: {$presupuesto->numero_presupuesto}\n";
            echo "  - Orden de Compra: {$ordenCompra->numero_orden}\n";
            echo "  - Factura: {$compra->numero_factura}\n";
            echo "  - Total de repuestos: {$repuestos->count()}\n";
            echo "  - Total de la compra: ₲ " . number_format($compra->total, 0, ',', '.') . "\n";

        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n✗ Error al crear la compra: " . $e->getMessage() . "\n";
            echo "  Línea: " . $e->getLine() . "\n";
            echo "  Archivo: " . $e->getFile() . "\n";
        }
    }
}
