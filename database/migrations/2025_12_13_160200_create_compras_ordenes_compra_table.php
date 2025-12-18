<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.ordenes_compra', function (Blueprint $table) {
            $table->id();

            // Información básica
            $table->string('numero_orden', 50)->unique();
            $table->date('fecha_orden');
            $table->date('fecha_entrega_esperada')->nullable();

            // Relaciones
            $table->unsignedBigInteger('proveedor_id');
            $table->unsignedBigInteger('sucursal_id'); // empresa schema
            $table->unsignedBigInteger('deposito_id')->nullable(); // empresa schema
            $table->unsignedBigInteger('presupuesto_id')->nullable(); // Si viene de un presupuesto
            $table->unsignedBigInteger('pedido_compra_id')->nullable(); // Pedido origen principal

            // Condiciones comerciales
            $table->enum('condicion_pago', [
                'CONTADO',
                '7_DIAS',
                '15_DIAS',
                '30_DIAS',
                '60_DIAS',
                '90_DIAS'
            ])->default('CONTADO');

            $table->enum('tipo_orden', [
                'NORMAL',
                'URGENTE',
                'SERVICIO',
                'IMPORTACION',
                'CONSIGNACION'
            ])->default('NORMAL');

            // Totales
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('iva_10', 15, 2)->default(0);
            $table->decimal('iva_5', 15, 2)->default(0);
            $table->decimal('exenta', 15, 2)->default(0);
            $table->decimal('total_iva', 15, 2)->default(0);
            $table->decimal('descuento_global', 15, 2)->default(0);
            $table->decimal('flete', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            // Estado
            $table->enum('estado', [
                'BORRADOR',
                'EMITIDA',
                'ENVIADA',
                'CONFIRMADA',
                'PARCIALMENTE_RECIBIDA',
                'COMPLETAMENTE_RECIBIDA',
                'FACTURADA',
                'CANCELADA',
                'ANULADA'
            ])->default('BORRADOR');

            // Control de recepción
            $table->decimal('porcentaje_recibido', 5, 2)->default(0);

            // Información de entrega
            $table->text('direccion_entrega')->nullable();
            $table->string('contacto_recepcion', 100)->nullable();
            $table->string('telefono_recepcion', 20)->nullable();

            // Observaciones
            $table->text('observaciones')->nullable();
            $table->text('condiciones_especiales')->nullable();

            // Documentos
            $table->string('archivo_orden_path', 255)->nullable();

            // Control
            $table->boolean('activo')->default(true);
            $table->boolean('requiere_confirmacion')->default(true);
            $table->timestamp('confirmada_en')->nullable();
            $table->timestamp('enviada_en')->nullable();

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->unsignedBigInteger('aprobadoPor')->nullable();
            $table->unsignedBigInteger('confirmadaPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('numero_orden');
            $table->index('fecha_orden');
            $table->index('proveedor_id');
            $table->index('sucursal_id');
            $table->index('deposito_id');
            $table->index('presupuesto_id');
            $table->index('pedido_compra_id');
            $table->index('estado');
            $table->index('tipo_orden');
            $table->index('activo');

            // NOTA: Foreign keys no se definen aquí por ser cross-schema
            // Se manejarán en los modelos Eloquent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.ordenes_compra');
    }
};
