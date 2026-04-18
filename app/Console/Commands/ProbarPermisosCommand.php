<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProbarPermisosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permisos:probar {usuario?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar permisos y roles del sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== PRUEBA DEL SISTEMA DE PERMISOS ===');
        $this->newLine();

        $usuarioNombre = $this->argument('usuario');

        if ($usuarioNombre) {
            $this->probarUsuarioEspecifico($usuarioNombre);
        } else {
            $this->mostrarResumen();
        }

        return 0;
    }

    /**
     * Probar un usuario específico
     */
    protected function probarUsuarioEspecifico($usuarioNombre)
    {
        $usuario = User::where('usuario', $usuarioNombre)->first();

        if (!$usuario) {
            $this->error("Usuario '$usuarioNombre' no encontrado");
            $this->newLine();
            $this->info('Usuarios disponibles:');
            User::all()->each(function ($u) {
                $this->line("  - {$u->usuario} ({$u->name})");
            });
            return;
        }

        $this->info("Usuario: {$usuario->name} ({$usuario->usuario})");
        $this->info("Email: {$usuario->email}");
        $this->newLine();

        // Mostrar roles
        $this->info('Roles asignados:');
        $roles = $usuario->roles;
        if ($roles->count() > 0) {
            $roles->each(function ($rol) {
                $this->line("  - {$rol->name}");
            });
        } else {
            $this->warn('  (Sin roles asignados)');
        }
        $this->newLine();

        // Mostrar permisos directos
        $this->info('Permisos directos:');
        $permisos = $usuario->permissions;
        if ($permisos->count() > 0) {
            $permisos->each(function ($permiso) {
                $this->line("  - {$permiso->name}");
            });
        } else {
            $this->warn('  (Sin permisos directos)');
        }
        $this->newLine();

        // Mostrar permisos heredados de roles
        $this->info('Permisos por roles:');
        $permisosRoles = $usuario->getAllPermissions();
        if ($permisosRoles->count() > 0) {
            $modulos = $permisosRoles->groupBy(function ($permiso) {
                $parts = explode('.', $permiso->name);
                return $parts[0] ?? 'Otros';
            });

            foreach ($modulos as $modulo => $perms) {
                $this->line("  {$modulo}: ({$perms->count()} permisos)");
                if ($this->option('verbose')) {
                    $perms->each(function ($p) {
                        $this->line("    - {$p->name}");
                    });
                }
            }
        } else {
            $this->warn('  (Sin permisos)');
        }
        $this->newLine();

        // Probar permisos específicos
        $this->info('Probando permisos clave:');
        $permisosProbar = [
            'compras.*' => 'Acceso completo a compras (wildcard)',
            'compras.compras.ver' => 'Ver compras',
            'compras.compras.crear' => 'Crear compras',
            'compras.compras.aprobar' => 'Aprobar compras',
            'ventas.facturas.crear' => 'Crear facturas',
            'admin.usuarios.ver' => 'Ver usuarios',
        ];

        foreach ($permisosProbar as $permiso => $descripcion) {
            $tiene = $usuario->can($permiso);
            $icono = $tiene ? '✓' : '✗';
            $color = $tiene ? 'info' : 'comment';
            $this->$color("  {$icono} {$descripcion}: {$permiso}");
        }
    }

    /**
     * Mostrar resumen general del sistema
     */
    protected function mostrarResumen()
    {
        // Resumen de permisos
        $this->info('TOTAL DE PERMISOS:');
        $totalPermisos = Permission::count();
        $this->line("  Total: {$totalPermisos}");

        $permisosPorModulo = Permission::all()->groupBy(function ($permiso) {
            $parts = explode('.', $permiso->name);
            return $parts[0] ?? 'Otros';
        });

        $this->newLine();
        $this->info('Permisos por módulo:');
        foreach ($permisosPorModulo as $modulo => $permisos) {
            $this->line("  - {$modulo}: {$permisos->count()} permisos");
        }

        // Resumen de roles
        $this->newLine();
        $this->info('TOTAL DE ROLES:');
        $totalRoles = Role::count();
        $this->line("  Total: {$totalRoles}");
        $this->newLine();

        $this->info('Roles disponibles:');
        Role::all()->each(function ($rol) {
            $cantPermisos = $rol->permissions->count();
            $this->line("  - {$rol->name} ({$cantPermisos} permisos)");
        });

        // Resumen de usuarios
        $this->newLine();
        $this->info('USUARIOS DEL SISTEMA:');
        User::with('roles')->get()->each(function ($usuario) {
            $rolesNames = $usuario->roles->pluck('name')->join(', ');
            $this->line("  - {$usuario->usuario} ({$usuario->name}) → {$rolesNames}");
        });

        $this->newLine();
        $this->comment('Uso: php artisan permisos:probar {usuario}');
        $this->comment('Ejemplo: php artisan permisos:probar superadmin');
    }
}
