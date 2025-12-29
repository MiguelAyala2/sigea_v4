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
        Schema::create('ventas.NOTAS_DEBITO', function (Blueprint $table) {
            $table->id();

            // Número de nota
            $table->string('numero_nota', 20)->unique()->comment('Número de nota de débito generado');

            // Relaciones
            $table->unsignedBigInteger('factura_id')->comment('Factura a la que aplica la nota de débito');
            $table->unsignedBigInteger('cliente_id')->comment('Cliente de la factura');
            $table->unsignedBigInteger('timbrado_id')->comment('Timbrado utilizado');
            $table->unsignedBigInteger('punto_expedicion_id')->comment('Punto de expedición donde se emite');
            $table->string('numero_timbrado', 15)->comment('Copia del número de timbrado');

            // Fechas
            $table->date('fecha_emision')->comment('Fecha de emisión de la nota de débito');

            // Motivo
            $table->text('motivo')->comment('Motivo de la nota de débito');

            // Totales
            $table->decimal('subtotal', 15, 2)->default(0)->comment('Subtotal antes de impuestos');
            $table->decimal('iva_10', 15, 2)->default(0)->comment('Total IVA 10%');
            $table->decimal('iva_5', 15, 2)->default(0)->comment('Total IVA 5%');
            $table->decimal('exenta', 15, 2)->default(0)->comment('Total de productos exentos');
            $table->decimal('total_iva', 15, 2)->default(0)->comment('Total de IVA (10% + 5%)');
            $table->decimal('total', 15, 2)->default(0)->comment('Total de la nota de débito');

            // Facturación electrónica
            $table->boolean('es_electronica')->default(false)->comment('Si es nota de débito electrónica');
            $table->string('cdc', 44)->nullable()->unique()->comment('Código de Control (CDC)');
            $table->text('qr_data')->nullable()->comment('Datos del código QR');
            $table->text('xml_firmado')->nullable()->comment('XML firmado enviado a SET');
            $table->enum('estado_set', ['PENDIENTE', 'APROBADO', 'RECHAZADO', 'ANULADO'])
                ->nullable()
                ->comment('Estado en la SET (solo electrónicas)');
            $table->timestamp('fecha_envio_set')->nullable()->comment('Fecha de envío a SET');
            $table->timestamp('fecha_respuesta_set')->nullable()->comment('Fecha de respuesta de SET');
            $table->text('mensaje_set')->nullable()->comment('Mensaje de respuesta de SET');

            // Estados
            $table->enum('estado', ['BORRADOR', 'EMITIDA', 'APLICADA', 'ANULADA'])
                ->default('BORRADOR')
                ->comment('Estado de la nota de débito');

            // Observaciones
            $table->text('observaciones')->nullable()->comment('Observaciones generales');
            $table->text('motivo_anulacion')->nullable()->comment('Motivo de anulación si aplica');

            // Auditoría
            $table->unsignedBigInteger('creado_por')->nullable()->comment('Usuario que creó la ND');
            $table->unsignedBigInteger('actualizado_por')->nullable()->comment('Usuario que actualizó la ND');
            $table->unsignedBigInteger('emitido_por')->nullable()->comment('Usuario que emitió la ND');
            $table->timestamp('emitido_en')->nullable()->comment('Fecha y hora de emisión');
            $table->unsignedBigInteger('anulado_por')->nullable()->comment('Usuario que anuló la ND');
            $table->timestamp('anulado_en')->nullable()->comment('Fecha y hora de anulación');

            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('factura_id');
            $table->index('cliente_id');
            $table->index('timbrado_id');
            $table->index('punto_expedicion_id');
            $table->index('fecha_emision');
            $table->index('estado');
            $table->index('numero_nota');
            $table->index(['es_electronica', 'estado_set']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.NOTAS_DEBITO');
    }
};
