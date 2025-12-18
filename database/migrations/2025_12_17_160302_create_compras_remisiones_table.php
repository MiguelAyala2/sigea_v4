<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.remisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_compra_id')->nullable()->constrained('compras.ordenes_compra')->onDelete('set null');
            $table->foreignId('proveedor_id')->constrained('compras.proveedores')->onDelete('restrict');
            $table->unsignedBigInteger('empresa_id');
            $table->string('numero', 50)->unique();
            $table->date('fecha');
            $table->string('numero_guia_proveedor', 50)->nullable();
            $table->string('transportista', 100)->nullable();
            $table->string('placa_vehiculo', 20)->nullable();
            $table->text('direccion_entrega')->nullable();
            $table->enum('estado', ['pendiente', 'recibida', 'parcial', 'anulada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->foreignId('recibido_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_recepcion')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('empresa_id')->references('id')->on('empresa.EMPRESA')->onDelete('restrict');
            $table->index(['empresa_id', 'fecha']);
            $table->index(['proveedor_id', 'estado']);
            $table->index('numero_guia_proveedor');
        });

        Schema::create('compras.remisiones_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remision_id')->constrained('compras.remisiones')->onDelete('cascade');
            $table->unsignedBigInteger('producto_id');
            $table->text('descripcion');
            $table->decimal('cantidad_enviada', 15, 2)->default(0);
            $table->decimal('cantidad_recibida', 15, 2)->default(0);
            $table->decimal('cantidad_rechazada', 15, 2)->default(0);
            $table->string('unidad_medida', 20)->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('producto_id')->references('id')->on('stock.PRODUCTOS')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.remisiones_detalle');
        Schema::dropIfExists('compras.remisiones');
    }
};
