<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa.DEPOSITOS', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sucursal_id');
            $table->string('codigo', 10);
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->boolean('permite_venta')->default(true);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('sucursal_id')->references('id')->on('empresa.SUCURSALES')->onDelete('cascade');
            $table->foreign('creadoPor')->references('id')->on('users')->onDelete('set null');
            $table->foreign('actualizadoPor')->references('id')->on('users')->onDelete('set null');
            $table->unique(['sucursal_id', 'codigo'], 'sucursal_deposito_codigo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa.DEPOSITOS');
    }
};
