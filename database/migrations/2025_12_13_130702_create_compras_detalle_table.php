<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.compras_detalle', function (Blueprint $table) {
            $table->id();

            // Relación con compra
            $table->unsignedBigInteger('compra_id');

            // Relación con producto (stock schema)
            $table->unsignedBigInteger('producto_id')->nullable();

            // Cantidades y precios
            $table->decimal('cantidad', 10, 3)->default(1);
            $table->decimal('precio_unitario', 15, 2)->default(0);

            // Descuentos
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_monto', 15, 2)->default(0);

            // Cálculos
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('iva_porcentaje', 5, 2)->default(10); // 0, 5 o 10
            $table->decimal('iva_monto', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            // Descripción (para productos no catalogados)
            $table->string('descripcion', 255)->nullable();

            // Control de lotes y vencimientos
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('compra_id');
            $table->index('producto_id');
            $table->index('lote');
            $table->index('fecha_vencimiento');

            // NOTA: Foreign keys no se definen aquí por ser cross-schema
            // Se manejarán en los modelos Eloquent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.compras_detalle');
    }
};
