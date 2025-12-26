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
        // Agregar los estados faltantes al enum
        DB::statement("ALTER TYPE compras.compra_estado_enum ADD VALUE IF NOT EXISTS 'BORRADOR'");
        DB::statement("ALTER TYPE compras.compra_estado_enum ADD VALUE IF NOT EXISTS 'APROBADA'");
        DB::statement("ALTER TYPE compras.compra_estado_enum ADD VALUE IF NOT EXISTS 'RECHAZADA'");
        DB::statement("ALTER TYPE compras.compra_estado_enum ADD VALUE IF NOT EXISTS 'ANULADA'");
        DB::statement("ALTER TYPE compras.compra_estado_enum ADD VALUE IF NOT EXISTS 'PAGADA'");
        DB::statement("ALTER TYPE compras.compra_estado_enum ADD VALUE IF NOT EXISTS 'PARCIAL'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se puede remover valores de un enum en PostgreSQL sin recrearlo
        // Por seguridad, no hacemos nada en el down
    }
};
