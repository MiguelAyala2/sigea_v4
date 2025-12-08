<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Crear schema stock si no existe
        DB::statement('CREATE SCHEMA IF NOT EXISTS stock');
    }

    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS stock CASCADE');
    }
};
