<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock.PRECIOS', function (Blueprint $table) {
            $table->id();

            // Foreign key
            $table->unsignedBigInteger('producto_id');

            // Campos de precio
            $table->decimal('precio_compra', 12, 2)->default(0);
            $table->decimal('precio_venta', 12, 2)->default(0);
            $table->decimal('margen_porcentaje', 5, 2)->default(0);

            // IVA
            $table->enum('iva', ['10', '5', 'exenta'])->default('10');

            // Moneda
            $table->enum('moneda', ['PYG', 'USD'])->default('PYG');
            $table->decimal('tipo_cambio', 10, 2)->nullable();

            // Flag de precio actual
            $table->boolean('es_actual')->default(false);

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->timestamps();

            // Foreign keys constraints
            $table->foreign('producto_id')
                  ->references('id')
                  ->on('stock.PRODUCTOS')
                  ->onDelete('cascade');

            $table->foreign('creadoPor')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Indices
            $table->index('producto_id');
            $table->index('es_actual');
            $table->index('moneda');
        });

        // Crear índice único parcial para es_actual
        DB::statement('CREATE UNIQUE INDEX precios_producto_actual_unique ON stock."PRECIOS" (producto_id) WHERE es_actual = true');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock.PRECIOS');
    }
};
