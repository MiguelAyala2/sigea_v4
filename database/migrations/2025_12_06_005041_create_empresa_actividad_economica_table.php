<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa.EMPRESA_ACTIVIDAD_ECONOMICA', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('actividad_economica_id');
            $table->boolean('es_principal')->default(false);
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresa.EMPRESA')->onDelete('cascade');
            $table->foreign('actividad_economica_id')->references('id')->on('empresa.ACTIVIDADES_ECONOMICAS')->onDelete('cascade');
            $table->unique(['empresa_id', 'actividad_economica_id'], 'empresa_actividad_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa.EMPRESA_ACTIVIDAD_ECONOMICA');
    }
};
