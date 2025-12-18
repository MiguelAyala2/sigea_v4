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

            'Stock Ver',
            'Stock Ajustar',

            // MÓDULO DE STOCKS (Productos, Categorías y Marcas)
            'Stocks Ver',
            'Stocks Crear',
            'Stocks Editar',
            'Stocks Eliminar',
            'Productos Ver',
            'Productos Crear',
            'Productos Editar',
            'Productos Eliminar',
            'Categorias Ver',
            'Categorias Crear',
            'Categorias Editar',
            'Categorias Eliminar',
            'Marcas Ver',
            'Marcas Crear',
            'Marcas Editar',
            'Marcas Eliminar',

            // PERMISOS MÓDULO COMPRAS (nuevos)
            'compras.ver',
            'compras.dashboard',
            'compras.compras.ver',
            'compras.compras.crear',
            'compras.compras.editar',
            'compras.compras.eliminar',
            'compras.compras.anular',
            'compras.recepciones.ver',
            'compras.recepciones.crear',
            'compras.recepciones.editar',
            'compras.recepciones.eliminar',
            'compras.aprobaciones.ver',
            'compras.aprobaciones.aprobar',
            'compras.aprobaciones.rechazar',
            'compras.reportes.ver',
            'compras.reportes.libro',
            'compras.reportes.analisis',
            'compras.reportes.flujo',

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
            Permiso::firstOrCreate(
                ['name' => $permiso],
                ['guard_name' => 'web']
            );
        }

        // ============================================
        // ROLES DEL SISTEMA
        // ============================================

        // NIVEL 0: SUPER ADMINISTRADOR
        $roleSuperAdmin = Rol::firstOrCreate(['name' => 'SuperAdmin'], ['guard_name' => 'web']);
        $roleSuperAdmin->syncPermissions(Permiso::all());

        // NIVEL 1: ADMINISTRADOR DEL SISTEMA
        $roleAdminSistema = Rol::firstOrCreate(['name' => 'Administrador del Sistema'], ['guard_name' => 'web']);
        $roleAdminSistema->syncPermissions([
            'Usuarios Ver', 'Usuarios Crear', 'Usuarios Editar', 'Usuarios Activar/Inactivar',
            'Usuarios Asignar Rol', 'Usuarios Reset Contraseña',
            'Roles Ver', 'Roles Crear', 'Roles Editar',

            // Acceso total a módulos operativos
            'Compras Ver', 'Compras Crear', 'Compras Editar', 'Compras Aprobar',
            'Proveedores Ver', 'Proveedores Crear', 'Proveedores Editar',
            'Ordenes Compra Ver', 'Ordenes Compra Crear', 'Ordenes Compra Editar', 'Ordenes Compra Aprobar',

            // Permisos nuevos módulo Compras
            'compras.ver', 'compras.dashboard',
            'compras.compras.ver', 'compras.compras.crear', 'compras.compras.editar', 'compras.compras.eliminar', 'compras.compras.anular',
            'compras.recepciones.ver', 'compras.recepciones.crear', 'compras.recepciones.editar', 'compras.recepciones.eliminar',
            'compras.aprobaciones.ver', 'compras.aprobaciones.aprobar', 'compras.aprobaciones.rechazar',
            'compras.reportes.ver', 'compras.reportes.libro', 'compras.reportes.analisis', 'compras.reportes.flujo',

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

            // Módulo Stocks
            'Stocks Ver', 'Stocks Crear', 'Stocks Editar', 'Stocks Eliminar',
            'Productos Ver', 'Productos Crear', 'Productos Editar', 'Productos Eliminar',
            'Categorias Ver', 'Categorias Crear', 'Categorias Editar', 'Categorias Eliminar',
            'Marcas Ver', 'Marcas Crear', 'Marcas Editar', 'Marcas Eliminar',

            'Reportes Ver', 'Reportes Compras', 'Reportes Servicios', 'Reportes Ventas', 'Reportes Financieros',
            'Configuracion Ver', 'Configuracion Editar',
        ]);

        // NIVEL 2: GERENTE GENERAL
        $roleGerenteGeneral = Rol::firstOrCreate(['name' => 'Gerente General'], ['guard_name' => 'web']);
        $roleGerenteGeneral->syncPermissions([
            'Compras Ver', 'Compras Aprobar',
            'Proveedores Ver',
            'Ordenes Compra Ver', 'Ordenes Compra Aprobar',

            // Permisos nuevos módulo Compras (Gerente General - solo ver y aprobar)
            'compras.ver', 'compras.dashboard',
            'compras.compras.ver',
            'compras.recepciones.ver',
            'compras.aprobaciones.ver', 'compras.aprobaciones.aprobar', 'compras.aprobaciones.rechazar',
            'compras.reportes.ver', 'compras.reportes.libro', 'compras.reportes.analisis', 'compras.reportes.flujo',

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

            // Módulo Stocks (solo lectura)
            'Stocks Ver',
            'Productos Ver',
            'Categorias Ver',
            'Marcas Ver',

            'Reportes Ver', 'Reportes Compras', 'Reportes Servicios', 'Reportes Ventas', 'Reportes Financieros',
        ]);

        // NIVEL 2: SUPERVISOR GENERAL
        $roleSupervisorGeneral = Rol::firstOrCreate(['name' => 'Supervisor General'], ['guard_name' => 'web']);
        $roleSupervisorGeneral->syncPermissions([
            'Compras Ver', 'Compras Aprobar',
            'Proveedores Ver',
            'Ordenes Compra Ver', 'Ordenes Compra Aprobar',

            // Permisos nuevos módulo Compras (Supervisor General - ver y aprobar)
            'compras.ver', 'compras.dashboard',
            'compras.compras.ver',
            'compras.recepciones.ver',
            'compras.aprobaciones.ver', 'compras.aprobaciones.aprobar', 'compras.aprobaciones.rechazar',
            'compras.reportes.ver', 'compras.reportes.libro', 'compras.reportes.analisis', 'compras.reportes.flujo',

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

            // Módulo Stocks (solo lectura)
            'Stocks Ver',
            'Productos Ver',
            'Categorias Ver',
            'Marcas Ver',

            'Reportes Ver', 'Reportes Compras', 'Reportes Servicios', 'Reportes Ventas',
        ]);

        // ============================================
        // MÓDULO DE COMPRAS
        // ============================================

        // NIVEL 3: ENCARGADO DE COMPRAS (Único Aprobador)
        $roleEncargadoCompras = Rol::firstOrCreate(['name' => 'Encargado de Compras'], ['guard_name' => 'web']);
        $roleEncargadoCompras->syncPermissions([
            'Compras Ver', 'Compras Crear', 'Compras Editar', 'Compras Aprobar',
            'Proveedores Ver', 'Proveedores Crear', 'Proveedores Editar',
            'Ordenes Compra Ver', 'Ordenes Compra Crear', 'Ordenes Compra Editar', 'Ordenes Compra Aprobar',

            // Permisos nuevos módulo Compras (acceso completo)
            'compras.ver', 'compras.dashboard',
            'compras.compras.ver', 'compras.compras.crear', 'compras.compras.editar', 'compras.compras.eliminar', 'compras.compras.anular',
            'compras.recepciones.ver', 'compras.recepciones.crear', 'compras.recepciones.editar', 'compras.recepciones.eliminar',
            'compras.aprobaciones.ver', 'compras.aprobaciones.aprobar', 'compras.aprobaciones.rechazar',
            'compras.reportes.ver', 'compras.reportes.libro', 'compras.reportes.analisis', 'compras.reportes.flujo',

            'Inventario Ver', 'Productos Ver', 'Stock Ver',
            // Módulo Stocks (acceso completo)
            'Stocks Ver', 'Stocks Crear', 'Stocks Editar',
            'Productos Ver', 'Productos Crear', 'Productos Editar',
            'Categorias Ver', 'Categorias Crear', 'Categorias Editar',
            'Marcas Ver', 'Marcas Crear', 'Marcas Editar',
            'Reportes Ver', 'Reportes Compras',
        ]);

        // NIVEL 4: SUPERVISOR DE COMPRAS (Revisor)
        $roleSupervisorCompras = Rol::firstOrCreate(['name' => 'Supervisor de Compras'], ['guard_name' => 'web']);
        $roleSupervisorCompras->syncPermissions([
            'Compras Ver', 'Compras Crear', 'Compras Editar', 'Compras Revisar',
            'Proveedores Ver', 'Proveedores Crear', 'Proveedores Editar',
            'Ordenes Compra Ver', 'Ordenes Compra Crear', 'Ordenes Compra Editar',

            // Permisos nuevos módulo Compras (sin eliminar/anular)
            'compras.ver', 'compras.dashboard',
            'compras.compras.ver', 'compras.compras.crear', 'compras.compras.editar',
            'compras.recepciones.ver', 'compras.recepciones.crear', 'compras.recepciones.editar',
            'compras.aprobaciones.ver',
            'compras.reportes.ver', 'compras.reportes.libro', 'compras.reportes.analisis',

            'Inventario Ver', 'Productos Ver', 'Stock Ver',
            // Módulo Stocks
            'Stocks Ver', 'Stocks Crear', 'Stocks Editar',
            'Productos Ver', 'Productos Crear', 'Productos Editar',
            'Categorias Ver', 'Categorias Crear', 'Categorias Editar',
            'Marcas Ver', 'Marcas Crear', 'Marcas Editar',
            'Reportes Ver', 'Reportes Compras',
        ]);

        // NIVEL 5: ASISTENTE DE COMPRAS (Ejecutor)
        $roleAsistenteCompras = Rol::firstOrCreate(['name' => 'Asistente de Compras'], ['guard_name' => 'web']);
        $roleAsistenteCompras->syncPermissions([
            'Compras Ver', 'Compras Crear',
            'Proveedores Ver', 'Proveedores Crear',
            'Ordenes Compra Ver', 'Ordenes Compra Crear',

            // Permisos nuevos módulo Compras (solo ver y crear)
            'compras.ver', 'compras.dashboard',
            'compras.compras.ver', 'compras.compras.crear',
            'compras.recepciones.ver', 'compras.recepciones.crear',
            'compras.aprobaciones.ver',

            'Inventario Ver', 'Productos Ver',
            // Módulo Stocks (solo lectura)
            'Stocks Ver',
            'Productos Ver',
            'Categorias Ver',
            'Marcas Ver',
        ]);

        // ============================================
        // MÓDULO DE SERVICIOS
        // ============================================

        // NIVEL 3: ENCARGADO DE SERVICIOS (Único Aprobador)
        $roleEncargadoServicios = Rol::firstOrCreate(['name' => 'Encargado de Servicios'], ['guard_name' => 'web']);
        $roleEncargadoServicios->syncPermissions([
            'Servicios Ver', 'Servicios Crear', 'Servicios Editar', 'Servicios Aprobar',
            'Instalaciones Ver', 'Instalaciones Crear', 'Instalaciones Editar', 'Instalaciones Ejecutar',
            'Ordenes Servicio Ver', 'Ordenes Servicio Crear', 'Ordenes Servicio Editar', 'Ordenes Servicio Aprobar',
            'Clientes Ver', 'Clientes Crear', 'Clientes Editar',
            'Inventario Ver', 'Productos Ver', 'Stock Ver',
            // Módulo Stocks (para ver productos necesarios para servicios)
            'Stocks Ver',
            'Productos Ver',
            'Categorias Ver',
            'Marcas Ver',
            'Reportes Ver', 'Reportes Servicios',
        ]);

        // NIVEL 4: SUPERVISOR TÉCNICO (Ejecutor)
        $roleSupervisorTecnico = Rol::firstOrCreate(['name' => 'Supervisor Técnico'], ['guard_name' => 'web']);
        $roleSupervisorTecnico->syncPermissions([
            'Servicios Ver', 'Servicios Crear', 'Servicios Editar',
            'Instalaciones Ver', 'Instalaciones Crear', 'Instalaciones Editar', 'Instalaciones Ejecutar',
            'Ordenes Servicio Ver', 'Ordenes Servicio Crear', 'Ordenes Servicio Editar',
            'Clientes Ver',
            'Inventario Ver', 'Productos Ver',
            // Módulo Stocks (solo lectura para ver materiales)
            'Stocks Ver',
            'Productos Ver',
            'Categorias Ver',
            'Marcas Ver',
        ]);

        // NIVEL 5: ASISTENTE INSTALADOR (Operativo)
        $roleAsistenteInstalador = Rol::firstOrCreate(['name' => 'Asistente Instalador'], ['guard_name' => 'web']);
        $roleAsistenteInstalador->syncPermissions([
            'Servicios Ver',
            'Instalaciones Ver', 'Instalaciones Ejecutar',
            'Ordenes Servicio Ver',
            'Clientes Ver',
            'Productos Ver',
            // Módulo Stocks (solo lectura)
            'Stocks Ver',
            'Productos Ver',
            'Categorias Ver',
            'Marcas Ver',
        ]);

        // ============================================
        // MÓDULO DE VENTAS
        // ============================================

        // NIVEL 3: ENCARGADO DE VENTAS (Único Aprobador)
        $roleEncargadoVentas = Rol::firstOrCreate(['name' => 'Encargado de Ventas'], ['guard_name' => 'web']);
        $roleEncargadoVentas->syncPermissions([
            'Ventas Ver', 'Ventas Crear', 'Ventas Editar', 'Ventas Aprobar',
            'Clientes Ver', 'Clientes Crear', 'Clientes Editar',
            'Cotizaciones Ver', 'Cotizaciones Crear', 'Cotizaciones Editar', 'Cotizaciones Aprobar',
            'Facturas Ver', 'Facturas Crear', 'Facturas Editar', 'Facturas Anular',
            'Caja Ver', 'Caja Abrir', 'Caja Cerrar',
            'Inventario Ver', 'Productos Ver', 'Stock Ver',
            // Módulo Stocks (para ver disponibilidad de productos)
            'Stocks Ver',
            'Productos Ver',
            'Categorias Ver',
            'Marcas Ver',
            'Reportes Ver', 'Reportes Ventas',
        ]);

        // NIVEL 4: ASISTENTE DE VENTAS
        $roleAsistenteVentas = Rol::firstOrCreate(['name' => 'Asistente de Ventas'], ['guard_name' => 'web']);
        $roleAsistenteVentas->syncPermissions([
            'Ventas Ver', 'Ventas Crear', 'Ventas Editar',
            'Clientes Ver', 'Clientes Crear', 'Clientes Editar',
            'Cotizaciones Ver', 'Cotizaciones Crear', 'Cotizaciones Editar',
            'Facturas Ver', 'Facturas Crear',
            'Inventario Ver', 'Productos Ver', 'Stock Ver',
            // Módulo Stocks (solo lectura)
            'Stocks Ver',
            'Productos Ver',
            'Categorias Ver',
            'Marcas Ver',
        ]);

        // NIVEL 5: CAJERO (Especializado)
        $roleCajero = Rol::firstOrCreate(['name' => 'Cajero'], ['guard_name' => 'web']);
        $roleCajero->syncPermissions([
            'Ventas Ver',
            'Clientes Ver',
            'Facturas Ver', 'Facturas Crear',
            'Caja Ver', 'Caja Abrir', 'Caja Cerrar', 'Caja Cobrar',
            'Productos Ver',
            // Módulo Stocks (solo lectura para consultar productos)
            'Stocks Ver',
            'Productos Ver',
            'Categorias Ver',
            'Marcas Ver',
        ]);

        // ============================================
        // CREAR USUARIO SUPER ADMINISTRADOR
        // ============================================

        $userSuperAdmin = User::firstOrCreate(
            ['usuario' => 'superadmin'],
            [
                'name' => 'Super Administrador',
                'email' => 'superadmin@sigea.com',
                'nro_cedula' => '0000000',
                'nro_celular' => '0000000000',
                'observacion' => 'SUPER ADMINISTRADOR DEL SISTEMA - ACCESO TOTAL',
                'password' => Hash::make('superadmin123'),
                'activo' => true,
                'ultimo_acceso' => null,
            ]
        );
        if (!$userSuperAdmin->hasRole($roleSuperAdmin)) {
            $userSuperAdmin->assignRole($roleSuperAdmin);
        }

        // ============================================
        // CREAR USUARIO ADMINISTRADOR DEL SISTEMA
        // ============================================

        $userAdmin = User::firstOrCreate(
            ['usuario' => 'administrador'],
            [
                'name' => 'Administrador',
                'email' => 'ronaldalexisniznunez@gmail.com',
                'nro_cedula' => '1234567',
                'nro_celular' => '0981234567',
                'observacion' => 'ADMINISTRADOR DEL SISTEMA',
                'password' => Hash::make('Rann2006'),
                'activo' => true,
                'ultimo_acceso' => null,
            ]
        );
        if (!$userAdmin->hasRole($roleAdminSistema)) {
            $userAdmin->assignRole($roleAdminSistema);
        }

        // ============================================
        // USUARIOS DE EJEMPLO POR ROL
        // ============================================

        // Gerente General
        $userGerente = User::firstOrCreate(
            ['usuario' => 'gerente.general'],
            [
                'name' => 'Juan Pérez',
                'email' => 'gerente@sigea.com',
                'nro_cedula' => '2000001',
                'nro_celular' => '0981000001',
                'observacion' => 'GERENTE GENERAL',
                'password' => Hash::make('12345678'),
                'activo' => true,
                'ultimo_acceso' => null,
            ]
        );
        if (!$userGerente->hasRole($roleGerenteGeneral)) {
            $userGerente->assignRole($roleGerenteGeneral);
        }

        // Encargado de Compras
        $userEncargadoCompras = User::firstOrCreate(
            ['usuario' => 'encargado.compras'],
            [
                'name' => 'María González',
                'email' => 'compras@sigea.com',
                'nro_cedula' => '3000001',
                'nro_celular' => '0981000002',
                'observacion' => 'ENCARGADO DE COMPRAS',
                'password' => Hash::make('12345678'),
                'activo' => true,
                'ultimo_acceso' => null,
            ]
        );
        if (!$userEncargadoCompras->hasRole($roleEncargadoCompras)) {
            $userEncargadoCompras->assignRole($roleEncargadoCompras);
        }

        // Encargado de Servicios
        $userEncargadoServicios = User::firstOrCreate(
            ['usuario' => 'encargado.servicios'],
            [
                'name' => 'Carlos Ramírez',
                'email' => 'servicios@sigea.com',
                'nro_cedula' => '4000001',
                'nro_celular' => '0981000003',
                'observacion' => 'ENCARGADO DE SERVICIOS',
                'password' => Hash::make('12345678'),
                'activo' => true,
                'ultimo_acceso' => null,
            ]
        );
        if (!$userEncargadoServicios->hasRole($roleEncargadoServicios)) {
            $userEncargadoServicios->assignRole($roleEncargadoServicios);
        }

        // Encargado de Ventas
        $userEncargadoVentas = User::firstOrCreate(
            ['usuario' => 'encargado.ventas'],
            [
                'name' => 'Ana Martínez',
                'email' => 'ventas@sigea.com',
                'nro_cedula' => '5000001',
                'nro_celular' => '0981000004',
                'observacion' => 'ENCARGADO DE VENTAS',
                'password' => Hash::make('12345678'),
                'activo' => true,
                'ultimo_acceso' => null,
            ]
        );
        if (!$userEncargadoVentas->hasRole($roleEncargadoVentas)) {
            $userEncargadoVentas->assignRole($roleEncargadoVentas);
        }

        // Cajero
        $userCajero = User::firstOrCreate(
            ['usuario' => 'cajero'],
            [
                'name' => 'Luis Torres',
                'email' => 'cajero@sigea.com',
                'nro_cedula' => '6000001',
                'nro_celular' => '0981000005',
                'observacion' => 'CAJERO',
                'password' => Hash::make('12345678'),
                'activo' => true,
                'ultimo_acceso' => null,
            ]
        );
        if (!$userCajero->hasRole($roleCajero)) {
            $userCajero->assignRole($roleCajero);
        }
    }
}
