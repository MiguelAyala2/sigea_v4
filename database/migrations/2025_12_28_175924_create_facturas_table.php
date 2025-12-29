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
        Schema::create('ventas.FACTURAS', function (Blueprint $table) {
            $table->id();

            // Número de factura
            $table->string('numero_factura', 20)->comment('Número de factura generado');

            // Fechas
            $table->date('fecha_emision')->comment('Fecha de emisión de la factura');
            $table->date('fecha_vencimiento')->nullable()->comment('Fecha de vencimiento para facturas a crédito');

            // Relaciones
            $table->unsignedBigInteger('cliente_id')->comment('Cliente al que se factura');
            $table->unsignedBigInteger('pedido_cliente_id')->nullable()->comment('Pedido del cliente origen (si aplica)');
            $table->unsignedBigInteger('cotizacion_id')->nullable()->comment('Cotización origen (si aplica)');
            $table->unsignedBigInteger('timbrado_id')->comment('Timbrado utilizado');
            $table->unsignedBigInteger('punto_expedicion_id')->comment('Punto de expedición donde se emite');
            $table->string('numero_timbrado', 15)->comment('Copia del número de timbrado');

            // Ubicación
            $table->unsignedBigInteger('sucursal_id')->comment('Sucursal donde se emite');
            $table->unsignedBigInteger('deposito_id')->comment('Depósito desde donde se factura');
            $table->unsignedBigInteger('vendedor_id')->nullable()->comment('Vendedor que realiza la venta');

            // Condiciones de venta
            $table->enum('condicion_pago', ['CONTADO', '7_DIAS', '15_DIAS', '30_DIAS', '60_DIAS', '90_DIAS'])
                ->default('CONTADO')
                ->comment('Condición de pago');
            $table->integer('cantidad_cuotas')->nullable()->comment('Cantidad de cuotas si es a crédito');

            // Totales
            $table->decimal('subtotal', 15, 2)->default(0)->comment('Subtotal antes de impuestos');
            $table->decimal('iva_10', 15, 2)->default(0)->comment('Total IVA 10%');
            $table->decimal('iva_5', 15, 2)->default(0)->comment('Total IVA 5%');
            $table->decimal('exenta', 15, 2)->default(0)->comment('Total de productos exentos');
            $table->decimal('total_iva', 15, 2)->default(0)->comment('Total de IVA (10% + 5%)');
            $table->decimal('descuento_global', 15, 2)->default(0)->comment('Descuento global aplicado');
            $table->decimal('flete', 15, 2)->default(0)->comment('Costo de flete');
            $table->decimal('total', 15, 2)->default(0)->comment('Total de la factura');

            // Facturación electrónica
            $table->boolean('es_electronica')->default(false)->comment('Si es factura electrónica');
            $table->string('cdc', 44)->nullable()->unique()->comment('Código de Control (CDC) para facturas electrónicas');
            $table->text('qr_data')->nullable()->comment('Datos del código QR');
            $table->text('xml_firmado')->nullable()->comment('XML firmado enviado a SET');
            $table->enum('estado_set', ['PENDIENTE', 'APROBADO', 'RECHAZADO', 'ANULADO'])
                ->nullable()
                ->comment('Estado en la SET (solo electrónicas)');
            $table->timestamp('fecha_envio_set')->nullable()->comment('Fecha de envío a SET');
            $table->timestamp('fecha_respuesta_set')->nullable()->comment('Fecha de respuesta de SET');
            $table->text('mensaje_set')->nullable()->comment('Mensaje de respuesta de SET');

            // Estados
            $table->enum('estado', ['BORRADOR', 'EMITIDA', 'PAGADA', 'PARCIALMENTE_PAGADA', 'VENCIDA', 'ANULADA'])
                ->default('BORRADOR')
                ->comment('Estado de la factura');

            // Observaciones y anulación
            $table->text('observaciones')->nullable()->comment('Observaciones generales');
            $table->text('motivo_anulacion')->nullable()->comment('Motivo de anulación si aplica');

            // Auditoría
            $table->unsignedBigInteger('creado_por')->nullable()->comment('Usuario que creó la factura');
            $table->unsignedBigInteger('actualizado_por')->nullable()->comment('Usuario que actualizó la factura');
            $table->unsignedBigInteger('emitido_por')->nullable()->comment('Usuario que emitió la factura');
            $table->timestamp('emitido_en')->nullable()->comment('Fecha y hora de emisión');
            $table->unsignedBigInteger('anulado_por')->nullable()->comment('Usuario que anuló la factura');
            $table->timestamp('anulado_en')->nullable()->comment('Fecha y hora de anulación');

            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('cliente_id');
            $table->index('pedido_cliente_id');
            $table->index('cotizacion_id');
            $table->index('timbrado_id');
            $table->index('punto_expedicion_id');
            $table->index('sucursal_id');
            $table->index('deposito_id');
            $table->index('vendedor_id');
            $table->index('fecha_emision');
            $table->index('estado');
            $table->index('numero_factura');
            $table->index(['es_electronica', 'estado_set']);

            // Índice único compuesto: una factura con el mismo número puede existir en diferentes timbrados
            $table->unique(['numero_factura', 'timbrado_id'], 'ventas_facturas_numero_factura_timbrado_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.FACTURAS');
    }
};
