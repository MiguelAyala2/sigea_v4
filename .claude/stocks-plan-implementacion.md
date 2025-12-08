<div align="center">
  <img src="sigea-logo.png" alt="SIGEA Logo" width="100"/>
</div>

# PLAN DE IMPLEMENTACIÓN MÓDULO STOCKS
## Desarrollo en Bloques Controlados con Recursos Reservados
### Sistema Integrado de Gestión de Inventario - SIGEA v4

---

## ÍNDICE

1. [Metodología de Trabajo](#metodología-de-trabajo)
2. [Control de Recursos](#control-de-recursos)
3. [Bloques de Implementación](#bloques-de-implementación)
   - [BLOQUE 1: Configuración Inicial y Schema](#bloque-1-configuración-inicial-y-schema)
   - [BLOQUE 2: Migraciones - Catálogos Base](#bloque-2-migraciones---catálogos-base-nivel-1)
   - [BLOQUE 3: Modelos - Catálogos Base](#bloque-3-modelos---catálogos-base)
   - [BLOQUE 4: Livewire + Vistas - Marcas](#bloque-4-livewire--vistas---marcas-crud-simple)
   - [BLOQUE 5: Livewire + Vistas - Unidades Medida](#bloque-5-livewire--vistas---unidades-medida-crud-simple)
   - [BLOQUE 6: Livewire + Vistas - Categorías](#bloque-6-livewire--vistas---categorías-crud-jerárquico)
   - [BLOQUE 7: Migraciones - Núcleo Productos](#bloque-7-migraciones---núcleo-productos-nivel-2)
   - [BLOQUE 8: Modelos - Núcleo Productos](#bloque-8-modelos---núcleo-productos)
   - [BLOQUE 9: Livewire - Productos Index](#bloque-9-livewire---productos-index)
   - [BLOQUE 10: Livewire - Productos Create](#bloque-10-livewire---productos-create-multipaso)
   - [BLOQUE 11: Migraciones - Gestión Stock](#bloque-11-migraciones---gestión-stock-nivel-3)
   - [BLOQUE 12: Modelos - Gestión Stock](#bloque-12-modelos---gestión-stock)
4. [Reserva para Resolución de Inconvenientes](#reserva-para-resolución-de-inconvenientes)
5. [Checklist General de Verificación](#checklist-general-de-verificación)

---

## METODOLOGÍA DE TRABAJO

### Proceso por Bloque

#### 1. ✅ CONFIRMAR
- Verificar que estás listo para el siguiente bloque
- Revisar recursos disponibles
- Confirmar comprensión de los objetivos del bloque

#### 2. 📝 IMPLEMENTAR
- Crear todos los archivos del bloque según especificación
- Seguir estrictamente las buenas prácticas documentadas
- Aplicar lecciones aprendidas del módulo Empresa

#### 3. 🧪 VERIFICAR
- Ejecutar comandos de verificación:
  - `php artisan migrate` (para migraciones)
  - `php artisan route:list` (para rutas)
  - `php artisan config:clear` (después de cambios en config)
  - Tinker para probar modelos si aplica

#### 4. ✔️ VALIDAR
- Confirmar que no hay errores antes de continuar
- Verificar relaciones Eloquent
- Probar validaciones en formularios
- Revisar que las vistas se renderizan correctamente

#### 5. 📊 REPORTAR
- Marcar completitud del bloque
- Documentar cualquier desviación del plan
- Actualizar estimación de recursos si es necesario

---

## CONTROL DE RECURSOS

### Distribución Total

```
┌─────────────────────────────────────────────────┐
│ TOTAL: 100% de recursos disponibles             │
├─────────────────────────────────────────────────┤
│ Bloques 1-12: 80% (implementación)              │
│ Reserva: 20% (resolución de inconvenientes)     │
└─────────────────────────────────────────────────┘
```

### Desglose por Bloque

| Bloque | Descripción | Recursos | Acumulado |
|--------|-------------|----------|-----------|
| 1 | Configuración Inicial | 5% | 5% |
| 2 | Migraciones Catálogos | 8% | 13% |
| 3 | Modelos Catálogos | 7% | 20% |
| 4 | Livewire Marcas | 10% | 30% |
| 5 | Livewire Unidades | 10% | 40% |
| 6 | Livewire Categorías | 12% | 52% |
| 7 | Migraciones Productos | 10% | 62% |
| 8 | Modelos Productos | 10% | 72% |
| 9 | Livewire Productos Index | 8% | 80% |
| 10 | Livewire Productos Create | (Fase 2) | - |
| 11 | Migraciones Stock | (Fase 2) | - |
| 12 | Modelos Stock | (Fase 2) | - |
| - | **RESERVA** | **20%** | **100%** |

### Ajustes Dinámicos

Si un bloque excede la estimación:
1. Documentar el exceso y la causa
2. Ajustar bloques siguientes proporcionalmente
3. Verificar que la reserva del 20% se mantiene
4. Reportar al finalizar el bloque

---

## BLOQUES DE IMPLEMENTACIÓN

---

## **BLOQUE 1: CONFIGURACIÓN INICIAL Y SCHEMA**
**Recursos estimados: 5% | Acumulado: 5%**

### Objetivos
- Crear el schema `stocks` en PostgreSQL
- Configurar el sistema para reconocer el nuevo schema
- Preparar la estructura de rutas y menú
- Configurar permisos básicos

### Archivos a Crear

#### 1.1 Migración del Schema
**Archivo**: `database/migrations/2025_12_06_XXXXXX_create_stocks_schema.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS stocks');
    }

    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS stocks CASCADE');
    }
};
```

### Archivos a Modificar

#### 1.2 Configuración de Base de Datos
**Archivo**: `config/database.php`

**Línea a modificar** (~96):
```php
// ANTES
'search_path' => 'empresa,public',

// DESPUÉS
'search_path' => 'stocks,empresa,public',
```

#### 1.3 Rutas del Módulo
**Archivo**: `routes/stocks.php` (crear nuevo)

```php
<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('stocks')->name('stocks.')->group(function () {

    // Rutas de Marcas
    Route::prefix('marcas')->name('marcas.')->group(function () {
        Route::get('/', function () {
            return view('stocks.marcas.index');
        })->name('index');
        Route::get('/crear', function () {
            return view('stocks.marcas.create');
        })->name('create');
        Route::get('/{marca}/editar', function ($marca) {
            return view('stocks.marcas.edit', compact('marca'));
        })->name('edit');
    });

    // Rutas de Unidades de Medida
    Route::prefix('unidades-medida')->name('unidades-medida.')->group(function () {
        Route::get('/', function () {
            return view('stocks.unidades-medida.index');
        })->name('index');
        Route::get('/crear', function () {
            return view('stocks.unidades-medida.create');
        })->name('create');
        Route::get('/{unidad}/editar', function ($unidad) {
            return view('stocks.unidades-medida.edit', compact('unidad'));
        })->name('edit');
    });

    // Rutas de Categorías
    Route::prefix('categorias')->name('categorias.')->group(function () {
        Route::get('/', function () {
            return view('stocks.categorias.index');
        })->name('index');
        Route::get('/crear', function () {
            return view('stocks.categorias.create');
        })->name('create');
        Route::get('/{categoria}/editar', function ($categoria) {
            return view('stocks.categorias.edit', compact('categoria'));
        })->name('edit');
    });

    // TODO: Agregar más rutas en bloques posteriores
});
```

**Registrar en**: `bootstrap/app.php` o equivalente

#### 1.4 Menú AdminLTE
**Archivo**: `config/adminlte.php`

**Agregar en la sección de menú** (~300-400 líneas):
```php
[
    'text' => 'STOCKS',
    'icon' => 'fas fa-boxes',
    'can'  => 'stocks.ver',
    'submenu' => [
        [
            'text' => 'Dashboard',
            'route' => 'stocks.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'can' => 'stocks.dashboard',
        ],
        [
            'text' => 'Productos',
            'icon' => 'fas fa-box',
            'can' => 'stocks.productos.ver',
            'submenu' => [
                [
                    'text' => 'Listado',
                    'route' => 'stocks.productos.index',
                    'icon' => 'fas fa-list',
                ],
                [
                    'text' => 'Nuevo Producto',
                    'route' => 'stocks.productos.create',
                    'icon' => 'fas fa-plus',
                    'can' => 'stocks.productos.crear',
                ],
            ],
        ],
        [
            'text' => 'Catálogos',
            'icon' => 'fas fa-folder-open',
            'can' => 'stocks.catalogos.ver',
            'submenu' => [
                [
                    'text' => 'Categorías',
                    'route' => 'stocks.categorias.index',
                    'icon' => 'fas fa-sitemap',
                ],
                [
                    'text' => 'Marcas',
                    'route' => 'stocks.marcas.index',
                    'icon' => 'fas fa-copyright',
                ],
                [
                    'text' => 'Unidades de Medida',
                    'route' => 'stocks.unidades-medida.index',
                    'icon' => 'fas fa-balance-scale',
                ],
            ],
        ],
        [
            'text' => 'Gestión de Stock',
            'icon' => 'fas fa-warehouse',
            'can' => 'stocks.gestion.ver',
            'submenu' => [
                [
                    'text' => 'Vista General',
                    'route' => 'stocks.index',
                    'icon' => 'fas fa-chart-bar',
                ],
                [
                    'text' => 'Ajuste de Stock',
                    'route' => 'stocks.ajuste',
                    'icon' => 'fas fa-sliders-h',
                    'can' => 'stocks.ajustar',
                ],
                [
                    'text' => 'Transferencias',
                    'route' => 'stocks.transferencia',
                    'icon' => 'fas fa-exchange-alt',
                    'can' => 'stocks.transferir',
                ],
                [
                    'text' => 'Inventario Físico',
                    'route' => 'stocks.inventario',
                    'icon' => 'fas fa-clipboard-list',
                    'can' => 'stocks.inventario',
                ],
            ],
        ],
        [
            'text' => 'Reportes',
            'icon' => 'fas fa-chart-line',
            'can' => 'stocks.reportes.ver',
            'submenu' => [
                [
                    'text' => 'Stock Bajo',
                    'route' => 'stocks.reportes.stock-bajo',
                    'icon' => 'fas fa-exclamation-triangle text-warning',
                ],
                [
                    'text' => 'Rotación de Productos',
                    'route' => 'stocks.reportes.rotacion',
                    'icon' => 'fas fa-sync',
                ],
                [
                    'text' => 'Inventario Valorizado',
                    'route' => 'stocks.reportes.valorizado',
                    'icon' => 'fas fa-dollar-sign',
                ],
            ],
        ],
    ],
],
```

#### 1.5 Permisos
**Archivo**: `database/seeders/RolYPermisoSeeder.php`

**Agregar en el método `run()`**:
```php
// Permisos módulo Stocks
$permisosStocks = [
    // Dashboard
    ['name' => 'stocks.ver', 'description' => 'Ver módulo de stocks'],
    ['name' => 'stocks.dashboard', 'description' => 'Ver dashboard de stocks'],

    // Productos
    ['name' => 'stocks.productos.ver', 'description' => 'Ver productos'],
    ['name' => 'stocks.productos.crear', 'description' => 'Crear productos'],
    ['name' => 'stocks.productos.editar', 'description' => 'Editar productos'],
    ['name' => 'stocks.productos.eliminar', 'description' => 'Eliminar productos'],

    // Catálogos
    ['name' => 'stocks.catalogos.ver', 'description' => 'Ver catálogos'],
    ['name' => 'stocks.catalogos.gestionar', 'description' => 'Gestionar catálogos'],

    // Gestión de Stock
    ['name' => 'stocks.gestion.ver', 'description' => 'Ver gestión de stock'],
    ['name' => 'stocks.ajustar', 'description' => 'Ajustar stock'],
    ['name' => 'stocks.transferir', 'description' => 'Transferir entre depósitos'],
    ['name' => 'stocks.inventario', 'description' => 'Realizar inventario físico'],

    // Reportes
    ['name' => 'stocks.reportes.ver', 'description' => 'Ver reportes de stocks'],
];

foreach ($permisosStocks as $permiso) {
    Permission::firstOrCreate(
        ['name' => $permiso['name']],
        ['description' => $permiso['description'], 'guard_name' => 'web']
    );
}

// Asignar permisos al rol Admin
$adminRole = Role::where('name', 'Admin')->first();
if ($adminRole) {
    $adminRole->givePermissionTo(Permission::whereIn('name', array_column($permisosStocks, 'name'))->get());
}
```

### Comandos de Verificación

```bash
# 1. Ejecutar migración del schema
php artisan migrate

# 2. Limpiar configuraciones
php artisan config:clear
php artisan route:clear

# 3. Verificar rutas
php artisan route:list | grep stocks

# 4. Ejecutar seeder de permisos
php artisan db:seed --class=RolYPermisoSeeder

# 5. Verificar en PostgreSQL que el schema existe
# Conectar a psql y ejecutar:
# \dn
# Debe aparecer el schema 'stocks'
```

### Checklist del Bloque 1

- [ ] Schema `stocks` creado en PostgreSQL
- [ ] `search_path` actualizado en `config/database.php`
- [ ] Archivo `routes/stocks.php` creado y registrado
- [ ] Menú de Stocks agregado en `config/adminlte.php`
- [ ] Permisos de Stocks agregados en seeder
- [ ] Comandos de verificación ejecutados sin errores
- [ ] Configuraciones limpiadas (`config:clear`, `route:clear`)

---

## **BLOQUE 2: MIGRACIONES - CATÁLOGOS BASE (Nivel 1)**
**Recursos estimados: 8% | Acumulado: 13%**

### Objetivos
- Crear las tablas de catálogos base: MARCAS, UNIDADES_MEDIDA, CATEGORIAS
- Aplicar todas las buenas prácticas de migraciones
- Establecer foreign keys, índices y constraints apropiados

### Archivos a Crear

#### 2.1 Migración: MARCAS
**Archivo**: `database/migrations/2025_12_06_XXXXXX_create_marcas_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks.MARCAS', function (Blueprint $table) {
            $table->id();

            // Campos de negocio
            $table->string('nombre', 100);
            $table->string('pais_origen', 100)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('logo_path')->nullable();

            // Campo de estado
            $table->boolean('activo')->default(true);

            // Campos de auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('nombre');
            $table->index('activo');

            // Foreign keys
            $table->foreign('creadoPor')->references('id')->on('users')->onDelete('set null');
            $table->foreign('actualizadoPor')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks.MARCAS');
    }
};
```

#### 2.2 Migración: UNIDADES_MEDIDA
**Archivo**: `database/migrations/2025_12_06_XXXXXX_create_unidades_medida_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks.UNIDADES_MEDIDA', function (Blueprint $table) {
            $table->id();

            // Campos de negocio
            $table->string('nombre', 100);
            $table->string('simbolo', 10);
            $table->boolean('permite_decimales')->default(true);
            $table->text('descripcion')->nullable();

            // Campo de estado
            $table->boolean('activo')->default(true);

            // Campos de auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices y constraints
            $table->unique('nombre');
            $table->unique('simbolo');
            $table->index('activo');

            // Foreign keys
            $table->foreign('creadoPor')->references('id')->on('users')->onDelete('set null');
            $table->foreign('actualizadoPor')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks.UNIDADES_MEDIDA');
    }
};
```

#### 2.3 Migración: CATEGORIAS
**Archivo**: `database/migrations/2025_12_06_XXXXXX_create_categorias_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks.CATEGORIAS', function (Blueprint $table) {
            $table->id();

            // Jerarquía
            $table->unsignedBigInteger('parent_id')->nullable();

            // Campos de negocio
            $table->string('codigo', 20);
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->integer('nivel')->default(1);
            $table->integer('orden')->default(0);

            // Campo de estado
            $table->boolean('activo')->default(true);

            // Campos de auditoría
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->unique('codigo');
            $table->index('parent_id');
            $table->index('nivel');
            $table->index('activo');

            // Foreign keys
            $table->foreign('parent_id')
                ->references('id')
                ->on('stocks.CATEGORIAS')
                ->onDelete('restrict'); // No permitir eliminar si tiene hijos

            $table->foreign('creadoPor')->references('id')->on('users')->onDelete('set null');
            $table->foreign('actualizadoPor')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks.CATEGORIAS');
    }
};
```

### Consideraciones Especiales

#### Tamaños de Campos
```php
// ✅ BIEN - Espacio suficiente
$table->string('nombre', 100);      // Nombres largos
$table->string('codigo', 20);       // Códigos jerárquicos (01.01.01.01)
$table->string('simbolo', 10);      // Símbolos cortos (Un, m, kg)

// ❌ MAL - Evitar
$table->char('codigo', 3);          // Muy pequeño (lección de Puntos Expedición)
```

#### Foreign Keys con Cascadas
```php
// Relaciones con usuarios - SET NULL si se elimina el usuario
->onDelete('set null')

// Relaciones jerárquicas - RESTRICT para evitar huérfanos
->onDelete('restrict')

// Relaciones de datos - CASCADE solo si corresponde
->onDelete('cascade')
```

### Comandos de Verificación

```bash
# 1. Ejecutar migraciones
php artisan migrate

# 2. Verificar en PostgreSQL
# \dt stocks.*
# Deben aparecer: MARCAS, UNIDADES_MEDIDA, CATEGORIAS

# 3. Verificar estructura de tabla específica
# \d stocks."MARCAS"
# \d stocks."UNIDADES_MEDIDA"
# \d stocks."CATEGORIAS"
```

### Checklist del Bloque 2

- [ ] Migración MARCAS creada y ejecutada
- [ ] Migración UNIDADES_MEDIDA creada y ejecutada
- [ ] Migración CATEGORIAS creada y ejecutada
- [ ] Todas las foreign keys funcionando correctamente
- [ ] Índices creados en campos de búsqueda frecuente
- [ ] Unique constraints aplicados donde corresponde
- [ ] Soft deletes habilitado en todas las tablas
- [ ] Campos de auditoría presentes (`creadoPor`, `actualizadoPor`)
- [ ] Verificación en PostgreSQL completada sin errores

---

## **BLOQUE 3: MODELOS - CATÁLOGOS BASE**
**Recursos estimados: 7% | Acumulado: 20%**

### Objetivos
- Crear modelos Eloquent para Marca, UnidadMedida y Categoria
- Implementar todas las relaciones
- Definir scopes para consultas comunes
- Aplicar traits de SoftDeletes y Auditable

### Archivos a Crear

#### 3.1 Modelo: Marca
**Archivo**: `app/Models/Stocks/Marca.php`

```php
<?php

namespace App\Models\Stocks;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Marca extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'stocks.MARCAS';

    protected $fillable = [
        'nombre',
        'pais_origen',
        'descripcion',
        'logo_path',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    // ==================== RELACIONES ====================

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'marca_id');
    }

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('nombre', "%{$search}%")
                ->orWhereLike('pais_origen', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarNombre(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('nombre', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarActivo(Builder $query, $search): void
    {
        $query->when($search !== null && $search !== '', function (Builder $query) use ($search) {
            $query->where('activo', $search);
        });
    }

    #[Scope]
    protected function soloActivos(Builder $query): void
    {
        $query->where('activo', true);
    }
}
```

#### 3.2 Modelo: UnidadMedida
**Archivo**: `app/Models/Stocks/UnidadMedida.php`

```php
<?php

namespace App\Models\Stocks;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class UnidadMedida extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'stocks.UNIDADES_MEDIDA';

    protected $fillable = [
        'nombre',
        'simbolo',
        'permite_decimales',
        'descripcion',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'permite_decimales' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    // ==================== RELACIONES ====================

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'unidad_medida_id');
    }

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    // ==================== ACCESSORS ====================

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' (' . $this->simbolo . ')';
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('nombre', "%{$search}%")
                ->orWhereLike('simbolo', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarNombre(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('nombre', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarActivo(Builder $query, $search): void
    {
        $query->when($search !== null && $search !== '', function (Builder $query) use ($search) {
            $query->where('activo', $search);
        });
    }

    #[Scope]
    protected function soloActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    #[Scope]
    protected function permiteDecimales(Builder $query): void
    {
        $query->where('permite_decimales', true);
    }
}
```

#### 3.3 Modelo: Categoria
**Archivo**: `app/Models/Stocks/Categoria.php`

```php
<?php

namespace App\Models\Stocks;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Categoria extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'stocks.CATEGORIAS';

    protected $fillable = [
        'parent_id',
        'codigo',
        'nombre',
        'descripcion',
        'nivel',
        'orden',
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'nivel' => 'integer',
            'orden' => 'integer',
            'activo' => 'boolean',
        ];
    }

    // ==================== RELACIONES ====================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'parent_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(Categoria::class, 'parent_id');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    public function creadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creadoPor');
    }

    public function actualizadoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizadoPor');
    }

    // ==================== ACCESSORS ====================

    public function getNombreCompletoAttribute(): string
    {
        return $this->codigo . ' - ' . $this->nombre;
    }

    public function getRutaCompletaAttribute(): string
    {
        $ruta = collect([$this->nombre]);
        $padre = $this->parent;

        while ($padre) {
            $ruta->prepend($padre->nombre);
            $padre = $padre->parent;
        }

        return $ruta->implode(' > ');
    }

    // ==================== SCOPES ====================

    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('nombre', "%{$search}%")
                ->orWhereLike('codigo', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarNombre(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('nombre', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarActivo(Builder $query, $search): void
    {
        $query->when($search !== null && $search !== '', function (Builder $query) use ($search) {
            $query->where('activo', $search);
        });
    }

    #[Scope]
    protected function soloActivos(Builder $query): void
    {
        $query->where('activo', true);
    }

    #[Scope]
    protected function raices(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    #[Scope]
    protected function porNivel(Builder $query, int $nivel): void
    {
        $query->where('nivel', $nivel);
    }

    #[Scope]
    protected function ordenadas(Builder $query): void
    {
        $query->orderBy('orden')->orderBy('nombre');
    }

    // ==================== MÉTODOS ====================

    /**
     * Verifica si esta categoría es descendiente de otra
     */
    public function esDescendienteDe(Categoria $categoria): bool
    {
        $padre = $this->parent;

        while ($padre) {
            if ($padre->id === $categoria->id) {
                return true;
            }
            $padre = $padre->parent;
        }

        return false;
    }

    /**
     * Obtiene todos los descendientes (recursivo)
     */
    public function todosLosDescendientes(): \Illuminate\Support\Collection
    {
        $descendientes = collect();

        foreach ($this->hijos as $hijo) {
            $descendientes->push($hijo);
            $descendientes = $descendientes->merge($hijo->todosLosDescendientes());
        }

        return $descendientes;
    }
}
```

### Consideraciones Especiales

#### Validaciones con Rule Facade
Los modelos están preparados para usar validaciones con `Rule::` facade:

```php
// En Livewire components (próximos bloques)
use Illuminate\Validation\Rule;

protected function rules()
{
    return [
        'nombre' => ['required', 'string', 'max:100', Rule::unique(Marca::class, 'nombre')],
    ];
}
```

#### Accessors Calculados
```php
// ✅ Accessor - Se calcula en tiempo real
public function getNombreCompletoAttribute(): string
{
    return $this->codigo . ' - ' . $this->nombre;
}

// Uso en Blade
{{ $categoria->nombre_completo }}
```

### Comandos de Verificación

```bash
# 1. Verificar que los modelos se cargan sin errores
php artisan tinker
>>> App\Models\Stocks\Marca::count();
>>> App\Models\Stocks\UnidadMedida::count();
>>> App\Models\Stocks\Categoria::count();

# 2. Probar scopes
>>> App\Models\Stocks\Marca::soloActivos()->get();

# 3. Salir de tinker
>>> exit
```

### Checklist del Bloque 3

- [ ] Modelo Marca creado con todas las relaciones
- [ ] Modelo UnidadMedida creado con todas las relaciones
- [ ] Modelo Categoria creado con relación recursiva
- [ ] Traits SoftDeletes y Auditable implementados
- [ ] Scopes básicos implementados en todos los modelos
- [ ] Accessors definidos donde corresponde
- [ ] Casts definidos para booleans e integers
- [ ] Verificación en Tinker completada sin errores
- [ ] Relaciones funcionan correctamente

---

## **BLOQUE 4: LIVEWIRE + VISTAS - MARCAS (CRUD Simple)**
**Recursos estimados: 10% | Acumulado: 30%**

### Objetivos
- Implementar CRUD completo de Marcas
- Crear componentes Livewire: Index, Create, Edit
- Crear vistas contenedoras y vistas de componentes
- Aplicar todas las buenas prácticas aprendidas

### Archivos a Crear

#### 4.1 Livewire: Index
**Archivo**: `app/Livewire/Stocks/Marcas/Index.php`

```php
<?php

namespace App\Livewire\Stocks\Marcas;

use App\Models\Stocks\Marca;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarActivo = '';

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['buscador', 'buscarActivo'])) {
            $this->resetPage();
        }
    }

    public function cambiarEstado($marcaId)
    {
        $marca = Marca::findOrFail($marcaId);
        $marca->activo = !$marca->activo;
        $marca->actualizadoPor = auth()->id();
        $marca->save();

        session()->flash('success', 'Estado actualizado correctamente!');
    }

    public function eliminar($marcaId)
    {
        $marca = Marca::findOrFail($marcaId);

        if ($marca->productos()->count() > 0) {
            session()->flash('error', 'No se puede eliminar la marca porque tiene productos asociados.');
            return;
        }

        $marca->delete();
        session()->flash('success', 'Marca eliminada correctamente!');
    }

    public function render()
    {
        return view('livewire.stocks.marcas.index', [
            'marcas' => Marca::query()
                ->buscador($this->buscador)
                ->buscarActivo($this->buscarActivo)
                ->withCount('productos')
                ->orderBy('nombre')
                ->paginate(10),
        ]);
    }
}
```

#### 4.2 Livewire: Create
**Archivo**: `app/Livewire/Stocks/Marcas/Create.php`

```php
<?php

namespace App\Livewire\Stocks\Marcas;

use App\Models\Stocks\Marca;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Create extends Component
{
    #[Validate]
    public $nombre = '';
    public $pais_origen = '';
    public $descripcion = '';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => ['required', 'string', 'max:100', Rule::unique(Marca::class, 'nombre')],
            'pais_origen' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre de la marca es obligatorio.',
        'nombre.unique' => 'Ya existe una marca con este nombre.',
        'nombre.max' => 'El nombre no debe superar los 100 caracteres.',
    ];

    public function guardar()
    {
        $this->validate();

        Marca::create([
            'nombre' => strtoupper($this->nombre),
            'pais_origen' => $this->pais_origen,
            'descripcion' => $this->descripcion,
            'activo' => $this->activo,
            'creadoPor' => auth()->id(),
        ]);

        session()->flash('success', 'Marca creada correctamente!');
        $this->redirectRoute('stocks.marcas.index');
    }

    public function render()
    {
        return view('livewire.stocks.marcas.create');
    }
}
```

#### 4.3 Livewire: Edit
**Archivo**: `app/Livewire/Stocks/Marcas/Edit.php`

```php
<?php

namespace App\Livewire\Stocks\Marcas;

use App\Models\Stocks\Marca;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public $marcaId;

    #[Validate]
    public $nombre = '';
    public $pais_origen = '';
    public $descripcion = '';
    public $activo = true;

    public function mount(Marca $marca)
    {
        $this->marcaId = $marca->id;
        $this->nombre = $marca->nombre;
        $this->pais_origen = $marca->pais_origen;
        $this->descripcion = $marca->descripcion;
        $this->activo = $marca->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Marca::class, 'nombre')->ignore($this->marcaId)
            ],
            'pais_origen' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre de la marca es obligatorio.',
        'nombre.unique' => 'Ya existe una marca con este nombre.',
        'nombre.max' => 'El nombre no debe superar los 100 caracteres.',
    ];

    public function guardar()
    {
        $this->validate();

        $marca = Marca::findOrFail($this->marcaId);
        $marca->update([
            'nombre' => strtoupper($this->nombre),
            'pais_origen' => $this->pais_origen,
            'descripcion' => $this->descripcion,
            'activo' => $this->activo,
            'actualizadoPor' => auth()->id(),
        ]);

        session()->flash('success', 'Marca actualizada correctamente!');
        $this->redirectRoute('stocks.marcas.index');
    }

    public function render()
    {
        return view('livewire.stocks.marcas.edit');
    }
}
```

#### 4.4 Vista Contenedora: Index
**Archivo**: `resources/views/stocks/marcas/index.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Marcas')

@section('content')
    @livewire('stocks.marcas.index')
@endsection
```

#### 4.5 Vista Contenedora: Create
**Archivo**: `resources/views/stocks/marcas/create.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Nueva Marca')

@section('content')
    @livewire('stocks.marcas.create')
@endsection
```

#### 4.6 Vista Contenedora: Edit
**Archivo**: `resources/views/stocks/marcas/edit.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Editar Marca')

@section('content')
    @livewire('stocks.marcas.edit', ['marca' => $marca])
@endsection
```

#### 4.7 Vista Componente: Index
**Archivo**: `resources/views/livewire/stocks/marcas/index.blade.php`

```blade
<div>
    {{-- Mensajes de éxito/error --}}
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="¡Éxito!" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="light" title="Marcas" icon="fas fa-copyright">
        {{-- Filtros --}}
        <div class="row mb-3">
            <div class="col-md-5">
                <x-adminlte-input name="buscador" wire:model.live="buscador"
                    placeholder="Buscar por nombre o país..." igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="col-md-3">
                <select wire:model.live="buscarActivo" class="form-control form-control-sm">
                    <option value="">Todos los estados</option>
                    <option value="1">Solo activos</option>
                    <option value="0">Solo inactivos</option>
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('stocks.marcas.create') }}" class="btn btn-success btn-block btn-sm">
                    <i class="fas fa-plus"></i> Nueva Marca
                </a>
            </div>
        </div>

        {{-- Tabla --}}
        @if($marcas->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>País de Origen</th>
                            <th>Productos</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marcas as $marca)
                            <tr>
                                <td>{{ $marca->id }}</td>
                                <td>
                                    <strong>{{ $marca->nombre }}</strong>
                                    @if($marca->descripcion)
                                        <br><small class="text-muted">{{ Str::limit($marca->descripcion, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $marca->pais_origen ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $marca->productos_count }}</span>
                                </td>
                                <td>
                                    @if($marca->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('stocks.marcas.edit', $marca) }}"
                                           class="btn btn-info btn-sm"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button wire:click="cambiarEstado({{ $marca->id }})"
                                                class="btn btn-{{ $marca->activo ? 'warning' : 'success' }} btn-sm"
                                                title="{{ $marca->activo ? 'Inactivar' : 'Activar' }}">
                                            <i class="fas fa-{{ $marca->activo ? 'ban' : 'check' }}"></i>
                                        </button>
                                        @if($marca->productos_count == 0)
                                            <button wire:click="eliminar({{ $marca->id }})"
                                                    wire:confirm="¿Está seguro de eliminar esta marca?"
                                                    class="btn btn-danger btn-sm"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="d-flex justify-content-between">
                <div>
                    Mostrando {{ $marcas->firstItem() }} a {{ $marcas->lastItem() }}
                    de {{ $marcas->total() }} registros
                </div>
                <div>
                    {{ $marcas->links() }}
                </div>
            </div>
        @else
            {{-- Estado vacío --}}
            <div class="text-center py-4">
                <i class="fas fa-copyright fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay marcas registradas</h5>
                <p class="text-muted">Comienza creando una nueva marca</p>
                <a href="{{ route('stocks.marcas.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nueva Marca
                </a>
            </div>
        @endif
    </x-adminlte-card>
</div>
```

#### 4.8 Vista Componente: Create
**Archivo**: `resources/views/livewire/stocks/marcas/create.blade.php`

```blade
<div>
    <x-adminlte-card theme="light" title="Nueva Marca" icon="fas fa-plus">
        <form class="row" wire:submit="guardar">

            {{-- Sección 1: Información General --}}
            <div class="col-md-12">
                <h5 class="text-muted border-bottom pb-2 mb-3">Información General</h5>
            </div>

            <x-adminlte-input name="nombre" wire:model="nombre"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="Ingrese el nombre de la marca"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">Nombre *</div>
                </x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="pais_origen" wire:model="pais_origen"
                placeholder="Ej: Paraguay, Brasil, China"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">País de Origen</div>
                </x-slot>
            </x-adminlte-input>

            <div class="form-group col-md-12">
                <label for="descripcion">Descripción</label>
                <textarea wire:model="descripcion" id="descripcion"
                          class="form-control form-control-sm"
                          rows="3"
                          placeholder="Descripción opcional de la marca"></textarea>
            </div>

            <div class="form-group col-md-6">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="activo" wire:model="activo">
                    <label class="custom-control-label" for="activo">
                        Marca activa
                    </label>
                </div>
            </div>

            <div class="col-md-12"><hr></div>

            {{-- Botones --}}
            <div class="form-group col-md-3">
                <a href="{{ route('stocks.marcas.index') }}"
                   class="btn btn-block btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="form-group col-md-3">
                <x-adminlte-button type="submit"
                    label="Guardar Marca"
                    theme="outline-success"
                    icon="fas fa-save"
                    class="w-100 btn-sm" />
            </div>
        </form>
    </x-adminlte-card>
</div>
```

#### 4.9 Vista Componente: Edit
**Archivo**: `resources/views/livewire/stocks/marcas/edit.blade.php`

```blade
<div>
    <x-adminlte-card theme="light" title="Editar Marca" icon="fas fa-edit">
        <form class="row" wire:submit="guardar">

            {{-- Sección 1: Información General --}}
            <div class="col-md-12">
                <h5 class="text-muted border-bottom pb-2 mb-3">Información General</h5>
            </div>

            <x-adminlte-input name="nombre" wire:model="nombre"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="Ingrese el nombre de la marca"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">Nombre *</div>
                </x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="pais_origen" wire:model="pais_origen"
                placeholder="Ej: Paraguay, Brasil, China"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">País de Origen</div>
                </x-slot>
            </x-adminlte-input>

            <div class="form-group col-md-12">
                <label for="descripcion">Descripción</label>
                <textarea wire:model="descripcion" id="descripcion"
                          class="form-control form-control-sm"
                          rows="3"
                          placeholder="Descripción opcional de la marca"></textarea>
            </div>

            <div class="form-group col-md-6">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="activo" wire:model="activo">
                    <label class="custom-control-label" for="activo">
                        Marca activa
                    </label>
                </div>
            </div>

            <div class="col-md-12"><hr></div>

            {{-- Botones --}}
            <div class="form-group col-md-3">
                <a href="{{ route('stocks.marcas.index') }}"
                   class="btn btn-block btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="form-group col-md-3">
                <x-adminlte-button type="submit"
                    label="Actualizar Marca"
                    theme="outline-success"
                    icon="fas fa-save"
                    class="w-100 btn-sm" />
            </div>
        </form>
    </x-adminlte-card>
</div>
```

### Consideraciones Especiales

#### Uso de Rule Facade
```php
// ✅ CORRECTO - Para PostgreSQL con schemas
Rule::unique(Marca::class, 'nombre')

// ❌ INCORRECTO - No funciona con schemas
'unique:stocks.MARCAS,nombre'
```

#### Validación Condicional
```php
// No incluir campos readonly en #[Validate]
public $marcaId;  // Sin #[Validate]

#[Validate]
public $nombre = '';  // Con #[Validate]
```

### Comandos de Verificación

```bash
# 1. Limpiar vistas
php artisan view:clear

# 2. Acceder a la ruta en el navegador
# http://localhost/stocks/marcas

# 3. Probar creación de marca
# http://localhost/stocks/marcas/crear

# 4. Verificar que se guarda en BD
php artisan tinker
>>> App\Models\Stocks\Marca::latest()->first();
```

### Checklist del Bloque 4

- [ ] Componente Livewire Index creado
- [ ] Componente Livewire Create creado
- [ ] Componente Livewire Edit creado
- [ ] Vista contenedora Index creada
- [ ] Vista contenedora Create creada
- [ ] Vista contenedora Edit creada
- [ ] Vista componente Index creada
- [ ] Vista componente Create creada
- [ ] Vista componente Edit creada
- [ ] Validaciones con `Rule::` facade funcionando
- [ ] Búsqueda y filtros funcionando
- [ ] Paginación funcionando
- [ ] Crear marca funciona correctamente
- [ ] Editar marca funciona correctamente
- [ ] Cambiar estado funciona correctamente
- [ ] Eliminar marca funciona (solo si no tiene productos)
- [ ] Mensajes flash se muestran correctamente

---

## **BLOQUE 5: LIVEWIRE + VISTAS - UNIDADES MEDIDA (CRUD Simple)**
**Recursos estimados: 10% | Acumulado: 40%**

### Objetivos
- Implementar CRUD completo de Unidades de Medida
- Reutilizar estructura de Marcas con adaptaciones específicas
- Campo específico: `permite_decimales` (checkbox)

### Estructura
Similar al BLOQUE 4, pero con las siguientes particularidades:

#### Campos específicos:
- `nombre` (required, unique)
- `simbolo` (required, unique, max:10)
- `permite_decimales` (boolean, default: true)
- `descripcion` (nullable)
- `activo` (boolean, default: true)

#### Validaciones especiales:
```php
protected function rules()
{
    return [
        'nombre' => ['required', 'string', 'max:100', Rule::unique(UnidadMedida::class, 'nombre')],
        'simbolo' => ['required', 'string', 'max:10', Rule::unique(UnidadMedida::class, 'simbolo')],
        'permite_decimales' => ['boolean'],
        'descripcion' => ['nullable', 'string'],
    ];
}
```

### Checklist del Bloque 5

- [ ] Componente Livewire Index creado
- [ ] Componente Livewire Create creado
- [ ] Componente Livewire Edit creado
- [ ] Todas las vistas creadas (contenedoras y componentes)
- [ ] Campo `permite_decimales` como checkbox funcional
- [ ] Validación de `simbolo` único funcionando
- [ ] CRUD completo funcional

---

## **BLOQUE 6: LIVEWIRE + VISTAS - CATEGORÍAS (CRUD Jerárquico)**
**Recursos estimados: 12% | Acumulado: 52%**

### Objetivos
- Implementar CRUD completo de Categorías con jerarquía
- Select de categoría padre con indentación visual
- Generación automática de código jerárquico
- Prevenir que una categoría sea su propio padre

### Particularidades Jerárquicas

#### Selección de Padre con Indentación
```blade
<select wire:model="parent_id" class="form-control form-control-sm">
    <option value="">Sin categoría padre (Raíz)</option>
    @foreach($categoriasPosibles as $cat)
        <option value="{{ $cat->id }}">
            {{ str_repeat('—', $cat->nivel - 1) }} {{ $cat->nombre }}
        </option>
    @endforeach
</select>
```

#### Validación para Prevenir Ciclos
```php
public function guardar()
{
    $this->validate();

    // Validar que no sea su propio padre
    if ($this->parent_id == $this->categoriaId) {
        session()->flash('error', 'Una categoría no puede ser su propio padre.');
        return;
    }

    // Validar que no sea descendiente del padre seleccionado
    if ($this->parent_id) {
        $parent = Categoria::find($this->parent_id);
        $categoriaActual = Categoria::find($this->categoriaId);

        if ($categoriaActual && $parent->esDescendienteDe($categoriaActual)) {
            session()->flash('error', 'No puede seleccionar un descendiente como padre.');
            return;
        }
    }

    // Continuar con el guardado...
}
```

### Checklist del Bloque 6

- [ ] Componente Livewire Index con árbol jerárquico
- [ ] Componente Livewire Create con selección de padre
- [ ] Componente Livewire Edit con selección de padre
- [ ] Validación de ciclos implementada
- [ ] Código jerárquico se genera automáticamente
- [ ] Visualización de árbol funcional
- [ ] CRUD completo funcional

---

## **BLOQUES 7-12**

Los bloques 7 al 12 continúan con la misma estructura y metodología:

- **BLOQUE 7**: Migraciones de Productos, Atributos, Imágenes
- **BLOQUE 8**: Modelos con todas las relaciones
- **BLOQUE 9**: Livewire Index de Productos con filtros avanzados
- **BLOQUE 10**: Livewire Create de Productos (multipaso)
- **BLOQUE 11**: Migraciones de Stock, Movimientos, Precios
- **BLOQUE 12**: Modelos de gestión de stock

Estos bloques se implementarán en una segunda fase, manteniendo la misma metodología y control de recursos.

---

## RESERVA PARA RESOLUCIÓN DE INCONVENIENTES
**Recursos reservados: 20%**

### Uso de la Reserva

Esta reserva se utilizará para:

#### 1. Errores de Validación PostgreSQL
- Problemas con `search_path`
- Errores de foreign keys
- Constraints que fallan

#### 2. Problemas de Livewire
- Validaciones que no funcionan correctamente
- Issues con `wire:model.live`
- Problemas de renderizado

#### 3. Ajustes de Relaciones
- Relaciones Eloquent incorrectas
- Scopes que no funcionan
- Eager loading necesario

#### 4. Refinamiento UX
- Mejoras en las vistas
- Ajustes de diseño AdminLTE
- Mensajes de usuario más claros

#### 5. Correcciones de Migraciones
- Cambios de estructura de tablas
- Ajustes de tamaños de campos
- Modificaciones de constraints

### Registro de Uso de Reserva

Al usar recursos de la reserva, documentar:
- **Bloque afectado**
- **Problema encontrado**
- **Solución aplicada**
- **Recursos consumidos** (estimado en %)

---

## CHECKLIST GENERAL DE VERIFICACIÓN

### Base de Datos
- [ ] Schema `stocks` existe y está en `search_path`
- [ ] Todas las migraciones ejecutadas sin errores
- [ ] Foreign keys funcionando correctamente
- [ ] Índices creados en campos de búsqueda
- [ ] Unique constraints aplicados correctamente

### Modelos
- [ ] Todos los modelos cargan sin errores
- [ ] Relaciones bidireccionales implementadas
- [ ] Scopes funcionan correctamente
- [ ] SoftDeletes y Auditable implementados
- [ ] Casts definidos correctamente

### Livewire
- [ ] Todos los componentes se renderizan
- [ ] Validaciones funcionan con `Rule::`
- [ ] `->ignore()` en unique para edición
- [ ] Reseteo de paginación en búsquedas
- [ ] Mensajes flash se muestran correctamente

### Vistas
- [ ] Diseño consistente con AdminLTE
- [ ] Tamaños `sm` en todos los componentes
- [ ] Estados vacíos informativos
- [ ] Paginación con conteos
- [ ] Confirmaciones en acciones destructivas

### Funcionalidad
- [ ] Crear registros funciona
- [ ] Editar registros funciona
- [ ] Cambiar estado funciona
- [ ] Eliminar funciona (con validaciones)
- [ ] Búsquedas funcionan
- [ ] Filtros funcionan

---

**Fecha de creación**: Diciembre 2025
**Versión del sistema**: SIGEA v4
**Framework**: Laravel 12.26.4 / PHP 8.4.15 / PostgreSQL
**Basado en**: Buenas prácticas del módulo Empresa
**Control de recursos**: 80% implementación + 20% reserva
