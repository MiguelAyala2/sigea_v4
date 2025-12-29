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
        Schema::create('ventas.FACTURAS_SERVICIOS', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('factura_id')->comment('Factura a la que pertenece este servicio');
            $table->unsignedBigInteger('tipo_servicio_id')->nullable()->comment('Tipo de servicio facturado');
            $table->unsignedBigInteger('orden_servicio_id')->nullable()->comment('Orden de servicio relacionada');

            // Datos del servicio
            $table->string('codigo')->comment('Código del servicio');
            $table->text('descripcion')->comment('Descripción del servicio');
            $table->decimal('cantidad', 10, 2)->default(1)->comment('Cantidad del servicio');
            $table->decimal('precio_unitario', 15, 2)->comment('Precio unitario del servicio');

            // Cálculos
            $table->decimal('subtotal', 15, 2)->comment('Subtotal antes de IVA');
            $table->decimal('iva_porcentaje', 5, 2)->default(10)->comment('Porcentaje de IVA');
            $table->decimal('iva_monto', 15, 2)->default(0)->comment('Monto de IVA calculado');
            $table->decimal('total', 15, 2)->comment('Total de la línea');

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('factura_id');
            $table->index('tipo_servicio_id');
            $table->index('orden_servicio_id');

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
        Schema::dropIfExists('ventas.FACTURAS_SERVICIOS');
    }
};
