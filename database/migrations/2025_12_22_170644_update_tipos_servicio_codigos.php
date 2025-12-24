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
        // Obtener todos los tipos de servicio ordenados por ID
        $tiposServicio = DB::table('servicios.TIPOS_SERVICIO')
            ->orderBy('id', 'asc')
            ->get();

        // Actualizar cada código al nuevo formato TDS001, TDS002, etc.
        foreach ($tiposServicio as $index => $tipo) {
            $nuevoCodigo = 'TDS' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            DB::table('servicios.TIPOS_SERVICIO')
                ->where('id', $tipo->id)
                ->update(['codigo' => $nuevoCodigo]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir, ya que los códigos antiguos se perdieron
    }
};
