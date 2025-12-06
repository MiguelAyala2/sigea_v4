<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa.SUCURSALES', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->char('codigo_establecimiento', 3);
            $table->string('nombre', 100);
            $table->string('direccion', 255);
            $table->string('departamento', 50);
            $table->string('ciudad', 50);
            $table->string('telefono', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->boolean('es_casa_central')->default(false);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresa.EMPRESA')->onDelete('cascade');
            $table->foreign('creadoPor')->references('id')->on('users')->onDelete('set null');
            $table->foreign('actualizadoPor')->references('id')->on('users')->onDelete('set null');
            $table->unique(['empresa_id', 'codigo_establecimiento'], 'empresa_sucursal_codigo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa.SUCURSALES');
    }
};
