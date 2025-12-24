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
        Schema::create('servicios.PRESUPUESTOS', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->date('fecha_presupuesto');

            // Relación con diagnóstico
            $table->foreignId('diagnostico_id')->constrained('servicios.DIAGNOSTICOS')->onDelete('cascade');

            // Relaciones con promoción y descuento (opcionales)
            $table->foreignId('promocion_id')->nullable()->constrained('servicios.promociones')->onDelete('set null');
            $table->foreignId('descuento_id')->nullable()->constrained('servicios.descuentos')->onDelete('set null');

            // Totales calculados
            $table->decimal('subtotal_servicios', 15, 2)->default(0);
            $table->decimal('descuento_promocion', 15, 2)->default(0);
            $table->decimal('total_servicios', 15, 2)->default(0);

            $table->decimal('subtotal_repuestos', 15, 2)->default(0);
            $table->decimal('descuento_descuento', 15, 2)->default(0);
            $table->decimal('total_repuestos', 15, 2)->default(0);

            $table->decimal('monto_total', 15, 2)->default(0);

            // Estado del presupuesto
            $table->enum('estado', ['pendiente_aprobacion', 'aprobado', 'rechazado'])->default('pendiente_aprobacion');

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
        Schema::dropIfExists('servicios.PRESUPUESTOS');
    }
};
