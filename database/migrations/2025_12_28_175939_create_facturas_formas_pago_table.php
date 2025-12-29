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
        Schema::create('ventas.FACTURAS_FORMAS_PAGO', function (Blueprint $table) {
            $table->id();

            // Relación
            $table->unsignedBigInteger('factura_id')->comment('Factura a la que pertenece esta forma de pago');

            // Forma de pago
            $table->enum('forma_pago', [
                'EFECTIVO',
                'CHEQUE',
                'TARJETA_DEBITO',
                'TARJETA_CREDITO',
                'TRANSFERENCIA',
                'QR',
                'OTRO'
            ])->comment('Forma de pago utilizada');

            // Monto
            $table->decimal('monto', 15, 2)->comment('Monto pagado con esta forma de pago');

            // Información adicional según forma de pago
            $table->string('referencia', 100)->nullable()->comment('Número de cheque, voucher, etc.');
            $table->string('banco', 100)->nullable()->comment('Banco emisor (para cheques o transferencias)');
            $table->date('fecha_pago')->nullable()->comment('Fecha del pago (para cheques posfechados)');
            $table->text('observaciones')->nullable()->comment('Observaciones adicionales');

            $table->timestamps();

            // Índices
            $table->index('factura_id');
            $table->index('forma_pago');

            // Foreign key con cascade
            $table->foreign('factura_id')
                ->references('id')
                ->on('ventas.FACTURAS')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.FACTURAS_FORMAS_PAGO');
    }
};
