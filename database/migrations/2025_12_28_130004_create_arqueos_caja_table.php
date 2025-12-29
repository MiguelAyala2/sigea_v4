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
        Schema::create('ventas.ARQUEOS_CAJA', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('apertura_caja_id')->comment('Apertura de caja a la que pertenece');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que realiza el arqueo');

            // Información del arqueo
            $table->date('fecha_arqueo')->comment('Fecha del arqueo');
            $table->time('hora_arqueo')->comment('Hora del arqueo');

            // Moneda
            $table->string('moneda', 10)->default('PYG')->comment('Moneda del arqueo');

            // Billetes en Guaraníes (Paraguay)
            $table->integer('cantidad_billetes_100000')->default(0)->comment('Cantidad de billetes de 100.000 Gs.');
            $table->integer('cantidad_billetes_50000')->default(0)->comment('Cantidad de billetes de 50.000 Gs.');
            $table->integer('cantidad_billetes_20000')->default(0)->comment('Cantidad de billetes de 20.000 Gs.');
            $table->integer('cantidad_billetes_10000')->default(0)->comment('Cantidad de billetes de 10.000 Gs.');
            $table->integer('cantidad_billetes_5000')->default(0)->comment('Cantidad de billetes de 5.000 Gs.');
            $table->integer('cantidad_billetes_2000')->default(0)->comment('Cantidad de billetes de 2.000 Gs.');

            // Monedas en Guaraníes
            $table->integer('cantidad_monedas_1000')->default(0)->comment('Cantidad de monedas de 1.000 Gs.');
            $table->integer('cantidad_monedas_500')->default(0)->comment('Cantidad de monedas de 500 Gs.');
            $table->integer('cantidad_monedas_100')->default(0)->comment('Cantidad de monedas de 100 Gs.');
            $table->integer('cantidad_monedas_50')->default(0)->comment('Cantidad de monedas de 50 Gs.');

            // Totales calculados
            $table->decimal('subtotal_billetes', 15, 2)->default(0)->comment('Subtotal de billetes');
            $table->decimal('subtotal_monedas', 15, 2)->default(0)->comment('Subtotal de monedas');
            $table->decimal('total_arqueo', 15, 2)->comment('Total del arqueo físico');

            // Otros valores
            $table->decimal('cheques_recibidos', 15, 2)->default(0)->comment('Total de cheques recibidos');
            $table->integer('cantidad_cheques')->default(0)->comment('Cantidad de cheques');

            // Observaciones
            $table->text('observaciones')->nullable()->comment('Observaciones del arqueo');
            $table->boolean('activo')->default(true);

            // Auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('apertura_caja_id');
            $table->index('usuario_id');
            $table->index('fecha_arqueo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas.ARQUEOS_CAJA');
    }
};
