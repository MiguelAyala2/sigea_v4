<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.compras_aprobacion_flujo', function (Blueprint $table) {
            $table->id();
            
            // Información del documento a aprobar
            $table->enum('documento_tipo', [
                'PEDIDO_COMPRA',
                'PRESUPUESTO',
                'ORDEN_COMPRA',
                'COMPRA',
                'RECEPCION',
                'PAGO'
            ]);
            
            $table->unsignedBigInteger('documento_id');
            
            // Información de aprobación
            $table->enum('estado', ['PENDIENTE', 'APROBADO', 'RECHAZADO', 'OBSERVADO'])->default('PENDIENTE');
            $table->integer('nivel_aprobacion')->default(1);
            $table->integer('secuencia')->default(1);
            
            // Usuario responsable
            $table->unsignedBigInteger('usuario_aprobador_id')->nullable();
            $table->string('rol_requerido', 100)->nullable();
            
            // Fechas de aprobación
            $table->timestamp('fecha_asignacion')->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamp('fecha_vencimiento')->nullable();
            
            // Detalles
            $table->text('comentarios')->nullable();
            $table->text('observaciones_rechazo')->nullable();
            
            // Adjuntos (ruta a archivos)
            $table->string('adjunto_firma_path', 255)->nullable();
            $table->string('adjunto_comprobante_path', 255)->nullable();
            
            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['documento_tipo', 'documento_id']);
            $table->index('estado');
            $table->index('nivel_aprobacion');
            $table->index('usuario_aprobador_id');
            $table->index('rol_requerido');
            $table->index('fecha_vencimiento');
            
            // Unique constraint: un solo aprobador por nivel y documento
            $table->unique(['documento_tipo', 'documento_id', 'nivel_aprobacion'], 'documento_nivel_aprobacion_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.compras_aprobacion_flujo');
    }
};
