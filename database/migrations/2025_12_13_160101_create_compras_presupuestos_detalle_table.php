<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.presupuestos_detalle', function (Blueprint $table) {
            $table->id();

            // Relación con presupuesto
            $table->unsignedBigInteger('presupuesto_id');

            // Relación con producto (stock schema)
            $table->unsignedBigInteger('producto_id')->nullable();

            // Relación con pedido detalle (si viene de un pedido)
            $table->unsignedBigInteger('pedido_compra_detalle_id')->nullable();

            // Descripción (por si no está en el catálogo)
            $table->string('descripcion', 255)->nullable();

            // Cantidades y precios
            $table->decimal('cantidad_cotizada', 10, 3)->default(1);
            $table->decimal('precio_unitario', 15, 2)->default(0);

            // Descuentos
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_monto', 15, 2)->default(0);

            // Cálculos
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('iva_porcentaje', 5, 2)->default(10); // 0, 5 o 10
            $table->decimal('iva_monto', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            // Información adicional del proveedor
            $table->integer('dias_entrega_item')->nullable();
            $table->string('marca_ofrecida', 100)->nullable();
            $table->text('observaciones_proveedor')->nullable();

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('presupuesto_id');
            $table->index('producto_id');
            $table->index('pedido_compra_detalle_id');

            // NOTA: Foreign keys no se definen aquí por ser cross-schema
            // Se manejarán en los modelos Eloquent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.presupuestos_detalle');
    }
};
