<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.presupuestos', function (Blueprint $table) {
            $table->id();

            // Relación con pedido de compra (opcional, puede ser cotización directa)
            $table->unsignedBigInteger('pedido_compra_id')->nullable();

            // Relación con proveedor
            $table->unsignedBigInteger('proveedor_id');

            // Información del presupuesto
            $table->string('numero_presupuesto', 50)->unique();
            $table->date('fecha_solicitud');
            $table->date('fecha_recepcion')->nullable();
            $table->date('fecha_vencimiento')->nullable(); // Validez de la cotización

            // Condiciones comerciales ofrecidas
            $table->enum('condicion_pago', [
                'CONTADO',
                '7_DIAS',
                '15_DIAS',
                '30_DIAS',
                '60_DIAS',
                '90_DIAS'
            ])->default('CONTADO');

            $table->integer('dias_entrega')->default(0); // Tiempo de entrega prometido
            $table->decimal('descuento_general', 15, 2)->default(0);
            $table->decimal('flete', 15, 2)->default(0);

            // Totales
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('iva_10', 15, 2)->default(0);
            $table->decimal('iva_5', 15, 2)->default(0);
            $table->decimal('exenta', 15, 2)->default(0);
            $table->decimal('total_iva', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            // Estado del presupuesto
            $table->enum('estado', [
                'PENDIENTE',
                'RECIBIDO',
                'EN_EVALUACION',
                'SELECCIONADO',
                'RECHAZADO',
                'VENCIDO',
                'ANULADO'
            ])->default('PENDIENTE');

            // Evaluación
            $table->integer('puntuacion')->nullable(); // 1-10
            $table->text('observaciones_evaluacion')->nullable();
            $table->text('observaciones')->nullable();

            // Archivos adjuntos
            $table->string('archivo_presupuesto_path', 255)->nullable();

            // Control
            $table->boolean('activo')->default(true);
            $table->boolean('es_mejor_precio')->default(false);
            $table->boolean('es_mejor_plazo')->default(false);

            // Auditoría
            $table->unsignedBigInteger('solicitadoPor')->nullable();
            $table->unsignedBigInteger('evaluadoPor')->nullable();
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('pedido_compra_id');
            $table->index('proveedor_id');
            $table->index('numero_presupuesto');
            $table->index('fecha_solicitud');
            $table->index('fecha_vencimiento');
            $table->index('estado');
            $table->index('activo');

            // NOTA: Foreign keys no se definen aquí por ser cross-schema
            // Se manejarán en los modelos Eloquent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.presupuestos');
    }
};
