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
        Schema::create('servicios.DIAGNOSTICOS', function (Blueprint $table) {
            $table->id();
            $table->string('numero_diagnostico', 20)->unique();
            $table->date('fecha_diagnostico');

            // Relaciones
            $table->foreignId('solicitud_id')->constrained('servicios.SOLICITUDES_SERVICIO')->onDelete('restrict');
            $table->foreignId('recepcion_id')->constrained('servicios.RECEPCIONES')->onDelete('restrict');
            $table->foreignId('cliente_id')->constrained('servicios.CLIENTES')->onDelete('restrict');
            $table->foreignId('producto_id')->constrained('stock.PRODUCTOS')->onDelete('restrict');

            // Datos del diagnóstico
            $table->text('problema_detectado');
            $table->text('solucion_propuesta')->nullable();
            $table->enum('estado_diagnostico', ['reparable', 'no_reparable', 'requiere_repuestos']);
            $table->enum('estado', ['pendiente', 'en_proceso', 'completado'])->default('pendiente');

            // Observaciones
            $table->text('observaciones')->nullable();

            // Estado y auditoría
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla intermedia para repuestos necesarios
        Schema::create('servicios.DIAGNOSTICO_REPUESTOS', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostico_id')->constrained('servicios.DIAGNOSTICOS')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('stock.PRODUCTOS')->onDelete('restrict');
            $table->integer('cantidad');
            $table->decimal('costo', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios.DIAGNOSTICO_REPUESTOS');
        Schema::dropIfExists('servicios.DIAGNOSTICOS');
    }
};
