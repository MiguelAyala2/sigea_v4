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
        Schema::create('servicios.RECEPCIONES', function (Blueprint $table) {
            $table->id();
            $table->string('numero_recepcion', 20)->unique(); // REC-000001
            $table->date('fecha_recepcion');
            $table->foreignId('solicitud_id')->constrained('servicios.SOLICITUDES_SERVICIO')->onDelete('restrict');
            $table->foreignId('cliente_id')->constrained('servicios.CLIENTES')->onDelete('restrict');
            $table->foreignId('producto_id')->constrained('stock.PRODUCTOS')->onDelete('restrict');
            $table->string('contacto_cliente')->nullable(); // Teléfono o email del cliente

            // Datos del equipo
            $table->string('tipo_equipo')->nullable(); // Tipo de equipo (ej: Bomba de Agua)
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->nullable();
            $table->enum('estado_recepcion', ['bueno', 'regular', 'malo']);

            // Detalles del servicio
            $table->text('descripcion_problema');
            $table->text('accesorios_recibidos')->nullable();

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
        Schema::dropIfExists('servicios.RECEPCIONES');
    }
};
