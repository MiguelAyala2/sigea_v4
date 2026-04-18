# Sistema de Permisos y Roles - SIGEA v4

## Estructura Implementada: Opción 1 + 4 (Granular + Wildcards)

Este documento describe el sistema mejorado de permisos granulares con soporte de wildcards implementado en el sistema.

---

## 📋 Tabla de Contenidos

1. [Configuración](#configuración)
2. [Estructura de Permisos](#estructura-de-permisos)
3. [Acciones Disponibles](#acciones-disponibles)
4. [Matriz de Permisos por Módulo](#matriz-de-permisos-por-módulo)
5. [Roles del Sistema](#roles-del-sistema)
6. [Uso de Wildcards](#uso-de-wildcards)
7. [Cómo Usar en el Código](#cómo-usar-en-el-código)
8. [Usuarios de Ejemplo](#usuarios-de-ejemplo)

---

## ⚙️ Configuración

### Wildcards Habilitados

En `config/permission.php`:

```php
'enable_wildcard_permission' => true,
```

Esto permite usar permisos como `compras.*` para dar acceso completo a un módulo.

---

## 🏗️ Estructura de Permisos

Formato: `{módulo}.{entidad}.{acción}`

### Ejemplos:

```
compras.compras.ver
compras.compras.crear
compras.proveedores.editar
ventas.facturas.aprobar
servicios.ordenes.ejecutar
stock.inventario.ajustar
```

---

## 🎬 Acciones Disponibles

Cada entidad puede tener las siguientes acciones estándar:

| Acción | Descripción | Ejemplo de Uso |
|--------|-------------|----------------|
| **ver** | Listar y visualizar detalles | Ver facturas, Ver clientes |
| **crear** | Crear nuevos registros | Crear compra, Crear producto |
| **editar** | Modificar registros existentes | Editar proveedor, Editar pedido |
| **eliminar** | Eliminar registros (soft delete) | Eliminar producto, Eliminar cliente |
| **aprobar** | Aprobar documentos/solicitudes | Aprobar compra, Aprobar cotización |
| **rechazar** | Rechazar documentos/solicitudes | Rechazar orden, Rechazar ajuste |
| **anular** | Anular documentos procesados | Anular factura, Anular compra |
| **imprimir** | Imprimir/Exportar documentos | Imprimir PDF, Generar documento |
| **exportar** | Exportar datos (Excel, PDF, etc.) | Exportar reportes, Descargar datos |
| **activar** | Activar registros inactivos | Activar usuario, Activar proveedor |
| **inactivar** | Desactivar registros | Inactivar cliente, Suspender usuario |
| **gestionar** | Acciones administrativas avanzadas | Gestionar cuentas por cobrar |
| **ejecutar** | Realizar tareas operativas | Ejecutar instalación, Realizar servicio |
| **finalizar** | Completar procesos | Finalizar servicio, Cerrar orden |
| **asignar** | Asignar recursos/responsables | Asignar técnico, Asignar rol |
| **cobrar** | Realizar cobranzas | Cobrar factura, Recibir pago |
| **pagar** | Realizar pagos | Pagar a proveedor |
| **transferir** | Mover entre ubicaciones | Transferir stock |
| **ajustar** | Ajustar valores/cantidades | Ajustar inventario |
| **convertir** | Convertir entre tipos | Convertir cotización a pedido |
| **reportes** | Ver reportes específicos | Reportes de caja, Reportes de módulo |

---

## 📊 Matriz de Permisos por Módulo

### 🔐 MÓDULO ADMIN (Administración)

#### Usuarios
```
admin.usuarios.ver
admin.usuarios.crear
admin.usuarios.editar
admin.usuarios.eliminar
admin.usuarios.asignar_rol
admin.usuarios.activar
admin.usuarios.inactivar
admin.usuarios.reset_password
admin.usuarios.exportar
```

#### Roles
```
admin.roles.ver
admin.roles.crear
admin.roles.editar
admin.roles.eliminar
admin.roles.asignar_permisos
```

#### Wildcards
```
admin.*              → Todos los permisos de administración
admin.usuarios.*     → Todos los permisos de usuarios
admin.roles.*        → Todos los permisos de roles
```

---

### 🛒 MÓDULO COMPRAS

#### Dashboard
```
compras.dashboard
```

#### Compras/Facturas
```
compras.compras.ver
compras.compras.crear
compras.compras.editar
compras.compras.eliminar
compras.compras.aprobar
compras.compras.rechazar
compras.compras.anular
compras.compras.imprimir
compras.compras.exportar
```

#### Recepciones
```
compras.recepciones.ver
compras.recepciones.crear
compras.recepciones.editar
compras.recepciones.eliminar
compras.recepciones.aprobar
compras.recepciones.rechazar
compras.recepciones.imprimir
```

#### Órdenes de Compra
```
compras.ordenes.ver
compras.ordenes.crear
compras.ordenes.editar
compras.ordenes.eliminar
compras.ordenes.aprobar
compras.ordenes.rechazar
compras.ordenes.anular
compras.ordenes.imprimir
```

#### Proveedores
```
compras.proveedores.ver
compras.proveedores.crear
compras.proveedores.editar
compras.proveedores.eliminar
compras.proveedores.activar
compras.proveedores.inactivar
compras.proveedores.exportar
```

#### Notas de Crédito/Débito
```
compras.notas_credito.ver
compras.notas_credito.crear
compras.notas_credito.aprobar
compras.notas_credito.anular

compras.notas_debito.ver
compras.notas_debito.crear
compras.notas_debito.aprobar
compras.notas_debito.anular
```

#### Cuentas por Pagar
```
compras.cuentas_pagar.ver
compras.cuentas_pagar.gestionar
compras.cuentas_pagar.pagar
compras.cuentas_pagar.reportes
```

#### Aprobaciones
```
compras.aprobaciones.ver
compras.aprobaciones.aprobar
compras.aprobaciones.rechazar
```

#### Reportes
```
compras.reportes.ver
compras.reportes.libro
compras.reportes.analisis
compras.reportes.flujo
compras.reportes.proveedor
compras.reportes.exportar
```

#### Wildcards
```
compras.*                    → Acceso completo al módulo
compras.compras.*            → Todos los permisos de compras
compras.recepciones.*        → Todos los permisos de recepciones
compras.ordenes.*            → Todos los permisos de órdenes
compras.proveedores.*        → Todos los permisos de proveedores
compras.notas_credito.*      → Todos los permisos de notas de crédito
compras.notas_debito.*       → Todos los permisos de notas de débito
compras.cuentas_pagar.*      → Todos los permisos de cuentas por pagar
compras.aprobaciones.*       → Todos los permisos de aprobaciones
compras.reportes.*           → Todos los permisos de reportes
```

---

### 💰 MÓDULO VENTAS

#### Dashboard
```
ventas.dashboard
```

#### Pedidos
```
ventas.pedidos.ver
ventas.pedidos.crear
ventas.pedidos.editar
ventas.pedidos.eliminar
ventas.pedidos.aprobar
ventas.pedidos.rechazar
ventas.pedidos.facturar
ventas.pedidos.anular
ventas.pedidos.imprimir
```

#### Cotizaciones
```
ventas.cotizaciones.ver
ventas.cotizaciones.crear
ventas.cotizaciones.editar
ventas.cotizaciones.eliminar
ventas.cotizaciones.aprobar
ventas.cotizaciones.rechazar
ventas.cotizaciones.convertir
ventas.cotizaciones.imprimir
```

#### Facturas
```
ventas.facturas.ver
ventas.facturas.crear
ventas.facturas.editar
ventas.facturas.eliminar
ventas.facturas.aprobar
ventas.facturas.anular
ventas.facturas.imprimir
ventas.facturas.exportar
```

#### Clientes
```
ventas.clientes.ver
ventas.clientes.crear
ventas.clientes.editar
ventas.clientes.eliminar
ventas.clientes.activar
ventas.clientes.inactivar
ventas.clientes.exportar
```

#### Notas de Crédito/Débito
```
ventas.notas_credito.ver
ventas.notas_credito.crear
ventas.notas_credito.aprobar
ventas.notas_credito.anular

ventas.notas_debito.ver
ventas.notas_debito.crear
ventas.notas_debito.aprobar
ventas.notas_debito.anular
```

#### Remisiones
```
ventas.remisiones.ver
ventas.remisiones.crear
ventas.remisiones.editar
ventas.remisiones.anular
ventas.remisiones.imprimir
```

#### Cuentas por Cobrar
```
ventas.cuentas_cobrar.ver
ventas.cuentas_cobrar.gestionar
ventas.cuentas_cobrar.cobrar
ventas.cuentas_cobrar.reportes
```

#### Caja
```
ventas.caja.ver
ventas.caja.abrir
ventas.caja.cerrar
ventas.caja.cobrar
ventas.caja.arqueo
ventas.caja.depositar
ventas.caja.reportes
```

#### Formas de Cobro
```
ventas.formas_cobro.ver
ventas.formas_cobro.crear
ventas.formas_cobro.editar
```

#### Reportes
```
ventas.reportes.ver
ventas.reportes.libro
ventas.reportes.ventas_periodo
ventas.reportes.cliente
ventas.reportes.vendedor
ventas.reportes.producto
ventas.reportes.exportar
```

#### Wildcards
```
ventas.*                     → Acceso completo al módulo
ventas.pedidos.*             → Todos los permisos de pedidos
ventas.cotizaciones.*        → Todos los permisos de cotizaciones
ventas.facturas.*            → Todos los permisos de facturas
ventas.clientes.*            → Todos los permisos de clientes
ventas.notas_credito.*       → Todos los permisos de notas de crédito
ventas.notas_debito.*        → Todos los permisos de notas de débito
ventas.remisiones.*          → Todos los permisos de remisiones
ventas.cuentas_cobrar.*      → Todos los permisos de cuentas por cobrar
ventas.caja.*                → Todos los permisos de caja
ventas.formas_cobro.*        → Todos los permisos de formas de cobro
ventas.reportes.*            → Todos los permisos de reportes
```

---

### 🔧 MÓDULO SERVICIOS

#### Dashboard
```
servicios.dashboard
```

#### Servicios
```
servicios.servicios.ver
servicios.servicios.crear
servicios.servicios.editar
servicios.servicios.eliminar
servicios.servicios.aprobar
servicios.servicios.rechazar
servicios.servicios.finalizar
servicios.servicios.anular
```

#### Órdenes de Servicio
```
servicios.ordenes.ver
servicios.ordenes.crear
servicios.ordenes.editar
servicios.ordenes.eliminar
servicios.ordenes.aprobar
servicios.ordenes.rechazar
servicios.ordenes.ejecutar
servicios.ordenes.finalizar
servicios.ordenes.imprimir
```

#### Instalaciones
```
servicios.instalaciones.ver
servicios.instalaciones.crear
servicios.instalaciones.editar
servicios.instalaciones.eliminar
servicios.instalaciones.ejecutar
servicios.instalaciones.finalizar
servicios.instalaciones.imprimir
```

#### Reclamos
```
servicios.reclamos.ver
servicios.reclamos.crear
servicios.reclamos.editar
servicios.reclamos.asignar
servicios.reclamos.resolver
servicios.reclamos.cerrar
```

#### Contratos
```
servicios.contratos.ver
servicios.contratos.crear
servicios.contratos.editar
servicios.contratos.aprobar
servicios.contratos.anular
servicios.contratos.imprimir
```

#### Reportes
```
servicios.reportes.ver
servicios.reportes.servicios
servicios.reportes.ordenes
servicios.reportes.instalaciones
servicios.reportes.reclamos
servicios.reportes.tecnicos
servicios.reportes.exportar
```

#### Wildcards
```
servicios.*                  → Acceso completo al módulo
servicios.servicios.*        → Todos los permisos de servicios
servicios.ordenes.*          → Todos los permisos de órdenes
servicios.instalaciones.*    → Todos los permisos de instalaciones
servicios.reclamos.*         → Todos los permisos de reclamos
servicios.contratos.*        → Todos los permisos de contratos
servicios.reportes.*         → Todos los permisos de reportes
```

---

### 📦 MÓDULO STOCK/INVENTARIO

#### Dashboard
```
stock.dashboard
```

#### Productos
```
stock.productos.ver
stock.productos.crear
stock.productos.editar
stock.productos.eliminar
stock.productos.activar
stock.productos.inactivar
stock.productos.exportar
```

#### Categorías
```
stock.categorias.ver
stock.categorias.crear
stock.categorias.editar
stock.categorias.eliminar
```

#### Marcas
```
stock.marcas.ver
stock.marcas.crear
stock.marcas.editar
stock.marcas.eliminar
```

#### Inventario
```
stock.inventario.ver
stock.inventario.crear
stock.inventario.editar
stock.inventario.eliminar
stock.inventario.ajustar
stock.inventario.transferir
```

#### Ajustes de Stock
```
stock.ajustes.ver
stock.ajustes.crear
stock.ajustes.aprobar
stock.ajustes.rechazar
```

#### Movimientos
```
stock.movimientos.ver
stock.movimientos.crear
stock.movimientos.reportes
```

#### Reportes
```
stock.reportes.ver
stock.reportes.existencias
stock.reportes.movimientos
stock.reportes.valorizado
stock.reportes.kardex
stock.reportes.exportar
```

#### Wildcards
```
stock.*                      → Acceso completo al módulo
stock.productos.*            → Todos los permisos de productos
stock.categorias.*           → Todos los permisos de categorías
stock.marcas.*               → Todos los permisos de marcas
stock.inventario.*           → Todos los permisos de inventario
stock.ajustes.*              → Todos los permisos de ajustes
stock.movimientos.*          → Todos los permisos de movimientos
stock.reportes.*             → Todos los permisos de reportes
```

---

### 🏢 MÓDULO EMPRESA/CONFIGURACIÓN

```
empresa.ver
empresa.editar
empresa.configuracion.ver
empresa.configuracion.editar
empresa.sucursales.ver
empresa.sucursales.crear
empresa.sucursales.editar
empresa.sucursales.eliminar
```

#### Wildcards
```
empresa.*                    → Acceso completo al módulo
```

---

### 📈 REPORTES GENERALES

```
reportes.dashboard
reportes.financieros.ver
reportes.financieros.exportar
reportes.ejecutivos.ver
reportes.ejecutivos.exportar
```

#### Wildcards
```
reportes.*                   → Acceso completo a reportes
```

---

## 👥 Roles del Sistema

### Nivel 0: SuperAdmin
- **Acceso:** TOTAL (todos los permisos)
- **Permisos:** `SuperAdmin` (permiso especial que otorga acceso completo)

---

### Nivel 1: Administrador del Sistema
- **Acceso:** Completo a todos los módulos
- **Permisos Wildcards:**
  - `admin.*`
  - `compras.*`
  - `ventas.*`
  - `servicios.*`
  - `stock.*`
  - `empresa.*`
  - `reportes.*`

---

### Nivel 2: Gerencia y Supervisión

#### Gerente General
- **Acceso:** Ver, aprobar/rechazar y reportes de todos los módulos
- **Permisos Clave:**
  - Ver usuarios y roles (sin modificar)
  - `compras.aprobaciones.*`
  - `compras.reportes.*`
  - `ventas.reportes.*`
  - `servicios.reportes.*`
  - `stock.reportes.*`
  - `reportes.*` (incluye financieros)

#### Supervisor General
- **Acceso:** Similar a Gerente General pero sin reportes financieros
- **Diferencia:** No tiene acceso a `reportes.financieros.*`

---

### Nivel 3: Encargados de Módulo

#### Encargado de Compras
- **Permisos:** `compras.*` + permisos de stock limitados
- **Puede:** Crear, editar, eliminar, aprobar, rechazar, anular compras

#### Encargado de Servicios
- **Permisos:** `servicios.*` + permisos de clientes y stock limitados
- **Puede:** Gestión completa de servicios, órdenes, instalaciones

#### Encargado de Ventas
- **Permisos:** `ventas.*` + permisos de stock limitados
- **Puede:** Gestión completa de ventas, clientes, caja, facturas

#### Encargado de Almacén
- **Permisos:** `stock.*`
- **Puede:** Gestión completa de inventario, productos, ajustes

---

### Nivel 4: Supervisores y Asistentes

#### Supervisor de Compras
- **Permisos:** Ver, crear, editar (sin eliminar/anular)
- **NO puede:** Aprobar, eliminar, anular

#### Supervisor Técnico
- **Permisos:** Ejecutar órdenes, finalizar servicios
- **NO puede:** Aprobar, eliminar

#### Asistente de Ventas
- **Permisos:** Ver, crear, editar (sin aprobar/anular)
- **NO puede:** Aprobar, eliminar facturas

#### Asistente de Almacén
- **Permisos:** Ver, crear, editar, ajustar inventario
- **NO puede:** Eliminar productos

---

### Nivel 5: Operativos

#### Asistente de Compras
- **Permisos:** Ver, crear (solo lectura y creación)
- **NO puede:** Editar, eliminar, aprobar

#### Asistente Instalador
- **Permisos:** Ver y ejecutar instalaciones
- **NO puede:** Crear, editar, aprobar órdenes

#### Cajero
- **Permisos:** `ventas.caja.*`, ver y crear facturas
- **NO puede:** Anular facturas, editar precios

---

## 🌟 Uso de Wildcards

### ¿Qué son los Wildcards?

Los wildcards (`*`) permiten otorgar múltiples permisos con una sola asignación.

### Ejemplos de Uso:

```php
// Dar acceso completo al módulo de compras
$role->givePermissionTo('compras.*');

// Equivale a asignar todos estos permisos:
// compras.dashboard
// compras.compras.ver
// compras.compras.crear
// compras.compras.editar
// compras.compras.eliminar
// ... etc
```

```php
// Dar acceso solo a las facturas de ventas
$role->givePermissionTo('ventas.facturas.*');

// Equivale a:
// ventas.facturas.ver
// ventas.facturas.crear
// ventas.facturas.editar
// ventas.facturas.eliminar
// ventas.facturas.aprobar
// ventas.facturas.anular
// ventas.facturas.imprimir
// ventas.facturas.exportar
```

### Jerarquía de Wildcards:

```
compras.*                    → TODO el módulo de compras
compras.compras.*            → Solo sección de compras
compras.compras.ver          → Solo ver compras (permiso específico)
```

---

## 💻 Cómo Usar en el Código

### En Rutas (Middleware)

```php
// Permiso específico
Route::get('/compras', [CompraController::class, 'index'])
    ->middleware('can:compras.compras.ver');

// Permiso para crear
Route::get('/compras/crear', [CompraController::class, 'create'])
    ->middleware('can:compras.compras.crear');

// Permiso para aprobar
Route::post('/compras/{compra}/aprobar', [CompraController::class, 'aprobar'])
    ->middleware('can:compras.compras.aprobar');
```

### En Controladores

```php
public function aprobar(Compra $compra)
{
    // Verificar permiso
    $this->authorize('compras.compras.aprobar');

    // O usando Gate
    if (Gate::denies('compras.compras.aprobar')) {
        abort(403, 'No tienes permiso para aprobar compras');
    }

    // Lógica de aprobación...
}
```

### En Vistas Blade

```blade
{{-- Mostrar botón solo si tiene permiso --}}
@can('compras.compras.crear')
    <a href="{{ route('compras.compras.create') }}" class="btn btn-primary">
        Nueva Compra
    </a>
@endcan

{{-- Mostrar botón de aprobar solo si tiene permiso --}}
@can('compras.compras.aprobar')
    <button wire:click="aprobar({{ $compra->id }})">
        Aprobar
    </button>
@endcan

{{-- Verificar múltiples permisos --}}
@canany(['compras.compras.editar', 'compras.compras.eliminar'])
    <div class="acciones">
        @can('compras.compras.editar')
            <button>Editar</button>
        @endcan

        @can('compras.compras.eliminar')
            <button>Eliminar</button>
        @endcan
    </div>
@endcanany

{{-- Verificar con wildcard --}}
@can('compras.*')
    <div class="admin-tools">
        <!-- Herramientas de administrador -->
    </div>
@endcan
```

### En Componentes Livewire

```php
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ComprasIndex extends Component
{
    use AuthorizesRequests;

    public function crear()
    {
        $this->authorize('compras.compras.crear');

        // Lógica para crear...
    }

    public function aprobar($compraId)
    {
        $this->authorize('compras.compras.aprobar');

        // Lógica para aprobar...
    }

    public function render()
    {
        return view('livewire.compras.index', [
            'puedeCrear' => auth()->user()->can('compras.compras.crear'),
            'puedeEditar' => auth()->user()->can('compras.compras.editar'),
            'puedeEliminar' => auth()->user()->can('compras.compras.eliminar'),
            'puedeAprobar' => auth()->user()->can('compras.compras.aprobar'),
        ]);
    }
}
```

### Verificar Permisos en PHP

```php
// Verificar un permiso
if (auth()->user()->can('compras.compras.crear')) {
    // Usuario puede crear compras
}

// Verificar múltiples permisos (ANY)
if (auth()->user()->canAny(['compras.compras.editar', 'compras.compras.ver'])) {
    // Usuario tiene al menos uno de estos permisos
}

// Verificar con wildcard
if (auth()->user()->can('compras.*')) {
    // Usuario tiene acceso completo al módulo de compras
}

// Verificar permiso directo
if (auth()->user()->hasPermissionTo('compras.compras.aprobar')) {
    // Usuario tiene específicamente este permiso
}

// Verificar por rol
if (auth()->user()->hasRole('Encargado de Compras')) {
    // Usuario tiene este rol
}
```

---

## 👤 Usuarios de Ejemplo

Al ejecutar el seeder, se crean los siguientes usuarios:

| Usuario | Contraseña | Rol | Email |
|---------|------------|-----|-------|
| superadmin | superadmin123 | SuperAdmin | superadmin@sigea.com |
| administrador | Rann2006 | Administrador del Sistema | ronaldalexisniznunez@gmail.com |
| gerente.general | 12345678 | Gerente General | gerente@sigea.com |
| encargado.compras | 12345678 | Encargado de Compras | compras@sigea.com |
| encargado.servicios | 12345678 | Encargado de Servicios | servicios@sigea.com |
| encargado.ventas | 12345678 | Encargado de Ventas | ventas@sigea.com |
| cajero | 12345678 | Cajero | cajero@sigea.com |
| encargado.almacen | 12345678 | Encargado de Almacén | almacen@sigea.com |

---

## 🚀 Cómo Aplicar los Cambios

### 1. Refrescar Base de Datos y Permisos

```bash
# Limpiar cache de permisos
php artisan permission:cache-reset

# Ejecutar migraciones y seeders
php artisan migrate:fresh --seed

# O solo ejecutar el seeder de permisos
php artisan db:seed --class=RolYPermisoSeeder
```

### 2. Actualizar Rutas

Las rutas ya deben usar los nuevos permisos. Ejemplo:

```php
// Antes (permisos legados)
->middleware('can:Compras Ver')

// Ahora (permisos granulares)
->middleware('can:compras.compras.ver')
```

### 3. Actualizar Vistas

Reemplazar directivas Blade antiguas:

```blade
{{-- Antes --}}
@can('Compras Crear')

{{-- Ahora --}}
@can('compras.compras.crear')
```

---

## 📝 Notas Importantes

### Compatibilidad

- Los permisos antiguos **NO fueron eliminados** para mantener compatibilidad
- Se pueden migrar gradualmente a la nueva estructura
- Los wildcards funcionan solo con la nueva nomenclatura (`modulo.entidad.accion`)

### Mejores Prácticas

1. **Usar wildcards para roles altos:** Administradores y Encargados
2. **Usar permisos específicos para roles operativos:** Asistentes, Cajeros
3. **Verificar permisos en múltiples capas:** Rutas + Controladores + Vistas
4. **Documentar permisos personalizados** en este archivo
5. **Limpiar cache** después de cambios: `php artisan permission:cache-reset`

### Seguridad

- **NUNCA** dar `SuperAdmin` a usuarios regulares
- **REVISAR** permisos antes de asignar wildcards completos
- **AUDITAR** cambios de permisos con los campos `creadoPor` y `actualizadoPor`
- **VALIDAR** permisos tanto en backend como frontend

---

## 🔄 Actualización de Código Existente

### Migración Gradual

Para migrar código existente sin romper funcionalidad:

1. **Identificar permisos antiguos** en uso
2. **Crear equivalente granular** en el seeder
3. **Actualizar middleware** en rutas
4. **Actualizar directivas** en vistas
5. **Probar funcionalidad** con diferentes roles
6. **Remover permisos antiguos** una vez migrado todo

### Script de Ayuda para Migración

```php
// Mapeo de permisos antiguos a nuevos
$mapeo = [
    'Compras Ver' => 'compras.compras.ver',
    'Compras Crear' => 'compras.compras.crear',
    'Compras Editar' => 'compras.compras.editar',
    'Compras Eliminar' => 'compras.compras.eliminar',
    'Compras Aprobar' => 'compras.compras.aprobar',

    'Proveedores Ver' => 'compras.proveedores.ver',
    'Proveedores Crear' => 'compras.proveedores.crear',
    // ... etc
];

// Migrar permisos de roles
foreach ($roles as $role) {
    $permisosAntiguos = $role->permissions()->pluck('name')->toArray();
    $permisosNuevos = [];

    foreach ($permisosAntiguos as $permisoAntiguo) {
        if (isset($mapeo[$permisoAntiguo])) {
            $permisosNuevos[] = $mapeo[$permisoAntiguo];
        }
    }

    $role->givePermissionTo($permisosNuevos);
}
```

---

## 📞 Soporte

Para dudas o problemas con el sistema de permisos:

1. Revisar esta documentación
2. Verificar logs de Laravel: `storage/logs/laravel.log`
3. Limpiar cache de permisos: `php artisan permission:cache-reset`
4. Consultar documentación de Spatie: https://spatie.be/docs/laravel-permission

---

**Última actualización:** {{ now()->format('Y-m-d H:i:s') }}
