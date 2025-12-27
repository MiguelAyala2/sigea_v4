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
        Schema::create('servicios.RECLAMOS', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique(); // RCL-000001
            $table->date('fecha_reclamo');

            // Relación con cliente
            $table->foreignId('cliente_id')->constrained('servicios.CLIENTES')->onDelete('cascade');

            // Relación opcional con orden de servicio
            $table->foreignId('orden_servicio_id')->nullable()->constrained('servicios.ORDENES_SERVICIO')->onDelete('set null');

            // Tipo de reclamo
            $table->enum('tipo_reclamo', [
                'calidad_servicio',
                'demora_entrega',
                'falla_post_servicio',
                'atencion_cliente',
                'costo_facturacion',
                'otro'
            ]);

            // Prioridad
            $table->enum('prioridad', [
                'baja',
                'media',
                'alta',
                'urgente'
            ])->default('media');

            // Estado del reclamo
            $table->enum('estado', [
                'pendiente',
                'en_revision',
                'en_proceso',
                'resuelto',
                'cerrado',
                'rechazado'
            ])->default('pendiente');

            // Canal de recepción
            $table->enum('canal_recepcion', [
                'presencial',
                'telefono',
                'email',
                'whatsapp',
                'web'
            ])->default('presencial');

            // Descripción del reclamo
            $table->text('descripcion');

            // Responsable asignado
            $table->foreignId('responsable_id')->nullable()->constrained('public.users')->onDelete('set null');

            // Solución y cierre
            $table->text('solucion')->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamp('fecha_cierre')->nullable();

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
        Schema::dropIfExists('servicios.RECLAMOS');
    }
};
