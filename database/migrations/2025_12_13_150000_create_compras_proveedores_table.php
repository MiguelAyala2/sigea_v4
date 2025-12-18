<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras.proveedores', function (Blueprint $table) {
            $table->id();

            // Información básica
            $table->string('razon_social', 200);
            $table->string('nombre_fantasia', 200)->nullable();
            $table->string('ruc', 20)->unique();
            $table->string('dv', 2)->nullable(); // Dígito verificador

            // Tipo de proveedor
            $table->enum('tipo_persona', ['FISICA', 'JURIDICA'])->default('FISICA');
            $table->enum('tipo_proveedor', ['PRODUCTOS', 'SERVICIOS', 'AMBOS'])->default('PRODUCTOS');

            // Contacto
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('sitio_web', 200)->nullable();

            // Dirección
            $table->text('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('pais', 100)->default('Paraguay');

            // Información de contacto principal
            $table->string('contacto_nombre', 100)->nullable();
            $table->string('contacto_cargo', 100)->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            $table->string('contacto_email', 100)->nullable();

            // Condiciones comerciales
            $table->integer('dias_plazo_pago')->default(0); // 0 = contado
            $table->decimal('limite_credito', 15, 2)->default(0);
            $table->decimal('descuento_habitual', 5, 2)->default(0);

            // Información bancaria
            $table->string('banco', 100)->nullable();
            $table->string('tipo_cuenta', 50)->nullable();
            $table->string('numero_cuenta', 50)->nullable();

            // Estado y flags
            $table->boolean('activo')->default(true);
            $table->boolean('es_nacional')->default(true);
            $table->boolean('contribuyente')->default(true);

            // Observaciones
            $table->text('observaciones')->nullable();

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('razon_social');
            $table->index('ruc');
            $table->index('activo');
            $table->index('tipo_proveedor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras.proveedores');
    }
};
