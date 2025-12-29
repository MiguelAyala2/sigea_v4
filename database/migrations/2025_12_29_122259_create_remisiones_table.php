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
        // Tabla principal de remisiones
        Schema::create('ventas.REMISIONES', function (Blueprint $table) {
            $table->id();

            // Número de remisión
            $table->string('numero_remision', 20)->unique()->comment('Número de remisión generado');

            // Fechas
            $table->date('fecha_emision')->comment('Fecha de emisión de la remisión');
            $table->date('fecha_entrega')->nullable()->comment('Fecha de entrega al cliente');

            // Relaciones
            $table->unsignedBigInteger('cliente_id')->comment('Cliente destinatario');
            $table->unsignedBigInteger('factura_id')->nullable()->comment('Factura origen (si aplica)');
            $table->unsignedBigInteger('pedido_cliente_id')->nullable()->comment('Pedido origen (si aplica)');

            // Ubicación
            $table->unsignedBigInteger('sucursal_id')->comment('Sucursal donde se emite');
            $table->unsignedBigInteger('deposito_id')->comment('Depósito desde donde se remite');
            $table->unsignedBigInteger('responsable_id')->nullable()->comment('Usuario responsable de la remisión');

            // Información adicional
            $table->text('observaciones')->nullable()->comment('Observaciones generales');
            $table->string('direccion_entrega')->nullable()->comment('Dirección de entrega');
            $table->string('receptor_nombre')->nullable()->comment('Nombre de quien recibe');
            $table->string('receptor_ci')->nullable()->comment('CI de quien recibe');
            $table->timestamp('fecha_recepcion')->nullable()->comment('Fecha y hora de recepción');

            // Estados
            $table->enum('estado', ['BORRADOR', 'EMITIDA', 'EN_TRANSITO', 'ENTREGADA', 'ANULADA'])
                ->default('BORRADOR')
                ->comment('Estado de la remisión');

            $table->text('motivo_anulacion')->nullable()->comment('Motivo de anulación si aplica');

            // Auditoría
            $table->unsignedBigInteger('creado_por')->nullable()->comment('Usuario que creó la remisión');
            $table->unsignedBigInteger('actualizado_por')->nullable()->comment('Usuario que actualizó');
            $table->unsignedBigInteger('emitido_por')->nullable()->comment('Usuario que emitió');
            $table->timestamp('emitido_en')->nullable()->comment('Fecha y hora de emisión');
            $table->unsignedBigInteger('anulado_por')->nullable()->comment('Usuario que anuló');
            $table->timestamp('anulado_en')->nullable()->comment('Fecha y hora de anulación');

            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('cliente_id');
            $table->index('factura_id');
            $table->index('pedido_cliente_id');
            $table->index('sucursal_id');
            $table->index('deposito_id');
            $table->index('fecha_emision');
            $table->index('estado');
            $table->index('numero_remision');
        });

        // Tabla de detalle de remisiones
        Schema::create('ventas.REMISIONES_DETALLE', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remision_id')->constrained('ventas.REMISIONES')->onDelete('cascade');

            // Producto
            $table->unsignedBigInteger('producto_id')->comment('Producto remitido');
            $table->string('producto_descripcion')->comment('Descripción del producto');

            // Cantidades
            $table->decimal('cantidad', 15, 2)->comment('Cantidad remitida');
            $table->string('unidad_medida', 20)->nullable()->comment('Unidad de medida');

            // Precios (opcional, para referencia)
            $table->decimal('precio_unitario', 15, 2)->default(0)->comment('Precio unitario de referencia');
            $table->decimal('subtotal', 15, 2)->default(0)->comment('Subtotal de referencia');

            // Relación con factura detalle si aplica
            $table->unsignedBigInteger('factura_detalle_id')->nullable()->comment('Detalle de factura origen');

            // Observaciones
            $table->text('observaciones')->nullable()->comment('Observaciones del ítem');

            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('remision_id');
            $table->index('producto_id');
            $table->index('factura_detalle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.REMISIONES_DETALLE');
        Schema::dropIfExists('ventas.REMISIONES');
    }
};
