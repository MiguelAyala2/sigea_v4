<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear schema de ventas
        DB::statement('CREATE SCHEMA IF NOT EXISTS ventas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar schema de ventas (solo si está vacío)
        DB::statement('DROP SCHEMA IF EXISTS ventas CASCADE');
    }
};
