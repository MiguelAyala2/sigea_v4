<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.pedidos_compra', function (Blueprint $table) {
            $table->id();

            // Información básica
            $table->string('numero_pedido', 50)->unique();
            $table->date('fecha_pedido');
            $table->date('fecha_necesaria')->nullable(); // Cuándo se necesita

            // Relaciones con otros schemas
            $table->unsignedBigInteger('sucursal_id'); // empresa schema
            $table->unsignedBigInteger('deposito_destino_id')->nullable(); // empresa schema
            $table->unsignedBigInteger('usuario_solicitante_id'); // public.users

            // Clasificación del pedido
            $table->enum('tipo_pedido', [
                'REPOSICION_STOCK',
                'COMPRA_DIRECTA',
                'PROYECTO_ESPECIFICO',
                'MANTENIMIENTO',
                'INSUMOS'
            ])->default('REPOSICION_STOCK');

            $table->enum('prioridad', ['NORMAL', 'URGENTE', 'CRITICA'])->default('NORMAL');

            // Estado del pedido
            $table->enum('estado', [
                'BORRADOR',
                'PENDIENTE_APROBACION',
                'APROBADO',
                'RECHAZADO',
                'EN_COTIZACION',
                'PARCIALMENTE_ORDENADO',
                'COMPLETAMENTE_ORDENADO',
                'ANULADO'
            ])->default('BORRADOR');

            // Control de progreso
            $table->decimal('total_estimado', 15, 2)->default(0);
            $table->decimal('porcentaje_ordenado', 5, 2)->default(0); // % ya convertido a órdenes

            // Justificación
            $table->text('justificacion')->nullable();
            $table->text('observaciones')->nullable();

            // Control
            $table->boolean('requiere_aprobacion')->default(true);
            $table->boolean('urgente')->default(false);
            $table->boolean('activo')->default(true);

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->unsignedBigInteger('aprobadoPor')->nullable();
            $table->timestamp('aprobado_en')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('numero_pedido');
            $table->index('fecha_pedido');
            $table->index('sucursal_id');
            $table->index('deposito_destino_id');
            $table->index('usuario_solicitante_id');
            $table->index('tipo_pedido');
            $table->index('prioridad');
            $table->index('estado');
            $table->index('activo');

            // NOTA: Foreign keys no se definen aquí por ser cross-schema
            // Se manejarán en los modelos Eloquent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.pedidos_compra');
    }
};
