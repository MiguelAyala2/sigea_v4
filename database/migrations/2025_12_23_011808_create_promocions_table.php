<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios.promociones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre');
            $table->enum('tipo', ['general', 'servicio', 'producto']);
            $table->decimal('descuento', 5, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->timestamps();

            $table->foreign('creadoPor')->references('id')->on('public.users')->onDelete('set null');
            $table->foreign('actualizadoPor')->references('id')->on('public.users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios.promociones');
    }
};
