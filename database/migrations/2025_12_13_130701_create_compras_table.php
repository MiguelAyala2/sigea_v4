<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.compras', function (Blueprint $table) {
            $table->id();

            // Relaciones con otros schemas
            $table->unsignedBigInteger('proveedor_id'); // proveedores schema
            $table->unsignedBigInteger('sucursal_id'); // empresa schema
            $table->unsignedBigInteger('deposito_id')->nullable(); // empresa schema
            $table->unsignedBigInteger('timbrado_id')->nullable(); // empresa schema
            $table->unsignedBigInteger('orden_compra_id')->nullable(); // para trazabilidad

            // Datos de la factura
            $table->string('numero_factura', 50);
            $table->string('timbrado', 20)->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();

            // Condiciones comerciales
            $table->enum('condicion_pago', ['CONTADO', '7_DIAS', '15_DIAS', '30_DIAS', '60_DIAS', '90_DIAS'])->default('CONTADO');
            $table->enum('tipo_factura', ['CONTADO', 'CREDITO'])->default('CONTADO');
            $table->enum('tipo_documento', ['FACTURA', 'NOTA_CREDITO', 'NOTA_DEBITO', 'AUTOFACTURA'])->default('FACTURA');

            // Montos
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('iva_10', 15, 2)->default(0);
            $table->decimal('iva_5', 15, 2)->default(0);
            $table->decimal('exenta', 15, 2)->default(0);
            $table->decimal('total_iva', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            // Estado y control
            $table->enum('estado', ['BORRADOR', 'PENDIENTE', 'APROBADA', 'RECHAZADA', 'ANULADA', 'PAGADA', 'PARCIAL'])->default('BORRADOR');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);

            // Factura electrónica
            $table->boolean('es_electronica')->default(false);
            $table->string('cdc', 44)->nullable()->unique(); // Código de control

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('proveedor_id');
            $table->index('sucursal_id');
            $table->index('deposito_id');
            $table->index('timbrado_id');
            $table->index('orden_compra_id');
            $table->index('numero_factura');
            $table->index('fecha_emision');
            $table->index('estado');
            $table->index('tipo_documento');
            $table->index('activo');

            // Constraint única
            $table->unique(['numero_factura', 'timbrado', 'proveedor_id'], 'factura_timbrado_proveedor_unique');

            // NOTA: Foreign keys no se definen aquí por ser cross-schema
            // Se manejarán en los modelos Eloquent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.compras');
    }
};
