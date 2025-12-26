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
        Schema::table('compras.cuentas_por_pagar', function (Blueprint $table) {
            // Agregar campo moneda
            $table->string('moneda', 3)->default('PYG')->after('saldo_pendiente');
        });

        // Modificar ENUM tipo - dropear constraint existente y crear nuevo
        DB::statement("ALTER TABLE compras.cuentas_por_pagar DROP CONSTRAINT IF EXISTS cuentas_por_pagar_tipo_check");
        DB::statement("ALTER TABLE compras.cuentas_por_pagar ALTER COLUMN tipo TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE compras.cuentas_por_pagar ALTER COLUMN tipo SET DEFAULT 'CREDITO'");
        DB::statement("ALTER TABLE compras.cuentas_por_pagar ADD CONSTRAINT cuentas_por_pagar_tipo_check CHECK (tipo IN ('CONTADO', 'CREDITO', 'NOTA_CREDITO', 'NOTA_DEBITO'))");

        // Modificar ENUM estado - dropear constraint existente y crear nuevo
        DB::statement("ALTER TABLE compras.cuentas_por_pagar DROP CONSTRAINT IF EXISTS cuentas_por_pagar_estado_check");
        DB::statement("ALTER TABLE compras.cuentas_por_pagar ALTER COLUMN estado TYPE VARCHAR(30)");
        DB::statement("ALTER TABLE compras.cuentas_por_pagar ALTER COLUMN estado SET DEFAULT 'PENDIENTE'");
        DB::statement("ALTER TABLE compras.cuentas_por_pagar ADD CONSTRAINT cuentas_por_pagar_estado_check CHECK (estado IN ('PENDIENTE', 'PARCIALMENTE_PAGADO', 'PAGADO', 'VENCIDO', 'ANULADO', 'APLICADA'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras.cuentas_por_pagar', function (Blueprint $table) {
            $table->dropColumn('moneda');
        });

        // Revertir ENUMs a valores originales
        DB::statement("ALTER TABLE compras.cuentas_por_pagar DROP CONSTRAINT IF EXISTS cuentas_por_pagar_tipo_check");
        DB::statement("ALTER TABLE compras.cuentas_por_pagar ALTER COLUMN tipo TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE compras.cuentas_por_pagar ADD CONSTRAINT cuentas_por_pagar_tipo_check CHECK (tipo IN ('CONTADO', 'CREDITO'))");

        DB::statement("ALTER TABLE compras.cuentas_por_pagar DROP CONSTRAINT IF EXISTS cuentas_por_pagar_estado_check");
        DB::statement("ALTER TABLE compras.cuentas_por_pagar ALTER COLUMN estado TYPE VARCHAR(30)");
        DB::statement("ALTER TABLE compras.cuentas_por_pagar ADD CONSTRAINT cuentas_por_pagar_estado_check CHECK (estado IN ('PENDIENTE', 'PARCIALMENTE_PAGADO', 'PAGADO', 'VENCIDO', 'ANULADO'))");
    }
};
