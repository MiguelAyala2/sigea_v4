<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock.IMAGENES_PRODUCTO', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->string('path', 255); // Ruta de la imagen
            $table->string('nombre_original', 255)->nullable(); // Nombre original del archivo
            $table->boolean('es_principal')->default(false); // Imagen destacada
            $table->integer('orden')->default(0); // Orden de visualización
            $table->timestamps();

            // Foreign key
            $table->foreign('producto_id')
                  ->references('id')
                  ->on('stock.PRODUCTOS')
                  ->onDelete('cascade');

            // Índices
            $table->index('producto_id');
            $table->index('es_principal');
            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock.IMAGENES_PRODUCTO');
    }
};
