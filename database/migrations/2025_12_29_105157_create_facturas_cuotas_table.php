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
        Schema::create('ventas.FACTURAS_CUOTAS', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('ventas.FACTURAS')->onDelete('cascade');
            $table->integer('numero_cuota');
            $table->decimal('monto', 15, 2);
            $table->date('fecha_vencimiento');
            $table->decimal('monto_pagado', 15, 2)->default(0);
            $table->decimal('saldo_pendiente', 15, 2);
            $table->enum('estado', ['PENDIENTE', 'PARCIALMENTE_PAGADA', 'PAGADA', 'VENCIDA'])->default('PENDIENTE');
            $table->date('fecha_pago')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.FACTURAS_CUOTAS');
    }
};
