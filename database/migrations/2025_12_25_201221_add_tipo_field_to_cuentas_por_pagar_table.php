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
        Schema::table('compras.cuentas_por_pagar', function (Blueprint $table) {
            $table->enum('tipo', ['CONTADO', 'CREDITO'])->default('CREDITO')->after('timbrado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras.cuentas_por_pagar', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
