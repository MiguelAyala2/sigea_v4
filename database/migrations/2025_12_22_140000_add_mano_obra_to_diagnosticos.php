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
        Schema::table('servicios.DIAGNOSTICOS', function (Blueprint $table) {
            $table->text('mano_obra_descripcion')->nullable()->after('solucion_propuesta');
            $table->decimal('mano_obra_costo', 15, 2)->default(0)->after('mano_obra_descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicios.DIAGNOSTICOS', function (Blueprint $table) {
            $table->dropColumn(['mano_obra_descripcion', 'mano_obra_costo']);
        });
    }
};
