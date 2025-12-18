<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.compras_integracion', function (Blueprint $table) {
            $table->id();
            
            // Documento origen
            $table->enum('documento_origen_tipo', [
                'PEDIDO_COMPRA',
                'PRESUPUESTO',
                'ORDEN_COMPRA',
                'COMPRA',
                'RECEPCION'
            ]);
            
            $table->unsignedBigInteger('documento_origen_id');
            
            // Documento destino
            $table->enum('documento_destino_tipo', [
                'PEDIDO_COMPRA',
                'PRESUPUESTO',
                'ORDEN_COMPRA',
                'COMPRA',
                'RECEPCION',
                'PAGO'
            ]);
            
            $table->unsignedBigInteger('documento_destino_id');
            
            // Información de la relación
            $table->enum('tipo_relacion', [
                'GENERA',
                'SE_CONVIERTE_EN',
                'DEPENDE_DE',
                'REFERENCIA',
                'CORRIGE',
                'ANULA'
            ]);
            
            $table->decimal('porcentaje_relacion', 5, 2)->nullable()->comment('Porcentaje del origen que se refleja en el destino');
            $table->boolean('es_completa')->default(true)->comment('Indica si toda la cantidad del origen fue procesada en el destino');
            
            // Metadatos
            $table->text('observaciones')->nullable();
            $table->json('detalles_relacion')->nullable()->comment('Detalles específicos de la relación en formato JSON');
            
            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['documento_origen_tipo', 'documento_origen_id']);
            $table->index(['documento_destino_tipo', 'documento_destino_id']);
            $table->index('tipo_relacion');
            $table->index('es_completa');
            
            // Unique constraint: relación única entre documentos
            $table->unique([
                'documento_origen_tipo',
                'documento_origen_id',
                'documento_destino_tipo',
                'documento_destino_id',
                'tipo_relacion'
            ], 'relacion_documentos_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.compras_integracion');
    }
};
