<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa.EMPRESA', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 200);
            $table->string('nombre_fantasia', 150)->nullable();
            $table->string('ruc', 15)->unique();
            $table->char('dv', 1);
            $table->enum('tipo_contribuyente', ['fisica', 'juridica'])->default('juridica');
            $table->enum('regimen_tributario', ['general', 'simplificado', 'resimple'])->default('general');
            $table->boolean('obligado_factura_electronica')->default(false);
            $table->string('direccion', 255);
            $table->string('departamento', 50);
            $table->string('ciudad', 50);
            $table->string('telefono', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('sitio_web', 100)->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->date('fecha_inicio_actividad')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('creadoPor')->references('id')->on('users')->onDelete('set null');
            $table->foreign('actualizadoPor')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa.EMPRESA');
    }
};
