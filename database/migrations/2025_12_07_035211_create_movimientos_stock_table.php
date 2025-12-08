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
        Schema::create('stock.MOVIMIENTOS_STOCK', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('deposito_id');
            $table->unsignedBigInteger('usuario_id');

            // Tipo de movimiento
            $table->enum('tipo', [
                'ENTRADA_COMPRA',
                'SALIDA_VENTA',
                'AJUSTE_POSITIVO',
                'AJUSTE_NEGATIVO',
                'TRANSFERENCIA_ORIGEN',
                'TRANSFERENCIA_DESTINO'
            ]);

            // Campos de movimiento
            $table->decimal('cantidad', 12, 2);
            $table->decimal('stock_anterior', 12, 2);
            $table->decimal('stock_posterior', 12, 2);

            // Campos de costo
            $table->decimal('costo_unitario', 12, 2)->nullable();
            $table->decimal('costo_total', 12, 2)->nullable();

            // Documento vinculado
            $table->string('documento_tipo', 50)->nullable(); // 'COMPRA', 'VENTA', 'NOTA_REMISION', etc.
            $table->unsignedBigInteger('documento_id')->nullable();

            // Motivo (para ajustes y transferencias)
            $table->text('motivo')->nullable();

            // Auditoría
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->timestamps();

            // Foreign keys constraints
            $table->foreign('producto_id')
                  ->references('id')
                  ->on('stock.PRODUCTOS')
                  ->onDelete('restrict');

            $table->foreign('deposito_id')
                  ->references('id')
                  ->on('empresa.DEPOSITOS')
                  ->onDelete('restrict');

            $table->foreign('usuario_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');

            // Indices
            $table->index('producto_id');
            $table->index('deposito_id');
            $table->index('usuario_id');
            $table->index('tipo');
            $table->index('fecha_movimiento');
            $table->index(['documento_tipo', 'documento_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock.MOVIMIENTOS_STOCK');
    }
};
