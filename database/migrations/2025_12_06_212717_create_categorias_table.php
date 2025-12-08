<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock.CATEGORIAS', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->integer('nivel')->default(1); // 1=Grupo, 2=Subgrupo, 3=Especialización
            $table->boolean('activo')->default(true);

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key para jerarquía
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('stock.CATEGORIAS')
                  ->onDelete('restrict');

            // Índices
            $table->index('parent_id');
            $table->index('codigo');
            $table->index('activo');
            $table->index('nivel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock.CATEGORIAS');
    }
};
