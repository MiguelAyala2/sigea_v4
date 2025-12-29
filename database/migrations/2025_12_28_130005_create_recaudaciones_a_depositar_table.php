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
        Schema::create('ventas.RECAUDACIONES_A_DEPOSITAR', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('apertura_caja_id')->nullable()->comment('Apertura de caja de origen');
            $table->unsignedBigInteger('cierre_caja_id')->nullable()->comment('Cierre de caja de origen');

            // Información de la recaudación
            $table->date('fecha_recaudacion')->comment('Fecha de la recaudación');

            // Tipo y monto
            $table->enum('tipo_recaudacion', [
                'EFECTIVO',
                'CHEQUE',
                'TARJETA',
                'TRANSFERENCIA',
                'MIXTO'
            ])->comment('Tipo de recaudación');

            $table->decimal('monto', 15, 2)->comment('Monto a depositar');

            // Información bancaria
            $table->string('banco', 100)->nullable()->comment('Banco donde se depositará');
            $table->string('cuenta', 50)->nullable()->comment('Número de cuenta');
            $table->string('numero_documento', 50)->nullable()->comment('Número de cheque o comprobante');

            // Control de depósito
            $table->date('fecha_prevista_deposito')->nullable()->comment('Fecha prevista para depositar');
            $table->date('fecha_real_deposito')->nullable()->comment('Fecha real del depósito');

            // Estado
            $table->enum('estado', [
                'PENDIENTE',
                'DEPOSITADO',
                'RECHAZADO',
                'CANCELADO'
            ])->default('PENDIENTE')->comment('Estado de la recaudación');

            // Responsables
            $table->unsignedBigInteger('usuario_registra_id')->comment('Usuario que registra');
            $table->unsignedBigInteger('usuario_deposita_id')->nullable()->comment('Usuario que deposita');

            // Comprobante de depósito
            $table->string('comprobante_deposito', 100)->nullable()->comment('Número de comprobante del banco');

            // Observaciones
            $table->text('observaciones')->nullable()->comment('Observaciones');
            $table->boolean('activo')->default(true);

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('apertura_caja_id');
            $table->index('cierre_caja_id');
            $table->index('fecha_recaudacion');
            $table->index('estado');
            $table->index('tipo_recaudacion');
            $table->index(['estado', 'fecha_prevista_deposito']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.RECAUDACIONES_A_DEPOSITAR');
    }
};
