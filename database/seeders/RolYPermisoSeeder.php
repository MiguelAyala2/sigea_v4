<?php

namespace Database\Seeders;

use App\Models\Admin\Permiso;
use App\Models\Admin\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolYPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============================================
        // PERMISOS DEL SISTEMA
        // ============================================

        $permisos = [
            // SISTEMA - NIVEL 0
            'SuperAdmin',

            // ADMINISTRACIÓN DEL SISTEMA
            'Usuarios Ver',
            'Usuarios Crear',
            'Usuarios Editar',
            'Usuarios Eliminar',
            'Usuarios Asignar Rol',
            'Usuarios Activar/Inactivar',
            'Usuarios Reset Contraseña',

            'Roles Ver',
            'Roles Crear',
            'Roles Editar',
            'Roles Eliminar',

            // MÓDULO DE COMPRAS
            'Compras Ver',
            'Compras Crear',
            'Compras Editar',
            'Compras Eliminar',
            'Compras Aprobar',       // Solo Encargado y Gerencia
            'Compras Revisar',       // Supervisor de Compras

            'Proveedores Ver',
            'Proveedores Crear',
            'Proveedores Editar',
            'Proveedores Eliminar',

            'Ordenes Compra Ver',
            'Ordenes Compra Crear',
            'Ordenes Compra Editar',
            'Ordenes Compra Eliminar',
            'Ordenes Compra Aprobar',

            // MÓDULO DE SERVICIOS
            'Servicios Ver',
            'Servicios Crear',
            'Servicios Editar',
            'Servicios Eliminar',
            'Servicios Aprobar',     // Solo Encargado y Gerencia

            'Instalaciones Ver',
            'Instalaciones Crear',
            'Instalaciones Editar',
            'Instalaciones Ejecutar',    // Supervisor Técnico

            'Ordenes Servicio Ver',
            'Ordenes Servicio Crear',
            'Ordenes Servicio Editar',
            'Ordenes Servicio Aprobar',

            // MÓDULO DE VENTAS
            'Ventas Ver',
            'Ventas Crear',
            'Ventas Editar',
            'Ventas Eliminar',
            'Ventas Aprobar',        // Solo Encargado y Gerencia

            'Clientes Ver',
            'Clientes Crear',
            'Clientes Editar',
            'Clientes Eliminar',

            'Cotizaciones Ver',
            'Cotizaciones Crear',
            'Cotizaciones Editar',
            'Cotizaciones Aprobar',

            'Facturas Ver',
            'Facturas Crear',
            'Facturas Editar',
            'Facturas Anular',

            'Caja Ver',
            'Caja Abrir',
            'Caja Cerrar',
            'Caja Cobrar',           // Cajero

            // MÓDULO DE INVENTARIO (Común a todos)
            'Inventario Ver',
            'Inventario Crear',
            'Inventario Editar',
            'Inventario Eliminar',

            'Productos Ver',
            'Productos Crear',
            'Productos Editar',
            'Productos Eliminar',

            'Stock Ver',
            'Stock Ajustar',

            // MÓDULO DE REPORTES
            'Reportes Ver',
            'Reportes Compras',
            'Reportes Servicios',
            'Reportes Ventas',
            'Reportes Financieros',  // Solo Gerencia

            // CONFIGURACIÓN
            'Configuracion Ver',
            'Configuracion Editar',
        ];

        foreach ($permisos as $permiso) {
            Permiso::create(['name' => $permiso]);
        }

        // ============================================
        // ROLES DEL SISTEMA
        // ============================================

        // NIVEL 0: SUPER ADMINISTRADOR
        $roleSuperAdmin = Rol::create(['name' => 'SuperAdmin']);
        $roleSuperAdmin->syncPermissions(Permiso::all());

        // NIVEL 1: ADMINISTRADOR DEL SISTEMA
        $roleAdminSistema = Rol::create(['name' => 'Administrador del Sistema']);
        $roleAdminSistema->syncPermissions([
            'Usuarios Ver', 'Usuarios Crear', 'Usuarios Editar', 'Usuarios Activar/Inactivar',
            'Usuarios Asignar Rol', 'Usuarios Reset Contraseña',
            'Roles Ver', 'Roles Crear', 'Roles Editar',

            // Acceso total a módulos operativos
            'Compras Ver', 'Compras Crear', 'Compras Editar', 'Compras Aprobar',
            'Proveedores Ver', 'Proveedores Crear', 'Proveedores Editar',
            'Ordenes Compra Ver', 'Ordenes Compra Crear', 'Ordenes Compra Editar', 'Ordenes Compra Aprobar',

            'Servicios Ver', 'Servicios Crear', 'Servicios Editar', 'Servicios Aprobar',
            'Instalaciones Ver', 'Instalaciones Crear', 'Instalaciones Editar', 'Instalaciones Ejecutar',
            'Ordenes Servicio Ver', 'Ordenes Servicio Crear', 'Ordenes Servicio Editar', 'Ordenes Servicio Aprobar',

            'Ventas Ver', 'Ventas Crear', 'Ventas Editar', 'Ventas Aprobar',
            'Clientes Ver', 'Clientes Crear', 'Clientes Editar',
            'Cotizaciones Ver', 'Cotizaciones Crear', 'Cotizaciones Editar', 'Cotizaciones Aprobar',
            'Facturas Ver', 'Facturas Crear', 'Facturas Editar', 'Facturas Anular',
            'Caja Ver', 'Caja Abrir', 'Caja Cerrar', 'Caja Cobrar',

            'Inventario Ver', 'Inventario Crear', 'Inventario Editar',
            'Productos Ver', 'Productos Crear', 'Productos Editar',
            'Stock Ver', 'Stock Ajustar',

            'Reportes Ver', 'Reportes Compras', 'Reportes Servicios', 'Reportes Ventas', 'Reportes Financieros',
            'Configuracion Ver', 'Configuracion Editar',
        ]);

        // NIVEL 2: GERENTE GENERAL
        $roleGerenteGeneral = Rol::create(['name' => 'Gerente General']);
        $roleGerenteGeneral->syncPermissions([
            'Compras Ver', 'Compras Aprobar',
            'Proveedores Ver',
            'Ordenes Compra Ver', 'Ordenes Compra Aprobar',

            'Servicios Ver', 'Servicios Aprobar',
            'Instalaciones Ver',
            'Ordenes Servicio Ver', 'Ordenes Servicio Aprobar',

            'Ventas Ver', 'Ventas Aprobar',
            'Clientes Ver',
            'Cotizaciones Ver', 'Cotizaciones Aprobar',
            'Facturas Ver',
            'Caja Ver',

            'Inventario Ver',
            'Productos Ver',
            'Stock Ver',

            'Reportes Ver', 'Reportes Compras', 'Reportes Servicios', 'Reportes Ventas', 'Reportes Financieros',
        ]);

        // NIVEL 2: SUPERVISOR GENERAL
        $roleSupervisorGeneral = Rol::create(['name' => 'Supervisor General']);
        $roleSupervisorGeneral->syncPermissions([
            'Compras Ver', 'Compras Aprobar',
            'Proveedores Ver',
            'Ordenes Compra Ver', 'Ordenes Compra Aprobar',

            'Servicios Ver', 'Servicios Aprobar',
            'Instalaciones Ver',
            'Ordenes Servicio Ver', 'Ordenes Servicio Aprobar',

            'Ventas Ver', 'Ventas Aprobar',
            'Clientes Ver',
            'Cotizaciones Ver', 'Cotizaciones Aprobar',
            'Facturas Ver',
            'Caja Ver',

            'Inventario Ver',
            'Productos Ver',
            'Stock Ver',

            'Reportes Ver', 'Reportes Compras', 'Reportes Servicios', 'Reportes Ventas',
        ]);

        // ============================================
        // MÓDULO DE COMPRAS
        // ============================================

        // NIVEL 3: ENCARGADO DE COMPRAS (Único Aprobador)
        $roleEncargadoCompras = Rol::create(['name' => 'Encargado de Compras']);
        $roleEncargadoCompras->syncPermissions([
            'Compras Ver', 'Compras Crear', 'Compras Editar', 'Compras Aprobar',
            'Proveedores Ver', 'Proveedores Crear', 'Proveedores Editar',
            'Ordenes Compra Ver', 'Ordenes Compra Crear', 'Ordenes Compra Editar', 'Ordenes Compra Aprobar',
            'Inventario Ver', 'Productos Ver', 'Stock Ver',
            'Reportes Ver', 'Reportes Compras',
        ]);

        // NIVEL 4: SUPERVISOR DE COMPRAS (Revisor)
        $roleSupervisorCompras = Rol::create(['name' => 'Supervisor de Compras']);
        $roleSupervisorCompras->syncPermissions([
            'Compras Ver', 'Compras Crear', 'Compras Editar', 'Compras Revisar',
            'Proveedores Ver', 'Proveedores Crear', 'Proveedores Editar',
            'Ordenes Compra Ver', 'Ordenes Compra Crear', 'Ordenes Compra Editar',
            'Inventario Ver', 'Productos Ver', 'Stock Ver',
            'Reportes Ver', 'Reportes Compras',
        ]);

        // NIVEL 5: ASISTENTE DE COMPRAS (Ejecutor)
        $roleAsistenteCompras = Rol::create(['name' => 'Asistente de Compras']);
        $roleAsistenteCompras->syncPermissions([
            'Compras Ver', 'Compras Crear',
            'Proveedores Ver', 'Proveedores Crear',
            'Ordenes Compra Ver', 'Ordenes Compra Crear',
            'Inventario Ver', 'Productos Ver',
        ]);

        // ============================================
        // MÓDULO DE SERVICIOS
        // ============================================

        // NIVEL 3: ENCARGADO DE SERVICIOS (Único Aprobador)
        $roleEncargadoServicios = Rol::create(['name' => 'Encargado de Servicios']);
        $roleEncargadoServicios->syncPermissions([
            'Servicios Ver', 'Servicios Crear', 'Servicios Editar', 'Servicios Aprobar',
            'Instalaciones Ver', 'Instalaciones Crear', 'Instalaciones Editar', 'Instalaciones Ejecutar',
            'Ordenes Servicio Ver', 'Ordenes Servicio Crear', 'Ordenes Servicio Editar', 'Ordenes Servicio Aprobar',
            'Clientes Ver', 'Clientes Crear', 'Clientes Editar',
            'Inventario Ver', 'Productos Ver', 'Stock Ver',
            'Reportes Ver', 'Reportes Servicios',
        ]);

        // NIVEL 4: SUPERVISOR TÉCNICO (Ejecutor)
        $roleSupervisorTecnico = Rol::create(['name' => 'Supervisor Técnico']);
        $roleSupervisorTecnico->syncPermissions([
            'Servicios Ver', 'Servicios Crear', 'Servicios Editar',
            'Instalaciones Ver', 'Instalaciones Crear', 'Instalaciones Editar', 'Instalaciones Ejecutar',
            'Ordenes Servicio Ver', 'Ordenes Servicio Crear', 'Ordenes Servicio Editar',
            'Clientes Ver',
            'Inventario Ver', 'Productos Ver',
        ]);

        // NIVEL 5: ASISTENTE INSTALADOR (Operativo)
        $roleAsistenteInstalador = Rol::create(['name' => 'Asistente Instalador']);
        $roleAsistenteInstalador->syncPermissions([
            'Servicios Ver',
            'Instalaciones Ver', 'Instalaciones Ejecutar',
            'Ordenes Servicio Ver',
            'Clientes Ver',
            'Productos Ver',
        ]);

        // ============================================
        // MÓDULO DE VENTAS
        // ============================================

        // NIVEL 3: ENCARGADO DE VENTAS (Único Aprobador)
        $roleEncargadoVentas = Rol::create(['name' => 'Encargado de Ventas']);
        $roleEncargadoVentas->syncPermissions([
            'Ventas Ver', 'Ventas Crear', 'Ventas Editar', 'Ventas Aprobar',
            'Clientes Ver', 'Clientes Crear', 'Clientes Editar',
            'Cotizaciones Ver', 'Cotizaciones Crear', 'Cotizaciones Editar', 'Cotizaciones Aprobar',
            'Facturas Ver', 'Facturas Crear', 'Facturas Editar', 'Facturas Anular',
            'Caja Ver', 'Caja Abrir', 'Caja Cerrar',
            'Inventario Ver', 'Productos Ver', 'Stock Ver',
            'Reportes Ver', 'Reportes Ventas',
        ]);

        // NIVEL 4: ASISTENTE DE VENTAS
        $roleAsistenteVentas = Rol::create(['name' => 'Asistente de Ventas']);
        $roleAsistenteVentas->syncPermissions([
            'Ventas Ver', 'Ventas Crear', 'Ventas Editar',
            'Clientes Ver', 'Clientes Crear', 'Clientes Editar',
            'Cotizaciones Ver', 'Cotizaciones Crear', 'Cotizaciones Editar',
            'Facturas Ver', 'Facturas Crear',
            'Inventario Ver', 'Productos Ver', 'Stock Ver',
        ]);

        // NIVEL 5: CAJERO (Especializado)
        $roleCajero = Rol::create(['name' => 'Cajero']);
        $roleCajero->syncPermissions([
            'Ventas Ver',
            'Clientes Ver',
            'Facturas Ver', 'Facturas Crear',
            'Caja Ver', 'Caja Abrir', 'Caja Cerrar', 'Caja Cobrar',
            'Productos Ver',
        ]);

        // ============================================
        // CREAR USUARIO SUPER ADMINISTRADOR
        // ============================================

        $userSuperAdmin = User::create([
            'name' => 'Super Administrador',
            'usuario' => 'superadmin',
            'email' => 'superadmin@sigea.com',
            'nro_cedula' => '0000000',
            'nro_celular' => '0000000000',
            'observacion' => 'SUPER ADMINISTRADOR DEL SISTEMA - ACCESO TOTAL',
            'password' => Hash::make('superadmin123'),
            'activo' => true,
            'ultimo_acceso' => null,
        ]);
        $userSuperAdmin->assignRole($roleSuperAdmin);

        // ============================================
        // CREAR USUARIO ADMINISTRADOR DEL SISTEMA
        // ============================================

        $userAdmin = User::create([
            'name' => 'Administrador',
            'usuario' => 'administrador',
            'email' => 'ronaldalexisniznunez@gmail.com',
            'nro_cedula' => '1234567',
            'nro_celular' => '0981234567',
            'observacion' => 'ADMINISTRADOR DEL SISTEMA',
            'password' => Hash::make('Rann2006'),
            'activo' => true,
            'ultimo_acceso' => null,
        ]);
        $userAdmin->assignRole($roleAdminSistema);

        // ============================================
        // USUARIOS DE EJEMPLO POR ROL
        // ============================================

        // Gerente General
        $userGerente = User::create([
            'name' => 'Juan Pérez',
            'usuario' => 'gerente.general',
            'email' => 'gerente@sigea.com',
            'nro_cedula' => '2000001',
            'nro_celular' => '0981000001',
            'observacion' => 'GERENTE GENERAL',
            'password' => Hash::make('12345678'),
            'activo' => true,
            'ultimo_acceso' => null,
        ]);
        $userGerente->assignRole($roleGerenteGeneral);

        // Encargado de Compras
        $userEncargadoCompras = User::create([
            'name' => 'María González',
            'usuario' => 'encargado.compras',
            'email' => 'compras@sigea.com',
            'nro_cedula' => '3000001',
            'nro_celular' => '0981000002',
            'observacion' => 'ENCARGADO DE COMPRAS',
            'password' => Hash::make('12345678'),
            'activo' => true,
            'ultimo_acceso' => null,
        ]);
        $userEncargadoCompras->assignRole($roleEncargadoCompras);

        // Encargado de Servicios
        $userEncargadoServicios = User::create([
            'name' => 'Carlos Ramírez',
            'usuario' => 'encargado.servicios',
            'email' => 'servicios@sigea.com',
            'nro_cedula' => '4000001',
            'nro_celular' => '0981000003',
            'observacion' => 'ENCARGADO DE SERVICIOS',
            'password' => Hash::make('12345678'),
            'activo' => true,
            'ultimo_acceso' => null,
        ]);
        $userEncargadoServicios->assignRole($roleEncargadoServicios);

        // Encargado de Ventas
        $userEncargadoVentas = User::create([
            'name' => 'Ana Martínez',
            'usuario' => 'encargado.ventas',
            'email' => 'ventas@sigea.com',
            'nro_cedula' => '5000001',
            'nro_celular' => '0981000004',
            'observacion' => 'ENCARGADO DE VENTAS',
            'password' => Hash::make('12345678'),
            'activo' => true,
            'ultimo_acceso' => null,
        ]);
        $userEncargadoVentas->assignRole($roleEncargadoVentas);

        // Cajero
        $userCajero = User::create([
            'name' => 'Luis Torres',
            'usuario' => 'cajero',
            'email' => 'cajero@sigea.com',
            'nro_cedula' => '6000001',
            'nro_celular' => '0981000005',
            'observacion' => 'CAJERO',
            'password' => Hash::make('12345678'),
            'activo' => true,
            'ultimo_acceso' => null,
        ]);
        $userCajero->assignRole($roleCajero);
    }
}
