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
        Schema::create('ventas.APERTURAS_CAJA', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('punto_expedicion_id')->comment('Punto de expedición/caja donde se abre');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que realiza la apertura');

            // Información de apertura
            $table->date('fecha_apertura')->comment('Fecha de apertura de caja');
            $table->time('hora_apertura')->comment('Hora de apertura');
            $table->decimal('saldo_inicial', 15, 2)->default(0)->comment('Saldo inicial con el que se abre la caja');

            // Estado
            $table->enum('estado', ['ABIERTA', 'CERRADA', 'CANCELADA'])->default('ABIERTA')->comment('Estado de la apertura');

            // Información adicional
            $table->text('observaciones')->nullable()->comment('Observaciones de la apertura');
            $table->boolean('activo')->default(true);

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('punto_expedicion_id');
            $table->index('usuario_id');
            $table->index('fecha_apertura');
            $table->index('estado');
            $table->index(['punto_expedicion_id', 'fecha_apertura']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.APERTURAS_CAJA');
    }
};
