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
        // Eliminar la columna fecha_entrega_estimada
        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->dropColumn('fecha_entrega_estimada');
        });

        // Modificar condicion_pago y agregar nuevos campos
        DB::statement("ALTER TABLE ventas.pedidos_clientes DROP CONSTRAINT IF EXISTS pedidos_clientes_condicion_pago_check");

        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            // Modificar condicion_pago a solo CONTADO o CREDITO
            $table->dropColumn('condicion_pago');
        });

        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->enum('condicion_pago', ['CONTADO', 'CREDITO'])
                ->default('CONTADO')
                ->after('cotizacion_id')
                ->comment('Forma de pago: Contado o Crédito');

            // Agregar campo de cuotas (solo aplica si es CREDITO)
            $table->integer('cantidad_cuotas')
                ->nullable()
                ->after('condicion_pago')
                ->comment('Número de cuotas si es a crédito (ej: 1, 2, 3, 6, 12, etc.)');

            // Agregar medio de pago
            $table->enum('medio_pago', ['EFECTIVO', 'TARJETA_DEBITO', 'TARJETA_CREDITO', 'CHEQUE'])
                ->default('EFECTIVO')
                ->after('cantidad_cuotas')
                ->comment('Medio de pago utilizado');
        });

        // Eliminar tipo_pedido
        DB::statement("ALTER TABLE ventas.pedidos_clientes DROP CONSTRAINT IF EXISTS pedidos_clientes_tipo_pedido_check");

        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->dropColumn('tipo_pedido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar fecha_entrega_estimada
        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->date('fecha_entrega_estimada')
                ->nullable()
                ->after('fecha_pedido')
                ->comment('Fecha estimada de entrega');
        });

        // Eliminar nuevos campos
        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->dropColumn(['cantidad_cuotas', 'medio_pago']);
        });

        // Restaurar condicion_pago original
        DB::statement("ALTER TABLE ventas.pedidos_clientes DROP CONSTRAINT IF EXISTS pedidos_clientes_condicion_pago_check");

        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->dropColumn('condicion_pago');
        });

        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->enum('condicion_pago', [
                'CONTADO',
                '7_DIAS',
                '15_DIAS',
                '30_DIAS',
                '60_DIAS',
                '90_DIAS'
            ])->default('CONTADO')->after('cotizacion_id')->comment('Forma de pago acordada');
        });

        // Restaurar tipo_pedido
        Schema::table('ventas.pedidos_clientes', function (Blueprint $table) {
            $table->enum('tipo_pedido', [
                'NORMAL',
                'URGENTE',
                'ENTREGA_PROGRAMADA',
                'MAYORISTA',
                'MINORISTA'
            ])->default('NORMAL')->after('condicion_pago')->comment('Tipo de pedido');
        });
    }
};
