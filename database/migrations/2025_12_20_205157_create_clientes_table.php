<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios.CLIENTES', function (Blueprint $table) {
            $table->id();

            // Campos principales
            $table->enum('tipo_cliente', ['fisica', 'juridica']);
            $table->string('documento', 20)->unique();
            $table->string('nombre', 200);

            // Campos de contacto (opcionales)
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 100)->nullable();

            // Dirección (opcional)
            $table->text('direccion')->nullable();

            // Observaciones (opcional)
            $table->text('observaciones')->nullable();

            // Campo de estado
            $table->boolean('activo')->default(true);

            // Campos de auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('creadoPor')
                ->references('id')
                ->on('public.users')
                ->onDelete('set null');

            $table->foreign('actualizadoPor')
                ->references('id')
                ->on('public.users')
                ->onDelete('set null');

            // Índices
            $table->index('tipo_cliente');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios.CLIENTES');
    }
};
