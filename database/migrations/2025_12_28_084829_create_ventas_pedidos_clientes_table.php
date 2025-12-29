<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla principal de Pedidos de Clientes
        Schema::create('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->id();

            // Información del Pedido
            $table->string('numero_pedido', 20)->unique()->comment('PC-000001, PC-000002, etc.');
            $table->date('fecha_pedido')->comment('Fecha en que se registra el pedido');
            $table->date('fecha_entrega_estimada')->nullable()->comment('Fecha estimada de entrega');

            // Relaciones
            $table->unsignedBigInteger('cliente_id')->comment('Cliente que realiza el pedido');
            $table->unsignedBigInteger('sucursal_id')->default(1)->comment('Sucursal donde se registra');
            $table->unsignedBigInteger('deposito_id')->default(1)->comment('Depósito desde donde se entregará');
            $table->unsignedBigInteger('vendedor_id')->nullable()->comment('Vendedor que atiende');
            $table->unsignedBigInteger('cotizacion_id')->nullable()->comment('Si viene de una cotización aprobada');

            // Condiciones Comerciales
            $table->enum('condicion_pago', [
                'CONTADO',
                '7_DIAS',
                '15_DIAS',
                '30_DIAS',
                '60_DIAS',
                '90_DIAS'
            ])->default('CONTADO')->comment('Forma de pago acordada');

            $table->enum('tipo_pedido', [
                'NORMAL',
                'URGENTE',
                'ENTREGA_PROGRAMADA',
                'MAYORISTA',
                'MINORISTA'
            ])->default('NORMAL')->comment('Tipo de pedido');

            $table->enum('tipo_entrega', [
                'RETIRO_LOCAL',
                'DELIVERY',
                'ENVIO_TRANSPORTE'
            ])->default('RETIRO_LOCAL')->comment('Forma de entrega');

            // Totales
            $table->decimal('subtotal', 15, 2)->default(0)->comment('Suma de subtotales de detalles');
            $table->decimal('iva_10', 15, 2)->default(0)->comment('Total IVA 10%');
            $table->decimal('iva_5', 15, 2)->default(0)->comment('Total IVA 5%');
            $table->decimal('exenta', 15, 2)->default(0)->comment('Total Exentas');
            $table->decimal('total_iva', 15, 2)->default(0)->comment('Suma de IVA 5% + IVA 10%');
            $table->decimal('descuento_global', 15, 2)->default(0)->comment('Descuento aplicado al total');
            $table->decimal('flete', 15, 2)->default(0)->comment('Costo de envío/flete');
            $table->decimal('total', 15, 2)->default(0)->comment('Total final del pedido');

            // Estado y Control
            $table->enum('estado', [
                'BORRADOR',
                'PENDIENTE',
                'CONFIRMADO',
                'EN_PREPARACION',
                'LISTO_ENTREGAR',
                'PARCIALMENTE_ENTREGADO',
                'COMPLETAMENTE_ENTREGADO',
                'FACTURADO',
                'CANCELADO',
                'ANULADO'
            ])->default('BORRADOR')->comment('Estado actual del pedido');

            $table->decimal('porcentaje_entregado', 5, 2)->default(0)->comment('% del pedido que ha sido entregado');

            // Información Adicional
            $table->text('observaciones')->nullable()->comment('Notas del pedido');
            $table->text('condiciones_especiales')->nullable()->comment('Condiciones adicionales');
            $table->string('direccion_entrega', 500)->nullable()->comment('Dirección de entrega si es delivery');

            // Auditoría
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->unsignedBigInteger('actualizado_por')->nullable();
            $table->unsignedBigInteger('confirmado_por')->nullable();
            $table->timestamp('confirmado_en')->nullable();
            $table->unsignedBigInteger('facturado_por')->nullable();
            $table->timestamp('facturado_en')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('cliente_id');
            $table->index('sucursal_id');
            $table->index('deposito_id');
            $table->index('vendedor_id');
            $table->index('fecha_pedido');
            $table->index('estado');
            $table->index('numero_pedido');
        });

        // Tabla de Detalles del Pedido
        Schema::create('ventas.pedidos_clientes_detalle', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('pedido_cliente_id')->comment('ID del pedido');
            $table->unsignedBigInteger('producto_id')->comment('Producto solicitado');
            $table->unsignedBigInteger('cotizacion_detalle_id')->nullable()->comment('Si viene de cotización');

            // Cantidades
            $table->decimal('cantidad_solicitada', 10, 2)->comment('Cantidad pedida por el cliente');
            $table->decimal('cantidad_entregada', 10, 2)->default(0)->comment('Cantidad ya entregada');
            $table->decimal('cantidad_pendiente', 10, 2)->comment('Cantidad que falta entregar');

            // Precios
            $table->decimal('precio_unitario', 15, 2)->comment('Precio de venta unitario');

            // Descuentos
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->comment('% de descuento en la línea');
            $table->decimal('descuento_monto', 15, 2)->default(0)->comment('Monto del descuento');

            // Cálculos
            $table->decimal('subtotal', 15, 2)->comment('cantidad × precio - descuento');
            $table->decimal('iva_porcentaje', 5, 2)->default(10)->comment('0, 5 o 10');
            $table->decimal('iva_monto', 15, 2)->default(0)->comment('Monto de IVA');
            $table->decimal('total', 15, 2)->comment('Subtotal + IVA');

            // Estado
            $table->enum('estado', [
                'PENDIENTE',
                'PARCIALMENTE_ENTREGADO',
                'COMPLETAMENTE_ENTREGADO',
                'CANCELADO'
            ])->default('PENDIENTE');

            $table->text('observaciones')->nullable()->comment('Notas sobre este producto');

            $table->timestamps();
            $table->softDeletes();

            // Índices y Claves Foráneas
            $table->index('pedido_cliente_id');
            $table->index('producto_id');
            $table->index('estado');

            $table->foreign('pedido_cliente_id')
                ->references('id')
                ->on('ventas.pedidos_clientes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.pedidos_clientes_detalle');
        Schema::dropIfExists('ventas.pedidos_clientes');
    }
};
