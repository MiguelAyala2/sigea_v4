<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar condicion_pago
        DB::statement("ALTER TABLE ventas.pedidos_clientes DROP CONSTRAINT IF EXISTS pedidos_clientes_condicion_pago_check");

        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->dropColumn(['condicion_pago', 'cantidad_cuotas']);
        });

        // Eliminar medio_pago
        DB::statement("ALTER TABLE ventas.pedidos_clientes DROP CONSTRAINT IF EXISTS pedidos_clientes_medio_pago_check");

        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->dropColumn('medio_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->enum('condicion_pago', ['CONTADO', 'CREDITO'])
                ->default('CONTADO')
                ->after('cotizacion_id')
                ->comment('Forma de pago: Contado o Crédito');

            $table->integer('cantidad_cuotas')
                ->nullable()
                ->after('condicion_pago')
                ->comment('Número de cuotas si es a crédito');

            $table->enum('medio_pago', ['EFECTIVO', 'TARJETA_DEBITO', 'TARJETA_CREDITO', 'CHEQUE'])
                ->default('EFECTIVO')
                ->after('cantidad_cuotas')
                ->comment('Medio de pago utilizado');
        });
    }
};
