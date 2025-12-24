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
        Schema::create('servicios.DIAGNOSTICO_TIPOS_SERVICIO', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostico_id')->constrained('servicios.DIAGNOSTICOS')->onDelete('cascade');
            $table->foreignId('tipo_servicio_id')->constrained('servicios.TIPOS_SERVICIO')->onDelete('restrict');
            $table->integer('cantidad')->default(1);
            $table->decimal('costo_unitario', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            // Índices
            $table->index('diagnostico_id');
            $table->index('tipo_servicio_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios.DIAGNOSTICO_TIPOS_SERVICIO');
    }
};
