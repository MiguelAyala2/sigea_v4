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
        // Lista de tablas del sistema que deben estar en public
        $systemTables = [
            'SYS_MODULOS',
            'SYS_SUB_MODULOS',
            'audits',
            'cache',
            'cache_locks',
            'failed_jobs',
            'job_batches',
            'jobs',
            'migrations',
            'model_has_permissions',
            'model_has_roles',
            'password_reset_tokens',
            'permissions',
            'role_has_permissions',
            'roles',
            'sessions',
            'users',
        ];

        foreach ($systemTables as $table) {
            // Verificar si la tabla existe en el schema empresa
            $exists = DB::select("
                SELECT EXISTS (
                    SELECT FROM information_schema.tables
                    WHERE table_schema = 'empresa'
                    AND table_name = ?
                )
            ", [$table]);

            if ($exists[0]->exists) {
                // Mover la tabla de empresa a public
                DB::statement("ALTER TABLE empresa.\"{$table}\" SET SCHEMA public");
                echo "Tabla {$table} movida de empresa a public\n";
            } else {
                echo "Tabla {$table} no encontrada en schema empresa, verificando en public...\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Lista de tablas del sistema
        $systemTables = [
            'SYS_MODULOS',
            'SYS_SUB_MODULOS',
            'audits',
            'cache',
            'cache_locks',
            'failed_jobs',
            'job_batches',
            'jobs',
            'migrations',
            'model_has_permissions',
            'model_has_roles',
            'password_reset_tokens',
            'permissions',
            'role_has_permissions',
            'roles',
            'sessions',
            'users',
        ];

        foreach ($systemTables as $table) {
            // Verificar si la tabla existe en el schema public
            $exists = DB::select("
                SELECT EXISTS (
                    SELECT FROM information_schema.tables
                    WHERE table_schema = 'public'
                    AND table_name = ?
                )
            ", [$table]);

            if ($exists[0]->exists) {
                // Mover la tabla de public a empresa (rollback)
                DB::statement("ALTER TABLE public.\"{$table}\" SET SCHEMA empresa");
            }
        }
    }
};
