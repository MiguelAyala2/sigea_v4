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
        Schema::create('stock.ajustes_stock', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('deposito_id');
            $table->unsignedBigInteger('usuario_id'); // Usuario que realizó el ajuste

            // Tipo de ajuste
            $table->enum('tipo_ajuste', ['AJUSTE_POSITIVO', 'AJUSTE_NEGATIVO']);

            // Motivo del ajuste (predefinido)
            $table->string('motivo_ajuste', 255);

            // Observaciones adicionales
            $table->text('observaciones');

            // Cantidades
            $table->decimal('cantidad', 15, 2);
            $table->decimal('stock_anterior', 15, 2);
            $table->decimal('stock_posterior', 15, 2);

            // Fecha del ajuste
            $table->timestamp('fecha_ajuste');

            // Referencia al movimiento de stock (kardex)
            $table->unsignedBigInteger('movimiento_stock_id')->nullable();

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('producto_id');
            $table->index('deposito_id');
            $table->index('usuario_id');
            $table->index('tipo_ajuste');
            $table->index('fecha_ajuste');

            // Foreign keys
            $table->foreign('producto_id')->references('id')->on('stock.PRODUCTOS')->onDelete('restrict');
            $table->foreign('deposito_id')->references('id')->on('empresa.DEPOSITOS')->onDelete('restrict');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('movimiento_stock_id')->references('id')->on('stock.MOVIMIENTOS_STOCK')->onDelete('set null');
            $table->foreign('creadoPor')->references('id')->on('users')->onDelete('set null');
            $table->foreign('actualizadoPor')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock.ajustes_stock');
    }
};
