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
        // Eliminar la restricción antigua y crear una nueva con CREDITO incluido
        DB::statement('
            ALTER TABLE ventas."FACTURAS"
            DROP CONSTRAINT IF EXISTS "FACTURAS_condicion_pago_check"
        ');

        DB::statement('
            ALTER TABLE ventas."FACTURAS"
            ADD CONSTRAINT "FACTURAS_condicion_pago_check"
            CHECK (condicion_pago IN (\'CONTADO\', \'CREDITO\', \'7_DIAS\', \'15_DIAS\', \'30_DIAS\', \'60_DIAS\', \'90_DIAS\'))
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar la restricción original
        DB::statement('
            ALTER TABLE ventas."FACTURAS"
            DROP CONSTRAINT IF EXISTS "FACTURAS_condicion_pago_check"
        ');

        DB::statement('
            ALTER TABLE ventas."FACTURAS"
            ADD CONSTRAINT "FACTURAS_condicion_pago_check"
            CHECK (condicion_pago IN (\'CONTADO\', \'7_DIAS\', \'15_DIAS\', \'30_DIAS\', \'60_DIAS\', \'90_DIAS\'))
        ');
    }
};
