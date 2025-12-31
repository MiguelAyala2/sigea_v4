# MODELO CONCEPTUAL - SCHEMA PUBLIC
## Sistema de Autenticación, Autorización y Usuarios - SIGEA v4

---

## ÍNDICE
1. [Filosofía del Schema](#1-filosofía-del-schema)
2. [Jerarquía Conceptual](#2-jerarquía-conceptual)
3. [Modelo Lógico de Datos](#3-modelo-lógico-de-datos)
4. [Sistema de Permisos (Spatie Permission)](#4-sistema-de-permisos)
5. [Flujos de Trabajo](#5-flujos-de-trabajo)
6. [Integración con Módulos](#6-integración-con-módulos)
7. [Seguridad y Mejores Prácticas](#7-seguridad-y-mejores-prácticas)
8. [Configuración y Seeders](#8-configuración-y-seeders)

---

## 1. FILOSOFÍA DEL SCHEMA

### 1.1 Principios Fundamentales

- **Laravel Authentication**: Sistema nativo de Laravel para autenticación
- **Spatie Permission**: Paquete robusto para gestión de roles y permisos
- **Schema PUBLIC**: Todas las tablas de autenticación en schema por defecto de PostgreSQL
- **Soft Deletes**: Eliminación lógica de usuarios
- **Auditoría**: Trazabilidad de quién crea/modifica cada usuario
- **Control de Acceso Basado en Roles (RBAC)**: Usuarios → Roles → Permisos

### 1.2 Componentes del Sistema

```
public (SCHEMA POSTGRESQL)
├── Autenticación (users, password_reset_tokens, sessions)
├── Autorización (roles, permissions, tablas pivot)
├── Estructura del Sistema (SYS_MODULOS, SYS_SUB_MODULOS)
└── Cache y Soporte (cache, cache_locks, jobs, job_batches, failed_jobs)
```

---

## 2. JERARQUÍA CONCEPTUAL

### 2.1 Nivel 1: Sistema de Módulos

#### SYS_MODULOS (Módulos del sistema)
```
SYS_MODULOS
├── Define los módulos principales del sistema
├── Ejemplos: EMPRESA, STOCK, COMPRAS, VENTAS, SERVICIOS
├── Agrupa permisos por módulo
└── Estructura del menú principal
```

**Características**:
- Tabla: `public.SYS_MODULOS`
- Campos: `id`, `modulo`, `created_at`, `updated_at`, `deleted_at`
- Soft deletes habilitado

#### SYS_SUB_MODULOS (Submódulos)
```
SYS_SUB_MODULOS
├── Subdivide los módulos principales
├── Ejemplos:
│   ├── STOCK → Productos, Categorías, Movimientos, Kardex
│   ├── VENTAS → Pedidos, Facturas, Caja, Cobranzas
│   └── SERVICIOS → Solicitudes, Diagnósticos, Órdenes
├── Organización jerárquica del sistema
└── Agrupa permisos por funcionalidad
```

**Características**:
- Tabla: `public.SYS_SUB_MODULOS`
- Foreign key: `modulo_id` → SYS_MODULOS
- Campos: `id`, `modulo_id`, `sub_modulo`, `created_at`, `updated_at`, `deleted_at`

### 2.2 Nivel 2: Usuarios

#### USERS (Usuarios del sistema)
```
USERS
├── Usuarios con acceso al sistema
├── Autenticación mediante email o usuario
├── Tipos implícitos:
│   ├── Administradores (rol admin)
│   ├── Gerentes (rol gerente)
│   ├── Vendedores (rol vendedor)
│   ├── Técnicos (rol tecnico)
│   └── Cajeros (rol cajero)
├── Estado activo/inactivo
├── Trazabilidad: último acceso
└── Relaciones con todos los módulos (creadoPor, actualizadoPor)
```

**Características**:
- Tabla: `public.users`
- Campos de identificación:
  - `name` (nombre completo)
  - `usuario` (username único)
  - `email` (único, puede ser null)
  - `nro_cedula` (único, puede ser null)
  - `nro_celular` (único, puede ser null)
- Autenticación: `password`, `remember_token`
- Control: `activo` (boolean), `ultimo_acceso` (datetime)
- Auditoría: `creadoPor`, `actualizadoPor` (auto-referencia)
- Timestamps: `created_at`, `updated_at`, `deleted_at`
- Traits: HasRoles (Spatie), Auditable (Owen-IT)

### 2.3 Nivel 3: Roles y Permisos (Spatie Permission)

#### ROLES (Roles del sistema)
```
ROLES
├── Agrupación de permisos
├── Ejemplos predefinidos:
│   ├── admin (acceso total)
│   ├── gerente (gestión general)
│   ├── vendedor (ventas y clientes)
│   ├── tecnico (servicios técnicos)
│   ├── cajero (caja y cobranzas)
│   └── comprador (compras y proveedores)
├── Un usuario puede tener múltiples roles
├── Un rol puede tener múltiples permisos
└── Soft deletes habilitado
```

**Características**:
- Tabla: `public.roles` (Spatie)
- Campos: `id`, `name`, `guard_name`
- Auditoría: `creadoPor`, `actualizadoPor`
- Unique constraint: `(name, guard_name)`

#### PERMISSIONS (Permisos granulares)
```
PERMISSIONS
├── Permisos específicos del sistema
├── Formato: modulo.accion (ej: productos.crear)
├── Organización:
│   ├── Por módulo (modulo_id)
│   ├── Por submódulo (sub_modulo_id)
│   └── Por acción (crear, editar, eliminar, ver)
├── Ejemplos:
│   ├── productos.ver
│   ├── productos.crear
│   ├── productos.editar
│   ├── productos.eliminar
│   ├── facturas.ver
│   ├── facturas.emitir
│   └── caja.abrir
└── Un permiso puede asignarse a múltiples roles
```

**Características**:
- Tabla: `public.permissions` (Spatie)
- Campos: `id`, `name`, `guard_name`
- Organización: `modulo_id` → SYS_MODULOS, `sub_modulo_id` → SYS_SUB_MODULOS
- Auditoría: `creadoPor`, `actualizadoPor`
- Unique constraint: `(name, guard_name)`

### 2.4 Nivel 4: Tablas Pivot (Relaciones N:M)

#### MODEL_HAS_ROLES (Usuarios ↔ Roles)
```
MODEL_HAS_ROLES
├── Asigna roles a usuarios
├── Relación polimórfica (model_type = 'App\Models\User')
├── Un usuario puede tener múltiples roles
└── Primary key compuesta: (role_id, model_id, model_type)
```

**Características**:
- Tabla: `public.model_has_roles` (Spatie)
- Campos: `role_id`, `model_type`, `model_id`

#### MODEL_HAS_PERMISSIONS (Usuarios ↔ Permisos directos)
```
MODEL_HAS_PERMISSIONS
├── Asigna permisos directos a usuarios
├── Permisos adicionales a los del rol
├── Uso: excepciones o permisos temporales
└── Primary key compuesta: (permission_id, model_id, model_type)
```

**Características**:
- Tabla: `public.model_has_permissions` (Spatie)
- Campos: `permission_id`, `model_type`, `model_id`

#### ROLE_HAS_PERMISSIONS (Roles ↔ Permisos)
```
ROLE_HAS_PERMISSIONS
├── Asigna permisos a roles
├── Define qué puede hacer cada rol
├── Un rol puede tener múltiples permisos
└── Primary key compuesta: (permission_id, role_id)
```

**Características**:
- Tabla: `public.role_has_permissions` (Spatie)
- Campos: `permission_id`, `role_id`

### 2.5 Nivel 5: Soporte de Autenticación

#### PASSWORD_RESET_TOKENS
```
PASSWORD_RESET_TOKENS
├── Tokens para recuperación de contraseña
├── Generado al solicitar "Olvidé mi contraseña"
├── Expiración configurable (default: 60 minutos)
└── Se elimina al cambiar contraseña
```

**Características**:
- Tabla: `public.password_reset_tokens`
- Primary key: `email`
- Campos: `token`, `created_at`

#### SESSIONS
```
SESSIONS
├── Sesiones activas de usuarios
├── Almacenamiento de sesión en base de datos
├── Control de sesiones concurrentes
└── Limpieza automática de sesiones antiguas
```

**Características**:
- Tabla: `public.sessions`
- Primary key: `id` (session ID)
- Campos: `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`

### 2.6 Nivel 6: Sistema de Caché y Jobs

#### CACHE, CACHE_LOCKS
```
CACHE
├── Almacenamiento de caché en base de datos
├── Usado por Spatie Permission para caché de permisos
└── Optimización de consultas frecuentes

CACHE_LOCKS
├── Locks distribuidos para prevenir race conditions
└── Usado en operaciones críticas
```

#### JOBS, JOB_BATCHES, FAILED_JOBS
```
JOBS
├── Cola de trabajos en segundo plano
├── Procesamiento asíncrono
└── Ejemplos: envío de emails, generación de reportes

JOB_BATCHES
├── Agrupación de trabajos relacionados
└── Permite procesar múltiples jobs como una unidad

FAILED_JOBS
├── Trabajos que fallaron
├── Permite reintentar manualmente
└── Debug de errores en jobs
```

---

## 3. MODELO LÓGICO DE DATOS

### 3.1 Diagrama Entidad-Relación (Texto)

```
[SYS_MODULOS] 1----N [SYS_SUB_MODULOS]
      |                      |
      |                      |
      N                      N
      |                      |
      |                      |
[PERMISSIONS]───┐
      |         │
      N         │
      |         │
[ROLE_HAS_PERMISSIONS] N----1 [ROLES]
                                 |
                                 N
                                 |
                          [MODEL_HAS_ROLES]
                                 |
                                 N
                                 |
                              [USERS]
                                 │
                                 N
                                 │
                          [MODEL_HAS_PERMISSIONS]
                                 │
                                 N
                                 │
                          [PERMISSIONS]

[USERS] ──┬─→ [SESSIONS] (sesiones activas)
          ├─→ [PASSWORD_RESET_TOKENS] (recuperación)
          ├─→ creadoPor/actualizadoPor (auto-referencia)
          └─→ Todos los módulos (empresa, stock, compras, ventas, servicios)
```

### 3.2 Estructura de Tablas

#### Tabla: users
```sql
id                          BIGSERIAL PRIMARY KEY
name                        VARCHAR(100)
usuario                     VARCHAR(45)
email                       VARCHAR(100) UNIQUE
nro_cedula                  VARCHAR(45) UNIQUE
nro_celular                 VARCHAR(30) UNIQUE
observacion                 VARCHAR(255)
email_verified_at           TIMESTAMP
password                    VARCHAR
activo                      BOOLEAN DEFAULT true
ultimo_acceso               DATETIME
remember_token              VARCHAR(100)
created_at, updated_at      TIMESTAMP
creadoPor                   BIGINT → users (SET NULL)
actualizadoPor              BIGINT → users (SET NULL)
deleted_at                  TIMESTAMP
```

#### Tabla: roles (Spatie)
```sql
id                          BIGSERIAL PRIMARY KEY
name                        VARCHAR
guard_name                  VARCHAR
creadoPor                   BIGINT → users (SET NULL)
actualizadoPor              BIGINT → users (SET NULL)
created_at, updated_at      TIMESTAMP
deleted_at                  TIMESTAMP

UNIQUE (name, guard_name)
```

#### Tabla: permissions (Spatie + Custom)
```sql
id                          BIGSERIAL PRIMARY KEY
name                        VARCHAR
guard_name                  VARCHAR
modulo_id                   BIGINT → SYS_MODULOS (CASCADE)
sub_modulo_id               BIGINT → SYS_SUB_MODULOS (CASCADE)
creadoPor                   BIGINT → users (SET NULL)
actualizadoPor              BIGINT → users (SET NULL)
created_at, updated_at      TIMESTAMP

UNIQUE (name, guard_name)
```

#### Tabla: SYS_MODULOS
```sql
id                          BIGSERIAL PRIMARY KEY
modulo                      VARCHAR(50)
created_at, updated_at      TIMESTAMP
deleted_at                  TIMESTAMP
```

#### Tabla: SYS_SUB_MODULOS
```sql
id                          BIGSERIAL PRIMARY KEY
modulo_id                   BIGINT → SYS_MODULOS
sub_modulo                  VARCHAR(100)
created_at, updated_at      TIMESTAMP
deleted_at                  TIMESTAMP
```

#### Tabla: model_has_roles (Spatie)
```sql
role_id                     BIGINT → roles (CASCADE)
model_type                  VARCHAR
model_id                    BIGINT

PRIMARY KEY (role_id, model_id, model_type)
INDEX (model_id, model_type)
```

#### Tabla: model_has_permissions (Spatie)
```sql
permission_id               BIGINT → permissions (CASCADE)
model_type                  VARCHAR
model_id                    BIGINT

PRIMARY KEY (permission_id, model_id, model_type)
INDEX (model_id, model_type)
```

#### Tabla: role_has_permissions (Spatie)
```sql
permission_id               BIGINT → permissions (CASCADE)
role_id                     BIGINT → roles (CASCADE)

PRIMARY KEY (permission_id, role_id)
```

---

## 4. SISTEMA DE PERMISOS (SPATIE PERMISSION)

### 4.1 Flujo de Verificación de Permisos

```
Usuario intenta realizar acción
        │
        ▼
¿Usuario tiene permiso directo?
        │
        ├─→ SÍ → ✅ Permitir
        │
        NO
        │
        ▼
¿Usuario tiene rol con el permiso?
        │
        ├─→ SÍ → ✅ Permitir
        │
        NO
        │
        ▼
❌ Denegar acceso
```

### 4.2 Métodos de Asignación

#### Asignar Rol a Usuario
```php
$user = User::find(1);
$user->assignRole('admin');
$user->assignRole(['vendedor', 'cajero']); // Múltiples roles
```

#### Asignar Permiso a Rol
```php
$role = Role::findByName('vendedor');
$role->givePermissionTo('facturas.ver');
$role->givePermissionTo(['facturas.crear', 'facturas.emitir']);
```

#### Asignar Permiso Directo a Usuario
```php
$user->givePermissionTo('productos.editar');
```

### 4.3 Métodos de Verificación

#### En Controladores/Middleware
```php
// Verificar permiso
if ($user->can('facturas.emitir')) {
    // Permitir acción
}

// Verificar rol
if ($user->hasRole('admin')) {
    // Usuario es admin
}

// Verificar cualquiera de varios permisos
if ($user->hasAnyPermission(['facturas.ver', 'facturas.crear'])) {
    // Usuario tiene al menos uno
}

// Verificar todos los permisos
if ($user->hasAllPermissions(['facturas.ver', 'facturas.crear'])) {
    // Usuario tiene todos
}
```

#### En Blade
```blade
@can('facturas.emitir')
    <button>Emitir Factura</button>
@endcan

@role('admin')
    <a href="/admin/settings">Configuración</a>
@endrole
```

#### En Rutas
```php
Route::middleware(['permission:facturas.emitir'])->group(function () {
    Route::post('/facturas', [FacturaController::class, 'store']);
});

Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
});
```

### 4.4 Estructura de Permisos Recomendada

```
MÓDULO.SUBMÓDULO.ACCIÓN

Ejemplos:
- empresa.sucursales.ver
- empresa.sucursales.crear
- empresa.sucursales.editar
- empresa.sucursales.eliminar

- stock.productos.ver
- stock.productos.crear
- stock.productos.editar
- stock.productos.eliminar
- stock.movimientos.ver
- stock.movimientos.crear

- ventas.pedidos.ver
- ventas.pedidos.crear
- ventas.facturas.ver
- ventas.facturas.emitir
- ventas.caja.abrir
- ventas.caja.cerrar

- servicios.solicitudes.ver
- servicios.solicitudes.crear
- servicios.diagnosticos.crear
- servicios.ordenes.asignar
```

---

## 5. FLUJOS DE TRABAJO

### 5.1 Flujo: Crear Usuario y Asignar Rol

```
┌─────────────────────────────────────────────────┐
│ INICIO: Administrador crea nuevo usuario       │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 1. CREAR USUARIO                                │
│ Ubicación: Configuración → Usuarios → Nuevo    │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ 1.1 Datos Usuario    │
      │ - Nombre completo    │
      │ - Usuario (username) │
      │ - Email              │
      │ - Cédula             │
      │ - Celular            │
      │ - Contraseña         │
      │ - Confirmar password │
      └──────────┬───────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ 1.2 [GUARDAR]        │
      │ Hash automático      │
      │ de contraseña        │
      └──────────┬───────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 2. ASIGNAR ROL                                  │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ 2.1 Seleccionar Rol  │
      │ ☐ Admin              │
      │ ☐ Gerente            │
      │ ☑ Vendedor           │
      │ ☐ Técnico            │
      │ ☐ Cajero             │
      └──────────┬───────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ 2.2 [ASIGNAR]        │
      │ $user->assignRole()  │
      └──────────┬───────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 3. PERMISOS AUTOMÁTICOS                         │
│ ✅ Usuario hereda permisos del rol "vendedor": │
│    - ventas.pedidos.ver                         │
│    - ventas.pedidos.crear                       │
│    - ventas.facturas.ver                        │
│    - ventas.facturas.emitir                     │
│    - servicios.clientes.ver                     │
│    - servicios.clientes.crear                   │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ 3.1 Notificar Usuario│
      │ Email con credenciales│
      └──────────┬───────────┘
                 │
                 ▼
      ┌────────────────┐
      │   FIN FLUJO    │
      │ ✅ Usuario     │
      │    Creado      │
      └────────────────┘
```

### 5.2 Flujo: Login y Verificación de Permisos

```
┌─────────────────────────────────────────────────┐
│ INICIO: Usuario intenta acceder al sistema     │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 1. PANTALLA DE LOGIN                            │
│ /login                                          │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ 1.1 Ingresar         │
      │ - Usuario o Email    │
      │ - Contraseña         │
      │ - Recordarme         │
      └──────────┬───────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ 1.2 [INICIAR SESIÓN] │
      └──────────┬───────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 2. VALIDACIÓN                                   │
└────────────────┬────────────────────────────────┘
                 │
        ┌────────┴────────┐
        │                 │
   ✅ Válido        ❌ Inválido
        │                 │
        │                 ▼
        │      ┌──────────────────────┐
        │      │ Error: Credenciales  │
        │      │ incorrectas          │
        │      │ [Volver al login]    │
        │      └──────────────────────┘
        │
        ▼
┌─────────────────────────────────────────────────┐
│ 3. VERIFICAR ESTADO                             │
└────────────────┬────────────────────────────────┘
                 │
        ┌────────┴────────┐
        │                 │
   activo=true     activo=false
        │                 │
        │                 ▼
        │      ┌──────────────────────┐
        │      │ Error: Usuario       │
        │      │ desactivado          │
        │      │ [Contactar admin]    │
        │      └──────────────────────┘
        │
        ▼
┌─────────────────────────────────────────────────┐
│ 4. CREAR SESIÓN                                 │
│ ✅ Genera session ID                            │
│ ✅ Guarda en tabla sessions                     │
│ ✅ Registra último_acceso                       │
│ ✅ Carga roles y permisos en sesión             │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 5. REDIRIGIR A DASHBOARD                        │
│ /dashboard o /home                              │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 6. RENDERIZAR MENÚ SEGÚN PERMISOS               │
│ Sistema verifica permisos para cada ítem:      │
│                                                 │
│ @can('ventas.pedidos.ver')                      │
│   ✅ Mostrar "Pedidos"                          │
│ @endcan                                         │
│                                                 │
│ @can('stock.productos.ver')                     │
│   ✅ Mostrar "Productos"                        │
│ @endcan                                         │
│                                                 │
│ @can('caja.abrir')                              │
│   ❌ No mostrar "Caja" (no tiene permiso)       │
│ @endcan                                         │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
      ┌────────────────┐
      │ Usuario navega │
      │ según permisos │
      └────────────────┘
```

### 5.3 Flujo: Olvidé mi Contraseña

```
┌─────────────────────────────────────────────────┐
│ INICIO: Usuario olvidó contraseña              │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 1. SOLICITAR RECUPERACIÓN                       │
│ /forgot-password                                │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ 1.1 Ingresar Email   │
      │ [_______________]    │
      │ [ENVIAR LINK]        │
      └──────────┬───────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 2. GENERAR TOKEN                                │
│ ✅ Genera token aleatorio                       │
│ ✅ Guarda en password_reset_tokens              │
│ ✅ Expiración: 60 minutos                       │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 3. ENVIAR EMAIL                                 │
│ ✅ Asunto: "Recuperación de contraseña"         │
│ ✅ Link: /reset-password?token=ABC123           │
│ ✅ Instrucciones de seguridad                   │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 4. USUARIO HACE CLIC EN LINK                    │
│ /reset-password?token=ABC123&email=user@mail    │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ 4.1 Validar Token    │
      └──────────┬───────────┘
                 │
        ┌────────┴────────┐
        │                 │
   ✅ Válido        ❌ Expirado
   (< 60 min)            │
        │                 ▼
        │      ┌──────────────────────┐
        │      │ Error: Link expirado │
        │      │ Solicitar nuevo link │
        │      └──────────────────────┘
        │
        ▼
┌─────────────────────────────────────────────────┐
│ 5. FORMULARIO NUEVA CONTRASEÑA                  │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
      ┌──────────────────────┐
      │ 5.1 Ingresar         │
      │ - Nueva contraseña   │
      │ - Confirmar contraseña│
      │ [CAMBIAR CONTRASEÑA] │
      └──────────┬───────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ 6. ACTUALIZAR CONTRASEÑA                        │
│ ✅ Hash nueva contraseña                        │
│ ✅ Actualizar en BD                             │
│ ✅ Eliminar token usado                         │
│ ✅ Notificar cambio exitoso                     │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
      ┌────────────────┐
      │ Redirigir a    │
      │ /login         │
      └────────────────┘
```

---

## 6. INTEGRACIÓN CON MÓDULOS

### 6.1 Usuarios en Todos los Módulos

Todos los módulos del sistema tienen foreign keys a `users`:

```
empresa.EMPRESA.creadoPor → users.id
empresa.SUCURSALES.creadoPor → users.id
stock.PRODUCTOS.creadoPor → users.id
stock.MOVIMIENTOS_STOCK.registrado_por → users.id
compras.FACTURAS_COMPRAS.creado_por → users.id
ventas.FACTURAS.creado_por → users.id
ventas.APERTURAS_CAJA.usuario_id → users.id
ventas.ORDENES_SERVICIO.tecnico_id → users.id (técnico asignado)
servicios.ORDENES_SERVICIO.tecnico_id → users.id
servicios.RECLAMOS.responsable_id → users.id
```

### 6.2 Permisos por Módulo

**EMPRESA**:
- empresa.sucursales.ver/crear/editar/eliminar
- empresa.depositos.ver/crear/editar/eliminar
- empresa.timbrados.ver/crear/editar/eliminar

**STOCK**:
- stock.productos.ver/crear/editar/eliminar
- stock.categorias.ver/crear/editar/eliminar
- stock.movimientos.ver/crear
- stock.ajustes.crear

**COMPRAS**:
- compras.proveedores.ver/crear/editar/eliminar
- compras.ordenes.ver/crear/aprobar
- compras.facturas.ver/registrar

**VENTAS**:
- ventas.pedidos.ver/crear/editar
- ventas.facturas.ver/crear/emitir/anular
- ventas.caja.abrir/cerrar
- ventas.cobranzas.ver/registrar

**SERVICIOS**:
- servicios.solicitudes.ver/crear
- servicios.diagnosticos.ver/crear
- servicios.ordenes.ver/crear/asignar
- servicios.reclamos.ver/gestionar

---

## 7. SEGURIDAD Y MEJORES PRÁCTICAS

### 7.1 Contraseñas

✅ **Implementado**:
- Hashing con bcrypt (Laravel default)
- Mínimo 8 caracteres (configurable)
- Validación de confirmación

⚠️ **Recomendado**:
- Política de contraseñas robustas (letras, números, símbolos)
- Expiración cada 90 días
- No reutilizar últimas 3 contraseñas

### 7.2 Sesiones

✅ **Implementado**:
- Almacenamiento en base de datos
- IP y User Agent registrados
- Limpieza automática de sesiones antiguas

⚠️ **Recomendado**:
- Timeout de sesión después de 30 minutos de inactividad
- Logout en todos los dispositivos al cambiar contraseña
- Límite de sesiones concurrentes

### 7.3 Auditoría

✅ **Implementado** (Owen-IT Auditing):
- Registro de cambios en todas las entidades
- Quién hizo qué y cuándo
- Valores antiguos y nuevos

### 7.4 Prevención de Ataques

✅ **Laravel protege contra**:
- SQL Injection (Eloquent ORM)
- XSS (Blade escapa automáticamente)
- CSRF (tokens en formularios)
- Session Fixation (regenera session ID)

---

## 8. CONFIGURACIÓN Y SEEDERS

### 8.1 Seeders Principales

**RolYPermisoSeeder**:
```php
// Crea módulos del sistema
SYS_MODULOS: EMPRESA, STOCK, COMPRAS, VENTAS, SERVICIOS

// Crea roles
ROLES: admin, gerente, vendedor, tecnico, cajero

// Crea permisos organizados por módulo
PERMISSIONS: 100+ permisos granulares

// Asigna permisos a roles
admin → todos los permisos
vendedor → solo ventas y clientes
tecnico → solo servicios
...
```

### 8.2 Usuario Inicial

Crear usuario administrador inicial:
```php
$admin = User::create([
    'name' => 'Administrador',
    'usuario' => 'admin',
    'email' => 'admin@sigea.com',
    'password' => Hash::make('password'),
    'activo' => true,
]);

$admin->assignRole('admin');
```

---

## RESUMEN EJECUTIVO

El schema PUBLIC proporciona:

- ✅ Autenticación robusta con Laravel
- ✅ Autorización granular con Spatie Permission
- ✅ Control de acceso basado en roles (RBAC)
- ✅ Organización jerárquica de permisos por módulos
- ✅ Auditoría completa de usuarios
- ✅ Trazabilidad en todos los módulos
- ✅ Seguridad implementada según mejores prácticas
- ✅ Escalable y mantenible

El sistema está diseñado para ser:

- **Seguro**: Múltiples capas de seguridad
- **Flexible**: Permisos granulares y roles adaptables
- **Integrado**: Usado por todos los módulos
- **Auditable**: Registro completo de acciones
- **Escalable**: Fácil agregar nuevos roles y permisos

---

**Fecha de documentación**: Diciembre 2025
**Versión del sistema**: SIGEA v4
**Framework**: Laravel 12.26.4 / PHP 8.4.15 / PostgreSQL
**Paquetes**: Spatie Permission v6, Owen-IT Auditing v13
**Estado**: Implementado y operativo
