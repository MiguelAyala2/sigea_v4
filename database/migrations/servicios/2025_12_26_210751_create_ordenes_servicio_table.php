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
        Schema::create('servicios.ORDENES_SERVICIO', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique(); // ORD-SRV-000001
            $table->date('fecha_orden');

            // Relación con presupuesto aprobado
            $table->foreignId('presupuesto_id')->constrained('servicios.PRESUPUESTOS')->onDelete('cascade');

            // Técnico asignado
            $table->foreignId('tecnico_id')->nullable()->constrained('public.users')->onDelete('set null');

            // Estado de la orden
            $table->enum('estado', [
                'pendiente',      // Orden creada, esperando inicio
                'en_proceso',     // Técnico trabajando en ella
                'pausada',        // Trabajo pausado temporalmente
                'finalizada',     // Trabajo completado
                'entregada',      // Equipo entregado al cliente
                'cancelada'       // Orden cancelada
            ])->default('pendiente');

            // Fechas de seguimiento
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_finalizacion')->nullable();
            $table->timestamp('fecha_entrega')->nullable();

            // Progreso de la orden (0-100)
            $table->integer('progreso')->default(0);

            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);

            // Auditoría
            $table->foreignId('creadoPor')->constrained('public.users')->onDelete('cascade');
            $table->foreignId('actualizadoPor')->nullable()->constrained('public.users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios.ORDENES_SERVICIO');
    }
};
