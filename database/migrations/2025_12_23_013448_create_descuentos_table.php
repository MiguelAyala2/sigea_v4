<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios.descuentos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('descripcion');
            $table->enum('tipo_descuento', ['porcentaje', 'monto_fijo']);
            $table->decimal('valor', 10, 2);
            $table->string('aplicable_a');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('servicios.descuentos');
    }
};
