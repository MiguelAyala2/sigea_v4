<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.compras_recepcion', function (Blueprint $table) {
            $table->id();

            // Relación con la compra/factura (documento existente)
            $table->unsignedBigInteger('compra_id');
            
            // Relación con depósito de recepción (schema empresa)
            $table->unsignedBigInteger('deposito_id');
            
            // Información de recepción
            $table->date('fecha_recepcion');
            $table->enum('estado', ['PENDIENTE', 'PARCIAL', 'COMPLETA', 'RECHAZADA'])->default('PENDIENTE');
            $table->decimal('porcentaje_recibido', 5, 2)->default(0.00);
            $table->text('observaciones')->nullable();
            
            // Documentos de recepción
            $table->string('numero_remision', 50)->nullable();
            $table->string('guia_transporte', 50)->nullable();
            $table->date('fecha_remision')->nullable();
            
            // Auditoría
            $table->unsignedBigInteger('usuario_receptor_id')->nullable();
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('compra_id');
            $table->index('deposito_id');
            $table->index('fecha_recepcion');
            $table->index('estado');
            $table->index('numero_remision');
            
            // Constraints únicas
            $table->unique(['compra_id', 'numero_remision'], 'compra_remision_unique');
            
            // NOTA: Foreign keys no se definen aquí por ser cross-schema
            // Se manejarán en los modelos Eloquent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.compras_recepcion');
    }
};
