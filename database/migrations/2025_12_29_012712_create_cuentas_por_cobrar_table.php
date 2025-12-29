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
        Schema::create('ventas.CUENTAS_POR_COBRAR', function (Blueprint $table) {
            $table->id();

            // Relación con factura
            $table->unsignedBigInteger('factura_id')->comment('Factura que genera la cuenta por cobrar');
            $table->string('numero_factura', 50)->comment('Número de factura para referencia rápida');

            // Cliente
            $table->unsignedBigInteger('cliente_id')->comment('Cliente que debe pagar');

            // Fechas
            $table->date('fecha_emision')->comment('Fecha de emisión de la factura');
            $table->date('fecha_vencimiento')->comment('Fecha de vencimiento del pago');

            // Montos
            $table->decimal('monto_total', 15, 2)->comment('Monto total de la factura');
            $table->decimal('monto_pagado', 15, 2)->default(0)->comment('Monto ya pagado');
            $table->decimal('saldo_pendiente', 15, 2)->comment('Saldo pendiente de pago');

            // Estado
            $table->enum('estado', ['PENDIENTE', 'PARCIALMENTE_PAGADA', 'PAGADA', 'VENCIDA', 'ANULADA'])
                ->default('PENDIENTE')
                ->comment('Estado de la cuenta');

            // Observaciones
            $table->text('observaciones')->nullable();

            // Auditoría
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->unsignedBigInteger('actualizado_por')->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('factura_id');
            $table->index('cliente_id');
            $table->index('fecha_vencimiento');
            $table->index('estado');
            $table->unique('factura_id', 'cuentas_cobrar_factura_id_unique');

            // Foreign keys
            $table->foreign('factura_id')
                ->references('id')
                ->on('ventas.FACTURAS')
                ->onDelete('restrict');

            $table->foreign('cliente_id')
                ->references('id')
                ->on('servicios.CLIENTES')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.CUENTAS_POR_COBRAR');
    }
};
