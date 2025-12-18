<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras.pedidos_compra_detalle', function (Blueprint $table) {
            $table->decimal('iva_porcentaje', 5, 2)->default(10)->after('subtotal_estimado');
        });
    }

    public function down(): void
    {
        Schema::table('compras.pedidos_compra_detalle', function (Blueprint $table) {
            $table->dropColumn('iva_porcentaje');
        });
    }
};
