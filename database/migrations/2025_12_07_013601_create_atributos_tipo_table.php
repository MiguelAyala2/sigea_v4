<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock.ATRIBUTOS_TIPO', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->string('unidad', 20)->nullable(); // Ej: HP, m, L, mm, etc.
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('es_filtrable')->default(true); // Para búsquedas avanzadas
            $table->boolean('es_requerido')->default(false); // Si es obligatorio para ciertos productos
            $table->boolean('activo')->default(true);

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('codigo');
            $table->index('activo');
            $table->index('orden');
            $table->index('es_filtrable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock.ATRIBUTOS_TIPO');
    }
};
