<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.pedidos_compra_detalle', function (Blueprint $table) {
            $table->id();

            // Relación con pedido
            $table->unsignedBigInteger('pedido_compra_id');

            // Relación con producto (stock schema)
            $table->unsignedBigInteger('producto_id');

            // Cantidades
            $table->decimal('cantidad_solicitada', 10, 3)->default(1);
            $table->decimal('cantidad_aprobada', 10, 3)->default(0);
            $table->decimal('cantidad_ordenada', 10, 3)->default(0); // Ya en órdenes de compra
            $table->decimal('cantidad_pendiente', 10, 3)->default(0); // Falta ordenar

            // Stock al momento de crear el pedido (referencia)
            $table->decimal('stock_actual', 10, 3)->default(0);
            $table->decimal('stock_minimo', 10, 3)->default(0);

            // Precio estimado/referencial
            $table->decimal('precio_estimado', 15, 2)->default(0);
            $table->decimal('subtotal_estimado', 15, 2)->default(0);

            // Justificación del ítem
            $table->text('justificacion_item')->nullable();
            $table->text('observaciones')->nullable();

            // Estado del ítem
            $table->enum('estado', [
                'PENDIENTE',
                'APROBADO',
                'RECHAZADO',
                'PARCIALMENTE_ORDENADO',
                'COMPLETAMENTE_ORDENADO'
            ])->default('PENDIENTE');

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('pedido_compra_id');
            $table->index('producto_id');
            $table->index('estado');

            // NOTA: Foreign keys no se definen aquí por ser cross-schema
            // Se manejarán en los modelos Eloquent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.pedidos_compra_detalle');
    }
};
