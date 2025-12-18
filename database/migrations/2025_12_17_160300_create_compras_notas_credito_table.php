<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.notas_credito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->nullable()->constrained('compras.compras')->onDelete('set null');
            $table->foreignId('proveedor_id')->constrained('compras.proveedores')->onDelete('restrict');
            $table->unsignedBigInteger('empresa_id');
            $table->string('numero', 50)->unique();
            $table->date('fecha');
            $table->string('numero_factura_afectada', 50)->nullable();
            $table->text('motivo');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('impuesto', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('estado', ['borrador', 'aplicada', 'anulada'])->default('borrador');
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('empresa_id')->references('id')->on('empresa.EMPRESA')->onDelete('restrict');
            $table->index(['empresa_id', 'fecha']);
            $table->index(['proveedor_id', 'estado']);
        });

        Schema::create('compras.notas_credito_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_credito_id')->constrained('compras.notas_credito')->onDelete('cascade');
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->text('descripcion');
            $table->decimal('cantidad', 15, 2)->default(0);
            $table->decimal('precio_unitario', 15, 2)->default(0);
            $table->decimal('descuento', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('impuesto', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('producto_id')->references('id')->on('stock.PRODUCTOS')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.notas_credito_detalle');
        Schema::dropIfExists('compras.notas_credito');
    }
};
