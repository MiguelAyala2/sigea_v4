<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class EliminarPermisosLegados extends Command
{
    protected $signature = 'permisos:eliminar-legados {--force : Eliminar sin confirmación}';
    protected $description = 'Elimina los permisos legados (sin formato modulo.entidad.accion)';

    public function handle()
    {
        $this->info('🔍 Buscando permisos legados...');
        $this->newLine();

        // Obtener permisos que NO tienen el formato nuevo (sin punto en el nombre)
        // Y que no sean SuperAdmin
        $legados = Permission::where('name', 'NOT LIKE', '%.%')
            ->where('name', '!=', 'SuperAdmin')
            ->orderBy('name')
            ->get();

        if ($legados->isEmpty()) {
            $this->info('✅ No se encontraron permisos legados para eliminar.');
            return 0;
        }

        $this->warn("Se encontraron {$legados->count()} permisos legados:");
        $this->newLine();

        // Mostrar lista de permisos a eliminar
        foreach ($legados as $permiso) {
            $this->line("  - {$permiso->name}");
        }

        $this->newLine();

        // Confirmación
        if (!$this->option('force')) {
            if (!$this->confirm('¿Está seguro que desea eliminar estos permisos?', false)) {
                $this->info('❌ Operación cancelada.');
                return 0;
            }
        }

        // Eliminar permisos
        $this->info('🗑️  Eliminando permisos legados...');

        $count = 0;
        foreach ($legados as $permiso) {
            $permiso->delete();
            $count++;
        }

        // Limpiar caché de permisos
        $this->info('🧹 Limpiando caché de permisos...');
        \Artisan::call('permission:cache-reset');

        $this->newLine();
        $this->info("✅ Se eliminaron {$count} permisos legados exitosamente.");
        $this->info('✅ Caché de permisos limpiada.');

        return 0;
    }
}
