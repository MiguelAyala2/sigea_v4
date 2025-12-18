<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.ordenes_compra_detalle', function (Blueprint $table) {
            $table->id();

            // Relación con orden de compra
            $table->unsignedBigInteger('orden_compra_id');

            // Relación con producto (stock schema)
            $table->unsignedBigInteger('producto_id')->nullable();

            // Relación con pedido detalle (trazabilidad)
            $table->unsignedBigInteger('pedido_compra_detalle_id')->nullable();

            // Relación con presupuesto detalle (si viene de cotización)
            $table->unsignedBigInteger('presupuesto_detalle_id')->nullable();

            // Descripción (para productos no catalogados)
            $table->string('descripcion', 255)->nullable();

            // Cantidades
            $table->decimal('cantidad_ordenada', 10, 3)->default(1);
            $table->decimal('cantidad_recibida', 10, 3)->default(0);
            $table->decimal('cantidad_pendiente', 10, 3)->default(0);

            // Precios acordados
            $table->decimal('precio_unitario', 15, 2)->default(0);

            // Descuentos
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_monto', 15, 2)->default(0);

            // Cálculos
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('iva_porcentaje', 5, 2)->default(10); // 0, 5 o 10
            $table->decimal('iva_monto', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            // Estado del ítem
            $table->enum('estado', [
                'PENDIENTE',
                'PARCIALMENTE_RECIBIDO',
                'COMPLETAMENTE_RECIBIDO',
                'CANCELADO'
            ])->default('PENDIENTE');

            // Información adicional
            $table->text('observaciones')->nullable();

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('orden_compra_id');
            $table->index('producto_id');
            $table->index('pedido_compra_detalle_id');
            $table->index('presupuesto_detalle_id');
            $table->index('estado');

            // NOTA: Foreign keys no se definen aquí por ser cross-schema
            // Se manejarán en los modelos Eloquent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.ordenes_compra_detalle');
    }
};
