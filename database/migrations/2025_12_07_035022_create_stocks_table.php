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
        Schema::create('stock.STOCK', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('deposito_id');

            // Campos de control de stock
            $table->decimal('stock_actual', 12, 2)->default(0);
            $table->decimal('stock_minimo', 12, 2)->default(0);
            $table->decimal('stock_maximo', 12, 2)->nullable();

            // Campos de ubicación
            $table->string('ubicacion', 100)->nullable();
            $table->string('pasillo', 50)->nullable();
            $table->string('estante', 50)->nullable();

            // Lote y vencimiento
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys constraints
            $table->foreign('producto_id')
                  ->references('id')
                  ->on('stock.PRODUCTOS')
                  ->onDelete('restrict');

            $table->foreign('deposito_id')
                  ->references('id')
                  ->on('empresa.DEPOSITOS')
                  ->onDelete('restrict');

            $table->foreign('creadoPor')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('actualizadoPor')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Unique constraint: un producto solo puede tener un registro de stock por depósito
            $table->unique(['producto_id', 'deposito_id']);

            // Indices
            $table->index('producto_id');
            $table->index('deposito_id');
            $table->index('stock_actual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock.STOCK');
    }
};
