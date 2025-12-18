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
        Schema::create('compras.cuentas_por_pagar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras.compras')->onDelete('cascade');
            $table->foreignId('proveedor_id')->constrained('compras.proveedores');
            $table->string('numero_documento', 50); // Número de factura
            $table->string('timbrado', 20)->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->enum('condicion_pago', ['CONTADO', '7_DIAS', '15_DIAS', '30_DIAS', '60_DIAS', '90_DIAS']);
            $table->decimal('monto_total', 15, 2);
            $table->decimal('monto_pagado', 15, 2)->default(0);
            $table->decimal('saldo_pendiente', 15, 2);
            $table->enum('estado', ['PENDIENTE', 'PARCIALMENTE_PAGADO', 'PAGADO', 'VENCIDO', 'ANULADO'])->default('PENDIENTE');
            $table->text('observaciones')->nullable();

            // Auditoría
            $table->foreignId('creadoPor')->nullable()->constrained('users');
            $table->foreignId('actualizadoPor')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('estado');
            $table->index('fecha_vencimiento');
            $table->index('proveedor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras.cuentas_por_pagar');
    }
};
