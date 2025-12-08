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
        Schema::create('stock.PRODUCTO_PROVEEDOR', function (Blueprint $table) {
            $table->id();

            // Composite key
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('proveedor_id');

            // Información del proveedor
            $table->string('codigo_proveedor', 100)->nullable();
            $table->decimal('precio_referencia', 12, 2)->nullable();
            $table->integer('tiempo_entrega_dias')->nullable();

            // Flags
            $table->boolean('es_principal')->default(false);
            $table->boolean('activo')->default(true);

            // Historial
            $table->decimal('ultimo_precio_compra', 12, 2)->nullable();
            $table->date('ultima_compra_fecha')->nullable();

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->timestamps();

            // Foreign keys constraints
            $table->foreign('producto_id')
                  ->references('id')
                  ->on('stock.PRODUCTOS')
                  ->onDelete('cascade');

            // Nota: proveedor_id apuntará a empresa.PROVEEDORES cuando se cree ese módulo
            // Por ahora comentamos esta foreign key
            // $table->foreign('proveedor_id')
            //       ->references('id')
            //       ->on('empresa.PROVEEDORES')
            //       ->onDelete('cascade');

            $table->foreign('creadoPor')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('actualizadoPor')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Composite primary key
            $table->unique(['producto_id', 'proveedor_id']);

            // Indices
            $table->index('producto_id');
            $table->index('proveedor_id');
            $table->index('es_principal');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock.PRODUCTO_PROVEEDOR');
    }
};
