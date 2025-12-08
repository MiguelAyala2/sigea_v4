<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock.PRODUCTOS', function (Blueprint $table) {
            $table->id();

            // Códigos
            $table->string('codigo', 20)->unique();
            $table->string('codigo_barras', 50)->nullable();
            $table->string('codigo_fabricante', 50)->nullable();

            // Información básica
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->string('modelo', 100)->nullable();
            $table->text('aplicacion')->nullable();

            // Clasificación
            $table->enum('tipo', ['PRODUCTO', 'INSUMO', 'SERVICIO', 'KIT'])->default('PRODUCTO');
            $table->enum('origen', ['NACIONAL', 'IMPORTADO'])->default('NACIONAL');

            // Relaciones
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->unsignedBigInteger('marca_id')->nullable();
            $table->unsignedBigInteger('unidad_medida_id');

            // Flags de gestión
            $table->boolean('permite_venta')->default(true);
            $table->boolean('permite_compra')->default(true);
            $table->boolean('maneja_stock')->default(true);
            $table->boolean('activo')->default(true);

            // Stock mínimo y máximo
            $table->decimal('stock_minimo', 10, 2)->default(0);
            $table->decimal('stock_maximo', 10, 2)->nullable();

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('categoria_id')
                  ->references('id')
                  ->on('stock.CATEGORIAS')
                  ->onDelete('restrict');

            $table->foreign('marca_id')
                  ->references('id')
                  ->on('stock.MARCAS')
                  ->onDelete('restrict');

            $table->foreign('unidad_medida_id')
                  ->references('id')
                  ->on('stock.UNIDADES_MEDIDA')
                  ->onDelete('restrict');

            // Índices
            $table->index('codigo');
            $table->index('codigo_barras');
            $table->index('categoria_id');
            $table->index('marca_id');
            $table->index('tipo');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock.PRODUCTOS');
    }
};
