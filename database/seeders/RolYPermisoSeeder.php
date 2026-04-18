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
        // PERMISOS DEL SISTEMA - ESTRUCTURA GRANULAR
        // ============================================

        $permisos = [
            // ============================================
            // SISTEMA - NIVEL 0
            // ============================================
            'SuperAdmin',

            // ============================================
            // ADMINISTRACIÓN DE USUARIOS Y ROLES
            // ============================================
            'admin.usuarios.ver',
            'admin.usuarios.crear',
            'admin.usuarios.editar',
            'admin.usuarios.eliminar',
            'admin.usuarios.asignar_rol',
            'admin.usuarios.activar',
            'admin.usuarios.inactivar',
            'admin.usuarios.reset_password',
            'admin.usuarios.exportar',

            'admin.roles.ver',
            'admin.roles.crear',
            'admin.roles.editar',
            'admin.roles.eliminar',
            'admin.roles.asignar_permisos',

            // Wildcards para administración
            'admin.*',
            'admin.usuarios.*',
            'admin.roles.*',

            // ============================================
            // MÓDULO DE COMPRAS
            // ============================================

            // Dashboard
            'compras.dashboard.ver',

            // Proveedores
            'compras.proveedores.ver',
            'compras.proveedores.crear',
            'compras.proveedores.editar',
            'compras.proveedores.eliminar',

            // Pedidos de Compra
            'compras.pedidos_de_compra.ver',
            'compras.pedidos_de_compra.crear',
            'compras.pedidos_de_compra.editar',
            'compras.pedidos_de_compra.eliminar',
            'compras.pedidos_de_compra.aprobar',
            'compras.pedidos_de_compra.rechazar',
            'compras.pedidos_de_compra.anular',
            'compras.pedidos_de_compra.imprimir',

            // Presupuestos
            'compras.presupuestos.ver',
            'compras.presupuestos.crear',
            'compras.presupuestos.editar',
            'compras.presupuestos.eliminar',
            'compras.presupuestos.comparar',
            'compras.presupuestos.imprimir',

            // Órdenes de Compra
            'compras.ordenes_de_compra.ver',
            'compras.ordenes_de_compra.crear',
            'compras.ordenes_de_compra.editar',
            'compras.ordenes_de_compra.eliminar',
            'compras.ordenes_de_compra.aprobar',
            'compras.ordenes_de_compra.rechazar',
            'compras.ordenes_de_compra.anular',
            'compras.ordenes_de_compra.imprimir',

            // Compras/Facturas
            'compras.compras_facturas.ver',
            'compras.compras_facturas.crear',
            'compras.compras_facturas.editar',
            'compras.compras_facturas.eliminar',
            'compras.compras_facturas.aprobar',
            'compras.compras_facturas.rechazar',
            'compras.compras_facturas.anular',
            'compras.compras_facturas.imprimir',
            'compras.compras_facturas.exportar',

            // Notas de Crédito
            'compras.notas_de_credito.ver',
            'compras.notas_de_credito.crear',
            'compras.notas_de_credito.editar',
            'compras.notas_de_credito.eliminar',
            'compras.notas_de_credito.aprobar',
            'compras.notas_de_credito.anular',

            // Notas de Débito
            'compras.notas_de_debito.ver',
            'compras.notas_de_debito.crear',
            'compras.notas_de_debito.editar',
            'compras.notas_de_debito.eliminar',
            'compras.notas_de_debito.aprobar',
            'compras.notas_de_debito.anular',

            // Remisiones
            'compras.remisiones.ver',
            'compras.remisiones.crear',
            'compras.remisiones.editar',
            'compras.remisiones.eliminar',

            // Recepción Mercadería
            'compras.recepcion_mercaderia.ver',
            'compras.recepcion_mercaderia.crear',
            'compras.recepcion_mercaderia.editar',
            'compras.recepcion_mercaderia.eliminar',
            'compras.recepcion_mercaderia.aprobar',
            'compras.recepcion_mercaderia.rechazar',
            'compras.recepcion_mercaderia.imprimir',

            // Aprobaciones
            'compras.aprobaciones.ver',
            'compras.aprobaciones.aprobar',
            'compras.aprobaciones.rechazar',

            // Cuentas por Pagar
            'compras.cuentas_por_pagar.ver',
            'compras.cuentas_por_pagar.crear',
            'compras.cuentas_por_pagar.editar',
            'compras.cuentas_por_pagar.pagar',
            'compras.cuentas_por_pagar.anular',

            // Reportes
            'compras.reportes.ver',
            'compras.reportes.libro_compras',
            'compras.reportes.analisis_proveedores',
            'compras.reportes.flujo_aprobaciones',
            'compras.reportes.recepciones_vs_compras',
            'compras.reportes.exportar',

            // Wildcards para entidades
            'compras.dashboard.*',
            'compras.proveedores.*',
            'compras.pedidos_de_compra.*',
            'compras.presupuestos.*',
            'compras.ordenes_de_compra.*',
            'compras.compras_facturas.*',
            'compras.notas_de_credito.*',
            'compras.notas_de_debito.*',
            'compras.remisiones.*',
            'compras.recepcion_mercaderia.*',
            'compras.aprobaciones.*',
            'compras.cuentas_por_pagar.*',
            'compras.reportes.*',

            // Wildcard para módulo completo
            'compras.*',

            // ============================================
            // MÓDULO DE VENTAS
            // ============================================

            // Pedidos
            'ventas.pedidos.ver',
            'ventas.pedidos.crear',
            'ventas.pedidos.editar',
            'ventas.pedidos.eliminar',
            'ventas.pedidos.confirmar',
            'ventas.pedidos.preparar',
            'ventas.pedidos.anular',
            'ventas.pedidos.imprimir',

            // Caja
            'ventas.caja.ver',
            'ventas.caja.apertura',
            'ventas.caja.movimientos',
            'ventas.caja.cierre',
            'ventas.caja.arqueo',
            'ventas.caja.recaudaciones',

            // Cotizaciones
            'ventas.cotizaciones.ver',
            'ventas.cotizaciones.crear',
            'ventas.cotizaciones.editar',
            'ventas.cotizaciones.eliminar',
            'ventas.cotizaciones.aprobar',
            'ventas.cotizaciones.rechazar',
            'ventas.cotizaciones.convertir',

            // Facturas
            'ventas.facturas.ver',
            'ventas.facturas.crear',
            'ventas.facturas.editar',
            'ventas.facturas.eliminar',
            'ventas.facturas.emitir',
            'ventas.facturas.anular',
            'ventas.facturas.imprimir',
            'ventas.facturas.exportar',

            // Clientes
            'ventas.clientes.ver',
            'ventas.clientes.crear',
            'ventas.clientes.editar',
            'ventas.clientes.eliminar',

            // Facturación
            'ventas.facturacion.ver',
            'ventas.facturacion.crear',
            'ventas.facturacion.editar',

            // Cuentas por Cobrar
            'ventas.cuentas_por_cobrar.ver',
            'ventas.cuentas_por_cobrar.crear',
            'ventas.cuentas_por_cobrar.editar',
            'ventas.cuentas_por_cobrar.cobrar',
            'ventas.cuentas_por_cobrar.anular',

            // Remisiones
            'ventas.remisiones.ver',
            'ventas.remisiones.crear',
            'ventas.remisiones.editar',
            'ventas.remisiones.eliminar',
            'ventas.remisiones.emitir',
            'ventas.remisiones.anular',
            'ventas.remisiones.imprimir',

            // Notas de Crédito
            'ventas.notas_de_credito.ver',
            'ventas.notas_de_credito.crear',
            'ventas.notas_de_credito.editar',
            'ventas.notas_de_credito.eliminar',
            'ventas.notas_de_credito.emitir',
            'ventas.notas_de_credito.anular',
            'ventas.notas_de_credito.imprimir',

            // Notas de Débito
            'ventas.notas_de_debito.ver',
            'ventas.notas_de_debito.crear',
            'ventas.notas_de_debito.editar',
            'ventas.notas_de_debito.eliminar',
            'ventas.notas_de_debito.emitir',
            'ventas.notas_de_debito.anular',
            'ventas.notas_de_debito.imprimir',

            // Cobranzas
            'ventas.cobranzas.ver',
            'ventas.cobranzas.crear',
            'ventas.cobranzas.editar',
            'ventas.cobranzas.registrar',
            'ventas.cobranzas.forma_pago',
            'ventas.cobranzas.historial',

            // Libro de Ventas
            'ventas.libro_de_ventas.ver',
            'ventas.libro_de_ventas.exportar',
            'ventas.libro_de_ventas.imprimir',

            // Informes
            'ventas.informes.ver',
            'ventas.informes.ventas_periodo',
            'ventas.informes.clientes',
            'ventas.informes.productos',
            'ventas.informes.exportar',

            // Wildcards para entidades
            'ventas.pedidos.*',
            'ventas.caja.*',
            'ventas.cotizaciones.*',
            'ventas.facturas.*',
            'ventas.clientes.*',
            'ventas.facturacion.*',
            'ventas.cuentas_por_cobrar.*',
            'ventas.remisiones.*',
            'ventas.notas_de_credito.*',
            'ventas.notas_de_debito.*',
            'ventas.cobranzas.*',
            'ventas.libro_de_ventas.*',
            'ventas.informes.*',

            // Wildcard para módulo completo
            'ventas.*',

            // ============================================
            // MÓDULO DE SERVICIOS
            // ============================================

            // Tipos de Servicio
            'servicios.tipos_de_servicio.ver',
            'servicios.tipos_de_servicio.crear',
            'servicios.tipos_de_servicio.editar',
            'servicios.tipos_de_servicio.eliminar',

            // Clientes
            'servicios.clientes.ver',
            'servicios.clientes.crear',
            'servicios.clientes.editar',
            'servicios.clientes.eliminar',
            'servicios.clientes.historial',

            // Solicitudes
            'servicios.solicitudes.ver',
            'servicios.solicitudes.crear',
            'servicios.solicitudes.editar',
            'servicios.solicitudes.eliminar',
            'servicios.solicitudes.aprobar',
            'servicios.solicitudes.rechazar',

            // Recepciones
            'servicios.recepciones.ver',
            'servicios.recepciones.crear',
            'servicios.recepciones.editar',
            'servicios.recepciones.eliminar',

            // Diagnósticos
            'servicios.diagnosticos.ver',
            'servicios.diagnosticos.crear',
            'servicios.diagnosticos.editar',
            'servicios.diagnosticos.eliminar',

            // Presupuestos
            'servicios.presupuestos.ver',
            'servicios.presupuestos.crear',
            'servicios.presupuestos.editar',
            'servicios.presupuestos.eliminar',
            'servicios.presupuestos.aprobar',
            'servicios.presupuestos.rechazar',

            // Órdenes de Servicio
            'servicios.ordenes_de_servicio.ver',
            'servicios.ordenes_de_servicio.crear',
            'servicios.ordenes_de_servicio.editar',
            'servicios.ordenes_de_servicio.eliminar',
            'servicios.ordenes_de_servicio.aprobar',
            'servicios.ordenes_de_servicio.finalizar',
            'servicios.ordenes_de_servicio.imprimir',

            // Entrega
            'servicios.entrega.ver',
            'servicios.entrega.crear',
            'servicios.entrega.editar',
            'servicios.entrega.entregar',

            // Promociones
            'servicios.promociones.ver',
            'servicios.promociones.crear',
            'servicios.promociones.editar',
            'servicios.promociones.eliminar',
            'servicios.promociones.activar',
            'servicios.promociones.inactivar',

            // Descuentos
            'servicios.descuentos.ver',
            'servicios.descuentos.crear',
            'servicios.descuentos.editar',
            'servicios.descuentos.eliminar',

            // Reclamos
            'servicios.reclamos.ver',
            'servicios.reclamos.crear',
            'servicios.reclamos.editar',
            'servicios.reclamos.eliminar',
            'servicios.reclamos.asignar',
            'servicios.reclamos.resolver',
            'servicios.reclamos.cerrar',

            // Informes
            'servicios.informes.ver',
            'servicios.informes.solicitudes',
            'servicios.informes.presupuestos',
            'servicios.informes.ordenes',
            'servicios.informes.reclamos',
            'servicios.informes.exportar',

            // Wildcards para entidades
            'servicios.tipos_de_servicio.*',
            'servicios.clientes.*',
            'servicios.solicitudes.*',
            'servicios.recepciones.*',
            'servicios.diagnosticos.*',
            'servicios.presupuestos.*',
            'servicios.ordenes_de_servicio.*',
            'servicios.entrega.*',
            'servicios.promociones.*',
            'servicios.descuentos.*',
            'servicios.reclamos.*',
            'servicios.informes.*',

            // Wildcard para módulo completo
            'servicios.*',

            // ============================================
            // MÓDULO DE INVENTARIO/STOCK
            // ============================================

            // Productos
            'stock.productos.ver',
            'stock.productos.crear',
            'stock.productos.editar',
            'stock.productos.eliminar',
            'stock.productos.activar',
            'stock.productos.inactivar',
            'stock.productos.exportar',

            // Categorías
            'stock.categorias.ver',
            'stock.categorias.crear',
            'stock.categorias.editar',
            'stock.categorias.eliminar',

            // Marcas
            'stock.marcas.ver',
            'stock.marcas.crear',
            'stock.marcas.editar',
            'stock.marcas.eliminar',

            // Unidades de Medida
            'stock.unidades_de_medida.ver',
            'stock.unidades_de_medida.crear',
            'stock.unidades_de_medida.editar',
            'stock.unidades_de_medida.eliminar',

            // Stock General
            'stock.stock_general.ver',
            'stock.stock_general.ajustar',
            'stock.stock_general.transferir',
            'stock.stock_general.inventario',

            // Reportes
            'stock.reportes.ver',
            'stock.reportes.existencias',
            'stock.reportes.movimientos',
            'stock.reportes.valorizado',
            'stock.reportes.kardex',
            'stock.reportes.rotacion',
            'stock.reportes.stock_bajo',
            'stock.reportes.exportar',

            // Wildcards para entidades
            'stock.productos.*',
            'stock.categorias.*',
            'stock.marcas.*',
            'stock.unidades_de_medida.*',
            'stock.stock_general.*',
            'stock.reportes.*',

            // Wildcard para módulo completo
            'stock.*',

            // ============================================
            // MÓDULO DE EMPRESA/CONFIGURACIÓN
            // ============================================

            // Datos de Empresa
            'empresa.datos_de_empresa.ver',
            'empresa.datos_de_empresa.editar',

            // Sucursales
            'empresa.sucursales.ver',
            'empresa.sucursales.crear',
            'empresa.sucursales.editar',
            'empresa.sucursales.eliminar',

            // Depósitos
            'empresa.depositos.ver',
            'empresa.depositos.crear',
            'empresa.depositos.editar',
            'empresa.depositos.eliminar',

            // Puntos de Expedición
            'empresa.puntos_expedicion.ver',
            'empresa.puntos_expedicion.crear',
            'empresa.puntos_expedicion.editar',
            'empresa.puntos_expedicion.eliminar',

            // Timbrados
            'empresa.timbrados.ver',
            'empresa.timbrados.crear',
            'empresa.timbrados.editar',
            'empresa.timbrados.eliminar',

            // Wildcards para entidades
            'empresa.datos_de_empresa.*',
            'empresa.sucursales.*',
            'empresa.depositos.*',
            'empresa.puntos_expedicion.*',
            'empresa.timbrados.*',

            // Wildcard para módulo completo
            'empresa.*',

            // ============================================
            // REPORTES GENERALES DEL SISTEMA
            // ============================================

            'reportes.dashboard',
            'reportes.financieros.ver',
            'reportes.financieros.exportar',
            'reportes.ejecutivos.ver',
            'reportes.ejecutivos.exportar',

            // Wildcards para Reportes
            'reportes.*',
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
            // Administración completa
            'admin.*',

            // Acceso completo a todos los módulos (usando wildcards)
            'compras.*',
            'ventas.*',
            'servicios.*',
            'stock.*',
            'empresa.*',
            'reportes.*',
        ]);

        // NIVEL 2: GERENTE GENERAL
        $roleGerenteGeneral = Rol::firstOrCreate(['name' => 'Gerente General'], ['guard_name' => 'web']);
        $roleGerenteGeneral->syncPermissions([
            // Solo ver usuarios y roles
            'admin.usuarios.ver',
            'admin.roles.ver',

            // Compras: Ver, Aprobar/Rechazar y Reportes
            'compras.dashboard',
            'compras.compras.ver',
            'compras.compras.imprimir',
            'compras.ordenes.ver',
            'compras.ordenes.imprimir',
            'compras.recepciones.ver',
            'compras.proveedores.ver',
            'compras.notas_credito.ver',
            'compras.notas_debito.ver',
            'compras.cuentas_pagar.ver',
            'compras.cuentas_pagar.reportes',
            'compras.aprobaciones.*',
            'compras.reportes.*',

            // Ventas: Ver, Aprobar/Rechazar y Reportes
            'ventas.dashboard',
            'ventas.pedidos.ver',
            'ventas.pedidos.aprobar',
            'ventas.pedidos.rechazar',
            'ventas.pedidos.imprimir',
            'ventas.cotizaciones.ver',
            'ventas.cotizaciones.aprobar',
            'ventas.cotizaciones.rechazar',
            'ventas.facturas.ver',
            'ventas.facturas.imprimir',
            'ventas.clientes.ver',
            'ventas.notas_credito.ver',
            'ventas.notas_debito.ver',
            'ventas.caja.ver',
            'ventas.caja.reportes',
            'ventas.cuentas_cobrar.ver',
            'ventas.cuentas_cobrar.reportes',
            'ventas.reportes.*',

            // Servicios: Ver, Aprobar/Rechazar y Reportes
            'servicios.dashboard',
            'servicios.servicios.ver',
            'servicios.servicios.aprobar',
            'servicios.servicios.rechazar',
            'servicios.ordenes.ver',
            'servicios.ordenes.aprobar',
            'servicios.ordenes.rechazar',
            'servicios.instalaciones.ver',
            'servicios.reclamos.ver',
            'servicios.contratos.ver',
            'servicios.contratos.aprobar',
            'servicios.reportes.*',

            // Stock: Solo lectura
            'stock.dashboard',
            'stock.productos.ver',
            'stock.categorias.ver',
            'stock.marcas.ver',
            'stock.inventario.ver',
            'stock.ajustes.ver',
            'stock.ajustes.aprobar',
            'stock.ajustes.rechazar',
            'stock.movimientos.ver',
            'stock.reportes.*',

            // Empresa: Solo lectura
            'empresa.ver',

            // Reportes completos
            'reportes.*',
        ]);

        // NIVEL 2: SUPERVISOR GENERAL
        $roleSupervisorGeneral = Rol::firstOrCreate(['name' => 'Supervisor General'], ['guard_name' => 'web']);
        $roleSupervisorGeneral->syncPermissions([
            // Similar a Gerente pero sin reportes financieros
            'admin.usuarios.ver',
            'admin.roles.ver',

            'compras.dashboard',
            'compras.compras.ver',
            'compras.compras.imprimir',
            'compras.ordenes.ver',
            'compras.recepciones.ver',
            'compras.proveedores.ver',
            'compras.aprobaciones.*',
            'compras.reportes.ver',
            'compras.reportes.libro',
            'compras.reportes.analisis',
            'compras.reportes.proveedor',

            'ventas.dashboard',
            'ventas.pedidos.ver',
            'ventas.pedidos.aprobar',
            'ventas.pedidos.rechazar',
            'ventas.cotizaciones.ver',
            'ventas.cotizaciones.aprobar',
            'ventas.cotizaciones.rechazar',
            'ventas.facturas.ver',
            'ventas.clientes.ver',
            'ventas.caja.ver',
            'ventas.reportes.ver',
            'ventas.reportes.libro',
            'ventas.reportes.ventas_periodo',
            'ventas.reportes.cliente',

            'servicios.dashboard',
            'servicios.servicios.ver',
            'servicios.servicios.aprobar',
            'servicios.servicios.rechazar',
            'servicios.ordenes.ver',
            'servicios.ordenes.aprobar',
            'servicios.ordenes.rechazar',
            'servicios.reportes.ver',
            'servicios.reportes.servicios',
            'servicios.reportes.ordenes',

            'stock.dashboard',
            'stock.productos.ver',
            'stock.inventario.ver',
            'stock.reportes.ver',
            'stock.reportes.existencias',
        ]);

        // ============================================
        // MÓDULO DE COMPRAS - ROLES
        // ============================================

        // NIVEL 3: ENCARGADO DE COMPRAS
        $roleEncargadoCompras = Rol::firstOrCreate(['name' => 'Encargado de Compras'], ['guard_name' => 'web']);
        $roleEncargadoCompras->syncPermissions([
            // Acceso completo al módulo de compras (usando wildcard)
            'compras.*',

            // Acceso a Stock para gestionar productos
            'stock.productos.ver',
            'stock.productos.crear',
            'stock.productos.editar',
            'stock.categorias.ver',
            'stock.categorias.crear',
            'stock.categorias.editar',
            'stock.marcas.ver',
            'stock.marcas.crear',
            'stock.marcas.editar',
            'stock.inventario.ver',
            'stock.reportes.ver',
            'stock.reportes.existencias',
        ]);

        // NIVEL 4: SUPERVISOR DE COMPRAS
        $roleSupervisorCompras = Rol::firstOrCreate(['name' => 'Supervisor de Compras'], ['guard_name' => 'web']);
        $roleSupervisorCompras->syncPermissions([
            'compras.dashboard',
            'compras.compras.ver',
            'compras.compras.crear',
            'compras.compras.editar',
            'compras.compras.imprimir',
            'compras.ordenes.ver',
            'compras.ordenes.crear',
            'compras.ordenes.editar',
            'compras.ordenes.imprimir',
            'compras.recepciones.ver',
            'compras.recepciones.crear',
            'compras.recepciones.editar',
            'compras.proveedores.ver',
            'compras.proveedores.crear',
            'compras.proveedores.editar',
            'compras.aprobaciones.ver',
            'compras.reportes.ver',
            'compras.reportes.libro',
            'compras.reportes.analisis',

            'stock.productos.ver',
            'stock.productos.crear',
            'stock.productos.editar',
            'stock.categorias.ver',
            'stock.marcas.ver',
            'stock.inventario.ver',
        ]);

        // NIVEL 5: ASISTENTE DE COMPRAS
        $roleAsistenteCompras = Rol::firstOrCreate(['name' => 'Asistente de Compras'], ['guard_name' => 'web']);
        $roleAsistenteCompras->syncPermissions([
            'compras.dashboard',
            'compras.compras.ver',
            'compras.compras.crear',
            'compras.ordenes.ver',
            'compras.ordenes.crear',
            'compras.recepciones.ver',
            'compras.recepciones.crear',
            'compras.proveedores.ver',
            'compras.proveedores.crear',
            'compras.aprobaciones.ver',

            'stock.productos.ver',
            'stock.categorias.ver',
            'stock.marcas.ver',
            'stock.inventario.ver',
        ]);

        // ============================================
        // MÓDULO DE SERVICIOS - ROLES
        // ============================================

        // NIVEL 3: ENCARGADO DE SERVICIOS
        $roleEncargadoServicios = Rol::firstOrCreate(['name' => 'Encargado de Servicios'], ['guard_name' => 'web']);
        $roleEncargadoServicios->syncPermissions([
            // Acceso completo al módulo de servicios (usando wildcard)
            'servicios.*',

            // Clientes (lectura y edición)
            'ventas.clientes.ver',
            'ventas.clientes.crear',
            'ventas.clientes.editar',

            // Stock (lectura para materiales)
            'stock.productos.ver',
            'stock.categorias.ver',
            'stock.inventario.ver',
            'stock.reportes.ver',
        ]);

        // NIVEL 4: SUPERVISOR TÉCNICO
        $roleSupervisorTecnico = Rol::firstOrCreate(['name' => 'Supervisor Técnico'], ['guard_name' => 'web']);
        $roleSupervisorTecnico->syncPermissions([
            'servicios.dashboard',
            'servicios.servicios.ver',
            'servicios.servicios.crear',
            'servicios.servicios.editar',
            'servicios.ordenes.ver',
            'servicios.ordenes.crear',
            'servicios.ordenes.editar',
            'servicios.ordenes.ejecutar',
            'servicios.ordenes.finalizar',
            'servicios.ordenes.imprimir',
            'servicios.instalaciones.ver',
            'servicios.instalaciones.crear',
            'servicios.instalaciones.editar',
            'servicios.instalaciones.ejecutar',
            'servicios.instalaciones.finalizar',
            'servicios.reclamos.ver',
            'servicios.reclamos.asignar',
            'servicios.reclamos.resolver',

            'ventas.clientes.ver',
            'stock.productos.ver',
            'stock.inventario.ver',
        ]);

        // NIVEL 5: ASISTENTE INSTALADOR
        $roleAsistenteInstalador = Rol::firstOrCreate(['name' => 'Asistente Instalador'], ['guard_name' => 'web']);
        $roleAsistenteInstalador->syncPermissions([
            'servicios.servicios.ver',
            'servicios.ordenes.ver',
            'servicios.ordenes.ejecutar',
            'servicios.instalaciones.ver',
            'servicios.instalaciones.ejecutar',

            'ventas.clientes.ver',
            'stock.productos.ver',
        ]);

        // ============================================
        // MÓDULO DE VENTAS - ROLES
        // ============================================

        // NIVEL 3: ENCARGADO DE VENTAS
        $roleEncargadoVentas = Rol::firstOrCreate(['name' => 'Encargado de Ventas'], ['guard_name' => 'web']);
        $roleEncargadoVentas->syncPermissions([
            // Acceso completo al módulo de ventas (usando wildcard)
            'ventas.*',

            // Stock (lectura para productos)
            'stock.productos.ver',
            'stock.categorias.ver',
            'stock.marcas.ver',
            'stock.inventario.ver',
            'stock.reportes.ver',
            'stock.reportes.existencias',
        ]);

        // NIVEL 4: ASISTENTE DE VENTAS
        $roleAsistenteVentas = Rol::firstOrCreate(['name' => 'Asistente de Ventas'], ['guard_name' => 'web']);
        $roleAsistenteVentas->syncPermissions([
            'ventas.dashboard',
            'ventas.pedidos.ver',
            'ventas.pedidos.crear',
            'ventas.pedidos.editar',
            'ventas.pedidos.imprimir',
            'ventas.cotizaciones.ver',
            'ventas.cotizaciones.crear',
            'ventas.cotizaciones.editar',
            'ventas.cotizaciones.convertir',
            'ventas.cotizaciones.imprimir',
            'ventas.facturas.ver',
            'ventas.facturas.crear',
            'ventas.clientes.ver',
            'ventas.clientes.crear',
            'ventas.clientes.editar',

            'stock.productos.ver',
            'stock.inventario.ver',
        ]);

        // NIVEL 5: CAJERO
        $roleCajero = Rol::firstOrCreate(['name' => 'Cajero'], ['guard_name' => 'web']);
        $roleCajero->syncPermissions([
            'ventas.facturas.ver',
            'ventas.facturas.crear',
            'ventas.facturas.imprimir',
            'ventas.clientes.ver',
            'ventas.caja.*',
            'ventas.remisiones.ver',

            'stock.productos.ver',
        ]);

        // ============================================
        // MÓDULO DE STOCK - ROLES
        // ============================================

        // NIVEL 3: ENCARGADO DE ALMACÉN
        $roleEncargadoAlmacen = Rol::firstOrCreate(['name' => 'Encargado de Almacén'], ['guard_name' => 'web']);
        $roleEncargadoAlmacen->syncPermissions([
            // Acceso completo al módulo de stock (usando wildcard)
            'stock.*',
        ]);

        // NIVEL 4: ASISTENTE DE ALMACÉN
        $roleAsistenteAlmacen = Rol::firstOrCreate(['name' => 'Asistente de Almacén'], ['guard_name' => 'web']);
        $roleAsistenteAlmacen->syncPermissions([
            'stock.dashboard',
            'stock.productos.ver',
            'stock.productos.crear',
            'stock.productos.editar',
            'stock.categorias.ver',
            'stock.marcas.ver',
            'stock.inventario.ver',
            'stock.inventario.ajustar',
            'stock.ajustes.ver',
            'stock.ajustes.crear',
            'stock.movimientos.ver',
            'stock.reportes.ver',
            'stock.reportes.existencias',
            'stock.reportes.movimientos',
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

        // Encargado de Almacén
        $userEncargadoAlmacen = User::firstOrCreate(
            ['usuario' => 'encargado.almacen'],
            [
                'name' => 'Roberto Silva',
                'email' => 'almacen@sigea.com',
                'nro_cedula' => '7000001',
                'nro_celular' => '0981000006',
                'observacion' => 'ENCARGADO DE ALMACÉN',
                'password' => Hash::make('12345678'),
                'activo' => true,
                'ultimo_acceso' => null,
            ]
        );
        if (!$userEncargadoAlmacen->hasRole($roleEncargadoAlmacen)) {
            $userEncargadoAlmacen->assignRole($roleEncargadoAlmacen);
        }
    }
}
