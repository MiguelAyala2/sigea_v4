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
        Schema::table('ventas.FACTURAS', function (Blueprint $table) {
            // Eliminar la restricción unique antigua de numero_factura
            $table->dropUnique('ventas_facturas_numero_factura_unique');

            // Agregar restricción unique compuesta (numero_factura + timbrado_id)
            $table->unique(['numero_factura', 'timbrado_id'], 'ventas_facturas_numero_factura_timbrado_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas.FACTURAS', function (Blueprint $table) {
            // Revertir: eliminar unique compuesto
            $table->dropUnique('ventas_facturas_numero_factura_timbrado_unique');

            // Restaurar unique simple en numero_factura
            $table->unique('numero_factura', 'ventas_facturas_numero_factura_unique');
        });
    }
};
