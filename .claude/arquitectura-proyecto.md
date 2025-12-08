# ARQUITECTURA DEL PROYECTO SIGEA v4

## 🏗️ Información General del Proyecto

### Identificación
- **Nombre:** SIGEA v4 - Sistema Integrado de Gestión Empresarial
- **Versión:** 4.0
- **Framework:** Laravel 12.26.4
- **PHP:** 8.4.15
- **Base de Datos:** PostgreSQL con múltiples schemas
- **Frontend:** Livewire 3 + AdminLTE 3

### Ubicación
```
Directorio: c:\Users\rm_mi\Herd\sigea_v4
Servidor: Laravel Herd (ambiente local Windows)
```

### Contexto de Negocio
**Cliente:** AGUATERÍA Y PLOMERÍA SIGEA S.A. (Paraguay)
- RUC: 80012345-6
- Actividad: Ferretería especializada en aguatería y plomería
- Ubicación: 3 sucursales (Asunción, San Lorenzo, Ñemby)
- Moneda: Solo PYG (Guaraníes paraguayos)
- IVA: 10% (general) / 5% (canasta básica)

---

## 🗄️ Arquitectura de Base de Datos

### Estrategia Multi-Schema PostgreSQL

```
┌─────────────────────────────────────────────────┐
│              PostgreSQL Database                 │
│                  sigea_v4                        │
├─────────────────────────────────────────────────┤
│                                                  │
│  ┌─────────────┐  ┌──────────┐  ┌───────────┐  │
│  │   public    │  │ empresa  │  │   stock   │  │
│  │  (Sistema)  │  │ (Módulo) │  │ (Módulo)  │  │
│  └─────────────┘  └──────────┘  └───────────┘  │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Schema `public` - Tablas del Sistema

**Propósito:** Gestión de usuarios, permisos, roles y configuración del sistema

**Tablas principales:**
```
users                    - Usuarios del sistema
permissions              - Permisos granulares
roles                    - Roles de usuario
model_has_permissions    - Asignación directa de permisos
model_has_roles          - Asignación de roles a usuarios
role_has_permissions     - Permisos por rol
SYS_MODULOS              - Módulos del sistema
SYS_SUB_MODULOS          - Sub-módulos
audits                   - Auditoría de cambios
cache, jobs, sessions    - Tablas de Laravel
```

**Características:**
- Spatie Laravel Permission para roles y permisos
- Owen-IT Auditing para trazabilidad
- Soft deletes en tabla users
- Foreign keys a otros schemas deben evitarse (usar nullable)

### Schema `empresa` - Módulo Empresa

**Propósito:** Gestión de estructura empresarial, sucursales, depósitos y facturación

**Tablas principales:**
```
EMPRESA                           - Datos de la empresa
ACTIVIDADES_ECONOMICAS            - Códigos de actividad económica
EMPRESA_ACTIVIDAD_ECONOMICA       - Pivot: empresa <-> actividades
SUCURSALES                        - Sucursales de la empresa
DEPOSITOS                         - Depósitos por sucursal
PUNTOS_EXPEDICION                 - Puntos de venta/expedición
TIMBRADOS                         - Timbrados de facturación electrónica
```

**Convenciones:**
- Nombres de tablas en MAYÚSCULAS
- Campo `creadoPor` y `actualizadoPor` (auditoría manual)
- Soft deletes en entidades principales
- FK a `public.users` solo en tabla EMPRESA (punto de entrada)

**Relaciones clave:**
```
EMPRESA (1) ──→ (N) SUCURSALES
SUCURSALES (1) ──→ (N) DEPOSITOS
SUCURSALES (1) ──→ (N) PUNTOS_EXPEDICION
EMPRESA (1) ──→ (N) TIMBRADOS
```

### Schema `stock` - Módulo Stock/Inventario

**Propósito:** Gestión de productos, inventario y movimientos de stock

**Tablas principales:**

**Nivel 1 - Catálogos Base:**
```
MARCAS                  - Marcas de productos (Grundfos, Tigre, etc.)
UNIDADES_MEDIDA         - Unidades de medida (UN, m, L, kg, etc.)
CATEGORIAS              - Categorías jerárquicas (3 niveles)
ATRIBUTOS_TIPO          - Tipos de atributos (Potencia, Voltaje, etc.)
```

**Nivel 2 - Productos:**
```
PRODUCTOS               - Productos del catálogo
PRECIOS                 - Histórico de precios por producto
ATRIBUTOS_PRODUCTO      - Valores de atributos por producto
IMAGENES_PRODUCTO       - Imágenes de productos
PRODUCTO_PROVEEDOR      - Relación producto-proveedor (futuro)
```

**Nivel 3 - Gestión Stock:**
```
STOCK                   - Stock actual por producto/depósito
MOVIMIENTOS_STOCK       - Histórico de movimientos
```

**Convenciones:**
- Nombres de tablas en MAYÚSCULAS
- `creadoPor` siempre NULL (evitar FK cross-schema)
- Soft deletes en PRODUCTOS, CATEGORIAS
- Unique constraint: (producto_id, deposito_id) en STOCK

**Relaciones clave:**
```
CATEGORIAS (recursiva) - parent_id para jerarquía
PRODUCTOS (N) ──→ (1) MARCA
PRODUCTOS (N) ──→ (1) CATEGORIA
PRODUCTOS (N) ──→ (1) UNIDAD_MEDIDA
PRODUCTOS (1) ──→ (N) PRECIOS
PRODUCTOS (1) ──→ (N) ATRIBUTOS_PRODUCTO
PRODUCTOS (1) ──→ (N) IMAGENES_PRODUCTO
STOCK (N) ──→ (1) PRODUCTO
STOCK (N) ──→ (1) DEPOSITO (schema empresa)
MOVIMIENTOS_STOCK (N) ──→ (1) STOCK
```

---

## 📁 Estructura de Directorios

### Backend (Laravel)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/              # Autenticación
│   │   ├── Empresa/           # Controladores módulo Empresa
│   │   │   ├── EmpresaController.php
│   │   │   ├── SucursalController.php
│   │   │   ├── DepositoController.php
│   │   │   └── TimbradoController.php
│   │   └── Stock/             # Controladores módulo Stock
│   │       ├── MarcaController.php
│   │       ├── UnidadMedidaController.php
│   │       ├── CategoriaController.php
│   │       └── ProductoController.php
│   │
│   └── Middleware/            # Middlewares personalizados
│
├── Livewire/
│   ├── Empresa/               # Componentes Livewire Empresa
│   │   ├── EmpresaIndex.php
│   │   ├── EmpresaCreate.php
│   │   ├── SucursalIndex.php
│   │   └── ...
│   └── Stock/                 # Componentes Livewire Stock
│       ├── MarcaIndex.php
│       ├── MarcaCreate.php
│       ├── CategoriaIndex.php
│       └── ...
│
├── Models/
│   ├── User.php               # Modelo de usuario (schema public)
│   ├── Empresa/               # Modelos módulo Empresa
│   │   ├── Empresa.php
│   │   ├── Sucursal.php
│   │   ├── Deposito.php
│   │   ├── Timbrado.php
│   │   └── ActividadEconomica.php
│   └── Stock/                 # Modelos módulo Stock
│       ├── Marca.php
│       ├── UnidadMedida.php
│       ├── Categoria.php
│       ├── Producto.php
│       ├── Precio.php
│       ├── AtributoTipo.php
│       └── Stock.php
│
└── Traits/                    # Traits reutilizables
    └── HasAuditFields.php     # Para campos creadoPor/actualizadoPor
```

### Frontend (Views + Livewire)

```
resources/views/
├── layouts/
│   └── app.blade.php          # Layout principal AdminLTE
│
├── empresa/                   # Vistas contenedoras Empresa
│   ├── empresas/
│   │   └── index.blade.php    # Solo llama a <livewire:empresa.empresa-index />
│   ├── sucursales/
│   │   └── index.blade.php
│   └── depositos/
│       └── index.blade.php
│
├── stock/                     # Vistas contenedoras Stock
│   ├── marcas/
│   │   └── index.blade.php    # Solo llama a <livewire:stock.marca-index />
│   ├── unidades/
│   │   └── index.blade.php
│   ├── categorias/
│   │   └── index.blade.php
│   └── productos/
│       └── index.blade.php
│
└── livewire/
    ├── empresa/               # Vistas de componentes Livewire Empresa
    │   ├── empresa-index.blade.php
    │   ├── empresa-create.blade.php
    │   └── ...
    └── stock/                 # Vistas de componentes Livewire Stock
        ├── marca-index.blade.php
        ├── marca-create.blade.php
        ├── categoria-index.blade.php
        └── ...
```

### Migraciones y Seeders

```
database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 2025_09_02_140441_create_permission_tables.php
│   ├── 2025_10_30_141807_create_SYS_MODULOS_table.php
│   │
│   ├── 2025_12_06_004854_create_empresa_schema.php
│   ├── 2025_12_06_004906_create_empresa_table.php
│   ├── 2025_12_06_005047_create_sucursales_table.php
│   ├── 2025_12_06_005053_create_depositos_table.php
│   ├── 2025_12_06_005100_create_puntos_expedicion_table.php
│   ├── 2025_12_06_005104_create_timbrados_table.php
│   │
│   ├── 2025_12_06_211129_create_stock_schema.php
│   ├── 2025_12_06_212529_create_marcas_table.php
│   ├── 2025_12_06_212633_create_unidades_medida_table.php
│   ├── 2025_12_06_212717_create_categorias_table.php
│   ├── 2025_12_07_010238_create_productos_table.php
│   ├── 2025_12_07_013601_create_atributos_tipo_table.php
│   ├── 2025_12_07_013714_create_atributos_producto_table.php
│   ├── 2025_12_07_031804_create_imagenes_producto_table.php
│   ├── 2025_12_07_035022_create_stocks_table.php
│   └── 2025_12_07_035211_create_movimientos_stock_table.php
│
└── seeders/
    ├── DatabaseSeeder.php
    ├── RolYPermisoSeeder.php
    ├── ParaguayDemoSeeder.php    # Seeder maestro para datos demo
    ├── Empresa/
    │   ├── EmpresaSeeder.php
    │   ├── SucursalSeeder.php
    │   ├── DepositoSeeder.php
    │   └── TimbradoSeeder.php
    └── Stock/
        ├── MarcaSeeder.php
        ├── UnidadMedidaSeeder.php
        ├── CategoriaSeeder.php
        ├── AtributoTipoSeeder.php
        ├── ProductoSeeder.php
        └── StockInicialSeeder.php
```

### Rutas

```
routes/
├── web.php                    # Rutas principales + auth
├── empresas.php               # Rutas del módulo Empresa
└── stocks.php                 # Rutas del módulo Stock
```

**Configuración en RouteServiceProvider o bootstrap/app.php:**
```php
Route::middleware(['web', 'auth'])
    ->prefix('empresas')
    ->group(base_path('routes/empresas.php'));

Route::middleware(['web', 'auth'])
    ->prefix('stocks')
    ->group(base_path('routes/stocks.php'));
```

---

## 🎨 Stack Tecnológico

### Backend
- **Laravel:** 12.26.4
- **PHP:** 8.4.15
- **Livewire:** 3.x (componentes reactivos)
- **Spatie Laravel Permission:** Roles y permisos
- **Owen-IT Auditing:** Auditoría de modelos
- **Eloquent ORM:** Interacción con base de datos

### Frontend
- **AdminLTE:** 3.x (tema de administración)
- **Alpine.js:** (incluido con Livewire)
- **Bootstrap:** 4.x (incluido con AdminLTE)
- **Font Awesome:** Iconos
- **jQuery:** (incluido con AdminLTE)

### Base de Datos
- **PostgreSQL:** Versión compatible con Laravel 12
- **Múltiples Schemas:** public, empresa, stock
- **Search Path:** Configurado en `config/database.php`

---

## 🔧 Patrones de Diseño Implementados

### 1. Patrón Container-Component (Livewire)

**Estructura:**
```
Controller (solo retorna vista)
    ↓
Vista Contenedora (Blade simple)
    ↓
Componente Livewire (toda la lógica)
    ↓
Vista del Componente (UI reactiva)
```

**Ejemplo:**
```php
// Controller
public function index()
{
    return view('stock.marcas.index');
}

// Vista Contenedora (resources/views/stock/marcas/index.blade.php)
<livewire:stock.marca-index />

// Componente Livewire (app/Livewire/Stock/MarcaIndex.php)
class MarcaIndex extends Component
{
    public function render()
    {
        return view('livewire.stock.marca-index');
    }
}
```

### 2. Repository Pattern (Implícito con Eloquent)

Los modelos Eloquent actúan como repositorios:
- Scopes para queries complejas
- Relaciones definidas en modelos
- Mutators y Accessors para transformación de datos

### 3. Soft Deletes

Entidades principales usan eliminación lógica:
```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;
}
```

### 4. Auditing Pattern

Todas las entidades importantes auditan cambios:
```php
use OwenIt\Auditing\Contracts\Auditable;

class Producto extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
```

### 5. Single Responsibility Principle

- **Controllers:** Solo retornan vistas
- **Livewire Components:** Lógica de negocio y presentación
- **Models:** Relaciones, scopes, validaciones de datos
- **Seeders:** Datos de prueba organizados por módulo

---

## 🔐 Seguridad y Permisos

### Sistema de Roles y Permisos (Spatie)

**Estructura:**
```
Usuario
  ├── tiene N roles
  └── tiene N permisos directos

Rol
  └── tiene N permisos

Permiso
  ├── pertenece a 1 módulo
  └── pertenece a 1 sub-módulo
```

**Nomenclatura de permisos:**
```
[modulo].[accion]
Ejemplos:
- stock.crear
- stock.editar
- stock.eliminar
- empresa.ver
```

**Middleware:**
```php
Route::middleware(['role:admin'])->group(function () {
    // Rutas solo para admin
});

Route::middleware(['permission:stock.crear'])->group(function () {
    // Rutas que requieren permiso específico
});
```

### Campos de Auditoría Manual

**En schema `empresa`:**
```php
'creadoPor' => User::first()->id,      // FK a public.users
'actualizadoPor' => auth()->id(),
```

**En schema `stock`:**
```php
'creadoPor' => null,                   // ⚠️ Siempre null (evitar FK cross-schema)
'actualizadoPor' => null,
```

---

## 📊 Convenciones de Código

### Nomenclatura

**Tablas:**
- Schema `public`: snake_case minúsculas (`users`, `permissions`)
- Schema `empresa`: MAYÚSCULAS (`EMPRESA`, `SUCURSALES`)
- Schema `stock`: MAYÚSCULAS (`PRODUCTOS`, `MARCAS`)

**Modelos:**
- PascalCase singular (`User`, `Empresa`, `Producto`)
- Namespace por módulo (`App\Models\Stock\Producto`)

**Componentes Livewire:**
- PascalCase con sufijo acción (`MarcaIndex`, `ProductoCreate`)
- Namespace por módulo (`App\Livewire\Stock\MarcaIndex`)

**Vistas Livewire:**
- kebab-case (`marca-index.blade.php`, `producto-create.blade.php`)

**Rutas:**
- kebab-case (`marcas`, `unidades-medida`)
- Agrupadas por módulo en archivos separados

### Campos Estándar en Modelos

**Obligatorios:**
```php
'activo' => boolean (default true)
'creadoPor' => bigint nullable
'actualizadoPor' => bigint nullable
'created_at' => timestamp
'updated_at' => timestamp
```

**Opcionales según entidad:**
```php
'deleted_at' => timestamp nullable (SoftDeletes)
'descripcion' => text nullable
'observaciones' => text nullable
```

### Validaciones en Livewire

```php
protected $rules = [
    'codigo' => 'required|unique:stock.MARCAS,codigo|max:20',
    'nombre' => 'required|max:100',
    'activo' => 'boolean',
];

protected $messages = [
    'codigo.required' => 'El código es obligatorio',
    'codigo.unique' => 'Este código ya existe',
];
```

---

## 🚀 Comandos Útiles

### Migraciones
```bash
# Ejecutar todas las migraciones
php artisan migrate

# Rollback última migración
php artisan migrate:rollback

# Refresh completo (⚠️ DESTRUYE DATOS)
php artisan migrate:fresh

# Fresh + seeders
php artisan migrate:fresh --seed
```

### Seeders
```bash
# Ejecutar todos los seeders
php artisan db:seed

# Ejecutar seeder específico
php artisan db:seed --class=ParaguayDemoSeeder
php artisan db:seed --class="Database\\Seeders\\Stock\\MarcaSeeder"
```

### Livewire
```bash
# Crear componente Livewire
php artisan make:livewire Stock/MarcaIndex

# Listar componentes
php artisan livewire:list
```

### Cache y Configuración
```bash
# Limpiar cache de configuración
php artisan config:clear

# Limpiar cache de rutas
php artisan route:clear

# Limpiar cache de vistas
php artisan view:clear

# Limpiar todo
php artisan optimize:clear
```

### Debugging
```bash
# Ver rutas registradas
php artisan route:list

# Ver rutas de un módulo específico
php artisan route:list --path=stocks

# Ejecutar Tinker
php artisan tinker

# En Tinker, probar modelo
>>> App\Models\Stock\Marca::count()
>>> App\Models\Stock\Producto::with('marca')->first()
```

---

## 🔄 Flujo de Desarrollo

### Para Agregar Nueva Funcionalidad

1. **Planificar**
   - Definir tablas necesarias
   - Identificar relaciones
   - Diseñar UI/UX

2. **Crear Migración**
   ```bash
   php artisan make:migration create_ejemplo_table
   ```

3. **Crear Modelo**
   ```bash
   php artisan make:model Stock/Ejemplo
   ```

4. **Crear Controlador (opcional)**
   ```bash
   php artisan make:controller Stock/EjemploController
   ```

5. **Crear Componentes Livewire**
   ```bash
   php artisan make:livewire Stock/EjemploIndex
   php artisan make:livewire Stock/EjemploCreate
   ```

6. **Crear Rutas**
   - Agregar en `routes/stocks.php`

7. **Crear Vistas**
   - Vista contenedora en `resources/views/stock/ejemplos/`
   - Vistas de componentes en `resources/views/livewire/stock/`

8. **Crear Seeder (opcional)**
   ```bash
   php artisan make:seeder Stock/EjemploSeeder
   ```

9. **Probar**
   ```bash
   php artisan migrate
   php artisan db:seed --class="Database\\Seeders\\Stock\\EjemploSeeder"
   ```

---

## 📚 Documentación Relacionada

- [seeders-estructura.md](.claude/seeders-estructura.md) - Guía completa de seeders
- [stocks-modelo-conceptual.md](.claude/stocks-modelo-conceptual.md) - Modelo conceptual del módulo Stock
- [stocks-plan-implementacion.md](.claude/stocks-plan-implementacion.md) - Plan de implementación por bloques

---

**Última actualización:** 2025-12-07
**Versión:** 1.0
**Autor:** Claude Code
