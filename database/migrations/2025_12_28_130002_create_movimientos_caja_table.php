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
        Schema::create('ventas.MOVIMIENTOS_CAJA', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('apertura_caja_id')->comment('Apertura de caja a la que pertenece');
            $table->unsignedBigInteger('usuario_responsable_id')->comment('Usuario que registra el movimiento');

            // Tipo y concepto
            $table->enum('tipo_movimiento', [
                'INGRESO',
                'EGRESO',
                'DEPOSITO',
                'RETIRO'
            ])->comment('Tipo de movimiento');

            $table->string('concepto', 255)->comment('Concepto/motivo del movimiento');

            // Montos
            $table->decimal('monto', 15, 2)->comment('Monto del movimiento');

            // Forma de pago
            $table->enum('forma_pago', [
                'EFECTIVO',
                'CHEQUE',
                'TARJETA_DEBITO',
                'TARJETA_CREDITO',
                'TRANSFERENCIA',
                'QR',
                'OTRO'
            ])->default('EFECTIVO')->comment('Forma de pago');

            // Referencia
            $table->string('comprobante_numero', 50)->nullable()->comment('Número de comprobante/factura');
            $table->string('referencia', 255)->nullable()->comment('Referencia adicional (nro cheque, código transacción, etc)');

            // Fechas
            $table->date('fecha_movimiento')->comment('Fecha del movimiento');
            $table->time('hora_movimiento')->comment('Hora del movimiento');

            // Relaciones opcionales
            $table->unsignedBigInteger('venta_id')->nullable()->comment('Si el movimiento es por una venta');
            $table->unsignedBigInteger('compra_id')->nullable()->comment('Si el movimiento es por una compra');

            // Control
            $table->boolean('activo')->default(true);

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('apertura_caja_id');
            $table->index('usuario_responsable_id');
            $table->index('tipo_movimiento');
            $table->index('forma_pago');
            $table->index('fecha_movimiento');
            $table->index(['apertura_caja_id', 'tipo_movimiento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.MOVIMIENTOS_CAJA');
    }
};
