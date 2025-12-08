<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock.ATRIBUTOS_PRODUCTO', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('atributo_tipo_id');
            $table->string('valor', 255); // Valor del atributo para este producto
            $table->timestamps();

            // Foreign keys
            $table->foreign('producto_id')
                  ->references('id')
                  ->on('stock.PRODUCTOS')
                  ->onDelete('cascade');

            $table->foreign('atributo_tipo_id')
                  ->references('id')
                  ->on('stock.ATRIBUTOS_TIPO')
                  ->onDelete('cascade');

            // Índices
            $table->index('producto_id');
            $table->index('atributo_tipo_id');
            $table->unique(['producto_id', 'atributo_tipo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock.ATRIBUTOS_PRODUCTO');
    }
};
