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
        Schema::create('ventas.FACTURAS_DETALLE', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('factura_id')->comment('Factura a la que pertenece este detalle');
            $table->unsignedBigInteger('pedido_detalle_id')->nullable()->comment('Relación con detalle del pedido (si aplica)');
            $table->unsignedBigInteger('producto_id')->comment('Producto facturado');

            // Datos del producto
            $table->decimal('cantidad', 10, 2)->comment('Cantidad del producto');
            $table->decimal('precio_unitario', 15, 2)->comment('Precio unitario del producto');

            // Descuentos
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->comment('Porcentaje de descuento');
            $table->decimal('descuento_monto', 15, 2)->default(0)->comment('Monto de descuento calculado');

            // Cálculos
            $table->decimal('subtotal', 15, 2)->comment('Subtotal antes de IVA (precio * cantidad - descuento)');
            $table->decimal('iva_porcentaje', 5, 2)->default(10)->comment('Porcentaje de IVA (0, 5 o 10)');
            $table->decimal('iva_monto', 15, 2)->default(0)->comment('Monto de IVA calculado');
            $table->decimal('total', 15, 2)->comment('Total de la línea (subtotal + IVA)');

            // Información adicional
            $table->text('descripcion_adicional')->nullable()->comment('Descripción adicional del producto');

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('factura_id');
            $table->index('producto_id');
            $table->index('pedido_detalle_id');

            // Foreign key con cascade
            $table->foreign('factura_id')
                ->references('id')
                ->on('ventas.FACTURAS')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.FACTURAS_DETALLE');
    }
};
