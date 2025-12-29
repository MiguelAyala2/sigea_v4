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
        Schema::create('ventas.CIERRES_CAJA', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('apertura_caja_id')->unique()->comment('Apertura de caja que se cierra');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que realiza el cierre');

            // Información de cierre
            $table->date('fecha_cierre')->comment('Fecha de cierre de caja');
            $table->time('hora_cierre')->comment('Hora de cierre');

            // Cálculos de saldo
            $table->decimal('saldo_inicial', 15, 2)->comment('Saldo inicial (copiado de apertura)');
            $table->decimal('total_ingresos', 15, 2)->default(0)->comment('Total de ingresos');
            $table->decimal('total_egresos', 15, 2)->default(0)->comment('Total de egresos');
            $table->decimal('saldo_calculado', 15, 2)->comment('Saldo calculado: inicial + ingresos - egresos');
            $table->decimal('saldo_declarado', 15, 2)->comment('Saldo declarado por el cajero');
            $table->decimal('diferencia', 15, 2)->default(0)->comment('Diferencia: saldo_calculado - saldo_declarado');

            // Desglose por forma de pago
            $table->decimal('total_efectivo', 15, 2)->default(0)->comment('Total en efectivo');
            $table->decimal('total_cheques', 15, 2)->default(0)->comment('Total en cheques');
            $table->decimal('total_tarjetas', 15, 2)->default(0)->comment('Total en tarjetas');
            $table->decimal('total_transferencias', 15, 2)->default(0)->comment('Total en transferencias');
            $table->decimal('total_otros', 15, 2)->default(0)->comment('Total en otros medios');

            // Estado
            $table->enum('estado', [
                'CERRADO_CORRECTO',
                'CERRADO_CON_DIFERENCIA',
                'CERRADO_CON_FALTANTE',
                'CERRADO_CON_SOBRANTE'
            ])->comment('Estado del cierre');

            // Observaciones
            $table->text('observaciones')->nullable()->comment('Observaciones del cierre');
            $table->boolean('activo')->default(true);

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('apertura_caja_id');
            $table->index('usuario_id');
            $table->index('fecha_cierre');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.CIERRES_CAJA');
    }
};
