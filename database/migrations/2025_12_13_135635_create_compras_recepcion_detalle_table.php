<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.compras_recepcion_detalle', function (Blueprint $table) {
            $table->id();

            // Relación con recepción (compras schema)
            $table->unsignedBigInteger('recepcion_id');

            // Relación con detalle de compra original (opcional)
            $table->unsignedBigInteger('compra_detalle_id')->nullable();

            // Relación con producto (stock schema)
            $table->unsignedBigInteger('producto_id');

            // Cantidades
            $table->decimal('cantidad_esperada', 10, 3)->default(0);
            $table->decimal('cantidad_recibida', 10, 3)->default(0);
            $table->decimal('cantidad_aceptada', 10, 3)->default(0);
            $table->decimal('cantidad_rechazada', 10, 3)->default(0);
            $table->decimal('diferencia', 10, 3)->default(0); // recibida - esperada

            // Motivos y observaciones
            $table->string('motivo_diferencia', 255)->nullable();
            $table->text('observaciones')->nullable();

            // Datos del producto (lote, vencimiento)
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();

            // Ubicación en almacén
            $table->string('ubicacion_almacen', 100)->nullable();

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('recepcion_id');
            $table->index('compra_detalle_id');
            $table->index('producto_id');
            $table->index('lote');
            $table->index('fecha_vencimiento');

            // NOTA: Foreign keys no se definen aquí por ser cross-schema
            // Se manejarán en los modelos Eloquent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.compras_recepcion_detalle');
    }
};
