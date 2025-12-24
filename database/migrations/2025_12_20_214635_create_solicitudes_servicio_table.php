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
        Schema::create('servicios.SOLICITUDES_SERVICIO', function (Blueprint $table) {
            $table->id();
            $table->string('numero_solicitud', 20)->unique(); // SOL-000001
            $table->date('fecha');
            $table->foreignId('cliente_id')->constrained('servicios.CLIENTES')->onDelete('restrict');
            $table->foreignId('producto_id')->constrained('stock.PRODUCTOS')->onDelete('restrict');
            $table->enum('tipo_servicio', ['mantenimiento', 'reparacion', 'diagnostico']);
            $table->enum('prioridad', ['baja', 'media', 'alta']);
            $table->enum('estado', ['pendiente', 'en_proceso', 'completado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('creadoPor')->constrained('users');
            $table->foreignId('actualizadoPor')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios.SOLICITUDES_SERVICIO');
    }
};
