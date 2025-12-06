# BUENAS PRÁCTICAS PARA DESARROLLO DE MÓDULOS
## Basado en la experiencia del Módulo Empresa - SIGEA v4

---

## ÍNDICE
1. [Validaciones y PostgreSQL](#validaciones-y-postgresql)
2. [Estructura de Datos](#estructura-de-datos)
3. [Migraciones](#migraciones)
4. [Modelos Eloquent](#modelos-eloquent)
5. [Livewire Components](#livewire-components)
6. [Vistas Blade](#vistas-blade)
7. [Campos Calculados](#campos-calculados)
8. [Testing y Verificación](#testing-y-verificación)
9. [Orden de Desarrollo](#orden-de-desarrollo)
10. [Errores Comunes y Soluciones](#errores-comunes-y-soluciones)

---

## 1. VALIDACIONES Y POSTGRESQL

### ❌ ERROR COMÚN: Usar strings en validaciones con schemas
```php
// INCORRECTO - Laravel interpreta empresa.TABLA como conexión.tabla
'codigo' => 'unique:empresa.DEPOSITOS,codigo'
'sucursal_id' => 'exists:empresa.SUCURSALES,id'
```

**Problema**: Laravel/PostgreSQL interpreta `empresa.TABLA` como una conexión de base de datos llamada "empresa", no como un schema.

**Error resultante**:
```
Database connection [empresa] not configured.
```

### ✅ SOLUCIÓN: Usar Rule facade con clases de modelo

```php
use Illuminate\Validation\Rule;
use App\Models\Empresa\Deposito;
use App\Models\Empresa\Sucursal;

protected function rules()
{
    return [
        'sucursal_id' => ['required', Rule::exists(Sucursal::class, 'id')],
        'codigo' => [
            'required',
            'string',
            'max:10',
            Rule::unique(Deposito::class, 'codigo')
                ->when($this->sucursal_id, function ($rule) {
                    return $rule->where('sucursal_id', $this->sucursal_id);
                })
        ],
    ];
}
```

### 🎯 BENEFICIOS:
- ✅ Funciona correctamente con PostgreSQL schemas
- ✅ Más legible y mantenible
- ✅ Permite validaciones condicionales con `->when()`
- ✅ Evita problemas con foreign keys vacías

### ⚙️ CONFIGURACIÓN REQUERIDA:
```php
// config/database.php
'connections' => [
    'pgsql' => [
        'search_path' => 'empresa,public',  // ← IMPORTANTE
    ],
],
```

---

## 2. ESTRUCTURA DE DATOS

### 📋 CAMPOS OBLIGATORIOS EN TODA ENTIDAD

```php
// Campos de auditoría
'creadoPor' => 'nullable',
'actualizadoPor' => 'nullable',
'created_at' => 'timestamp',
'updated_at' => 'timestamp',
'deleted_at' => 'timestamp nullable',  // Si usa SoftDeletes

// Campo de estado
'activo' => 'boolean default true',
```

### 🔑 CLAVES ÚNICAS COMPUESTAS

Cuando un código debe ser único por padre (ej: código único por sucursal):

```php
// En migración
$table->unique(['sucursal_id', 'codigo'], 'sucursal_codigo_unique');

// En validación
Rule::unique(Deposito::class, 'codigo')
    ->when($this->sucursal_id, function ($rule) {
        return $rule->where('sucursal_id', $this->sucursal_id);
    })
    ->ignore($this->deposito->id ?? null)  // Para edición
```

### 📏 TAMAÑOS DE CAMPOS

Establecer tamaños apropiados desde el inicio:

```php
// ❌ INCORRECTO - Muy pequeño
$table->char('codigo', 3);  // Luego necesitó cambiar a 10

// ✅ CORRECTO - Espacio suficiente
$table->string('codigo', 10);
$table->string('nombre', 100);
$table->string('descripcion', 255)->nullable();
```

**Lección del error**: El campo `codigo` de Puntos de Expedición se creó como `char(3)` pero se necesitaban códigos de 5 caracteres. Se tuvo que ejecutar `ALTER TABLE` en producción.

---

## 3. MIGRACIONES

### 📝 ORDEN CORRECTO DE CREACIÓN

```php
1. Schema (si se usa)
2. Tablas padre (sin foreign keys)
3. Tablas hijas (con foreign keys a las padres)
4. Tablas pivot (con foreign keys a ambas tablas)
```

**Ejemplo del módulo Empresa**:
```
1. create_empresa_schema.php
2. create_empresa_table.php
3. create_actividades_economicas_table.php
4. create_empresa_actividad_economica_table.php  (pivot)
5. create_sucursales_table.php
6. create_depositos_table.php
7. create_puntos_expedicion_table.php
8. create_timbrados_table.php
```

### 🔗 FOREIGN KEYS CORRECTAS

```php
// ✅ COMPLETO: Con cascadas y índices
$table->unsignedBigInteger('sucursal_id');
$table->foreign('sucursal_id')
    ->references('id')
    ->on('empresa.SUCURSALES')
    ->onDelete('cascade');  // o 'set null' si es nullable
```

### 🚫 CONSTRAINTS EN ENUM

```php
// PostgreSQL ENUM no acepta strings vacíos
$table->enum('cdc_ambiente', ['produccion', 'test'])->nullable();

// Validación correspondiente
'cdc_ambiente' => [
    Rule::requiredIf($this->es_electronico),  // ← IMPORTANTE
    'nullable',
    Rule::in(['produccion', 'test'])
],
```

**Lección del error**: Intentar insertar un string vacío en un ENUM causa error de constraint violation.

---

## 4. MODELOS ELOQUENT

### 🏗️ ESTRUCTURA ESTÁNDAR

```php
<?php

namespace App\Models\Empresa;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class MiModelo extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'empresa.MI_TABLA';

    protected $fillable = [
        // Campos de negocio
        'campo1',
        'campo2',
        // Campos de auditoría
        'activo',
        'creadoPor',
        'actualizadoPor',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'fecha_campo' => 'date',
            'es_principal' => 'boolean',
        ];
    }

    // ==================== CONSTANTES ====================

    public const TIPOS = [
        'tipo1' => 'Tipo 1',
        'tipo2' => 'Tipo 2',
    ];

    // ==================== RELACIONES ====================

    public function padre(): BelongsTo
    {
        return $this->belongsTo(Padre::class, 'padre_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(Hijo::class, 'padre_id');
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
    protected function soloActivos(Builder $query): void
    {
        $query->where('activo', true);
    }
}
```

### 📊 CAMPOS CALCULADOS VS ALMACENADOS

**REGLA**: Si el valor se calcula basándose SOLO en otros campos del mismo registro → Accessor. Si cambia con el tiempo o uso → Almacenar.

```php
// ✅ ACCESSOR - Se calcula siempre igual
public function getRucCompletoAttribute(): string
{
    return $this->ruc . '-' . $this->dv;
}

// ✅ ALMACENADO - Cambia con el uso
protected $fillable = ['numero_actual'];  // Se incrementa al usarse
```

**Lección del error**: `numero_actual` de Timbrados inicialmente se intentó calcular, pero debe almacenarse porque cambia al consumir numeración.

### 🔢 INICIALIZACIÓN DE CAMPOS CALCULADOS

```php
// ❌ INCORRECTO - Dejar que el usuario lo ingrese
public $numero_actual = 0;  // En Livewire component

// ✅ CORRECTO - Calcularlo al crear
Timbrado::create([
    'numero_desde' => $this->numero_desde,
    'numero_hasta' => $this->numero_hasta,
    'numero_actual' => $this->numero_desde - 1,  // ← Calculado
]);
```

**Lección del error**: Permitir que el usuario ingrese `numero_actual` causaba que el porcentaje de uso fuera incorrecto desde el inicio.

---

## 5. LIVEWIRE COMPONENTS

### 🎨 ESTRUCTURA ESTÁNDAR

```php
<?php

namespace App\Livewire\MiModulo;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination;

    // Propiedades de búsqueda
    public $buscador = '';
    public $buscarActivo = '';

    // Resetear paginación al buscar
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['buscador', 'buscarActivo'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.mi-modulo.index', [
            'registros' => MiModelo::query()
                ->buscador($this->buscador)
                ->buscarActivo($this->buscarActivo)
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ]);
    }
}
```

### 📝 CREATE COMPONENT

```php
class Create extends Component
{
    #[Validate]
    public $campo1 = '';
    public $campo2 = '';
    public $activo = true;

    protected function rules()
    {
        return [
            'campo1' => ['required', 'string', 'max:100'],
            'campo2' => [
                'required',
                Rule::unique(MiModelo::class, 'campo2')
                    ->when($this->padre_id, function ($rule) {
                        return $rule->where('padre_id', $this->padre_id);
                    })
            ],
        ];
    }

    protected $messages = [
        'campo1.required' => 'El campo 1 es obligatorio.',
    ];

    public function guardar()
    {
        $this->validate();

        MiModelo::create([
            'campo1' => $this->campo1,
            'campo2' => $this->campo2,
            'activo' => $this->activo,
            'creadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Registro creado correctamente!');
        $this->redirectRoute('ruta.index');
    }
}
```

### ✏️ EDIT COMPONENT

**IMPORTANTE**: No validar campos de solo lectura

```php
class Edit extends Component
{
    public $registroId;
    public $campo_readonly;  // No incluir en #[Validate]

    #[Validate]
    public $campo_editable = '';

    public function mount(MiModelo $registro)
    {
        $this->registroId = $registro->id;
        $this->campo_readonly = $registro->campo_readonly;
        $this->campo_editable = $registro->campo_editable;
    }

    protected function rules()
    {
        return [
            'campo_editable' => [
                'required',
                Rule::unique(MiModelo::class, 'campo_editable')
                    ->ignore($this->registroId)  // ← IMPORTANTE para edición
            ],
        ];
    }

    public function guardar()
    {
        $this->validate();

        $registro = MiModelo::findOrFail($this->registroId);
        $registro->update([
            'campo_editable' => $this->campo_editable,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Registro actualizado correctamente!');
        $this->redirectRoute('ruta.index');
    }
}
```

---

## 6. VISTAS BLADE

### 🖼️ ESTRUCTURA ESTÁNDAR INDEX

```blade
<div>
    {{-- Mensajes de éxito/error --}}
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="¡Éxito!" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="light" title="Mi Módulo" icon="fas fa-icon">
        {{-- Filtros --}}
        <div class="row mb-3">
            <div class="col-md-5">
                <x-adminlte-input name="buscador" wire:model.live="buscador"
                    placeholder="Buscar..." igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="col-md-2">
                <a href="{{ route('ruta.create') }}" class="btn btn-success btn-block btn-sm">
                    <i class="fas fa-plus"></i> Nuevo
                </a>
            </div>
        </div>

        {{-- Tabla --}}
        @if($registros->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    {{-- Contenido --}}
                </table>
            </div>

            {{-- Paginación --}}
            <div class="d-flex justify-content-between">
                <div>
                    Mostrando {{ $registros->firstItem() }} a {{ $registros->lastItem() }}
                    de {{ $registros->total() }} registros
                </div>
                <div>
                    {{ $registros->links() }}
                </div>
            </div>
        @else
            {{-- Estado vacío --}}
            <div class="text-center py-4">
                <i class="fas fa-icon fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay registros</h5>
            </div>
        @endif
    </x-adminlte-card>
</div>
```

### 📋 ESTRUCTURA ESTÁNDAR CREATE/EDIT

```blade
<div>
    <x-adminlte-card theme="light" title="Crear/Editar" icon="fas fa-icon">
        <form class="row" wire:submit="guardar">

            {{-- Sección 1 --}}
            <div class="col-md-12">
                <h5 class="text-muted border-bottom pb-2 mb-3">Información General</h5>
            </div>

            <x-adminlte-input name="campo1" wire:model="campo1"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="Ingrese valor"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">Campo 1 *</div>
                </x-slot>
            </x-adminlte-input>

            {{-- Campos de solo lectura en EDIT --}}
            @if(isset($registroId))
                <x-adminlte-input name="campo_readonly"
                    value="{{ $campo_readonly }}"
                    type="text" readonly
                    fgroup-class="col-md-6" igroup-size="sm">
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-secondary">Campo Solo Lectura</div>
                    </x-slot>
                </x-adminlte-input>
            @endif

            <div class="col-md-12"><hr></div>

            {{-- Botones --}}
            <div class="form-group col-md-3">
                <a href="{{ route('ruta.index') }}"
                   class="btn btn-block btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="form-group col-md-3">
                <x-adminlte-button type="submit"
                    label="Guardar"
                    theme="outline-success"
                    icon="fas fa-save"
                    class="w-100 btn-sm" />
            </div>
        </form>
    </x-adminlte-card>
</div>
```

### 🎨 CONSISTENCIA EN DISEÑO

```blade
{{-- ✅ CORRECTO - Tamaños consistentes --}}
igroup-size="sm"        {{-- En todos los inputs --}}
class="btn btn-sm"      {{-- En todos los botones --}}
class="table table-sm"  {{-- En todas las tablas --}}

{{-- ✅ CORRECTO - Campos obligatorios marcados --}}
<x-slot name="prependSlot">
    <div class="input-group-text">Campo *</div>  {{-- Asterisco si es required --}}
</x-slot>

{{-- ✅ CORRECTO - Estados visuales claros --}}
<span class="badge badge-success">Activo</span>
<span class="badge badge-danger">Inactivo</span>
```

---

## 7. CAMPOS CALCULADOS

### 📐 PORCENTAJES Y CÁLCULOS

**Lección del error con Timbrados**:

```php
// ❌ INCORRECTO - Lógica incorrecta
public function getPorcentajeUsoAttribute(): float
{
    $total = $this->numero_hasta - $this->numero_desde + 1;
    $usado = $this->numero_actual - $this->numero_desde;  // ← Problema aquí
    return $total > 0 ? round(($usado / $total) * 100, 2) : 0;
}
// Si numero_actual = numero_desde - 1 (sin usar nada)
// usado = -1, porcentaje negativo!
```

```php
// ✅ CORRECTO - Considera el caso inicial
public function getPorcentajeUsoAttribute(): float
{
    $total = $this->numero_hasta - $this->numero_desde + 1;
    $usado = $this->numero_actual - $this->numero_desde + 1;  // ← +1 corrige

    if ($usado <= 0 || $total <= 0) {
        return 0;  // ← Manejo explícito del caso sin uso
    }

    return round(($usado / $total) * 100, 2);
}
```

### ✅ VALIDACIÓN DE LÓGICA

Antes de implementar un cálculo, probar casos extremos:

1. **Caso inicial** (sin uso): ¿Da 0%?
2. **Caso con 1 uso**: ¿Da el porcentaje esperado?
3. **Caso completo** (100% usado): ¿Da 100%?
4. **Caso fuera de rango**: ¿Maneja valores negativos?

---

## 8. TESTING Y VERIFICACIÓN

### ✅ CHECKLIST PRE-ENTREGA

#### Base de Datos
- [ ] Migraciones corren sin errores (`php artisan migrate:fresh`)
- [ ] Foreign keys están correctamente definidas
- [ ] Índices en campos de búsqueda frecuente
- [ ] Unique constraints donde corresponde
- [ ] Campos nullable vs required correctamente definidos

#### Modelos
- [ ] Relaciones definidas en ambas direcciones
- [ ] Casts definidos para booleans y dates
- [ ] SoftDeletes en entidades principales
- [ ] Auditoría implementada
- [ ] Scopes para filtros comunes

#### Validaciones
- [ ] Usar `Rule::` facade, no strings
- [ ] `->ignore()` en unique para edición
- [ ] `->when()` para validaciones condicionales
- [ ] Mensajes personalizados en español
- [ ] Campos enum con `Rule::requiredIf` si aplica

#### Livewire
- [ ] Resetear paginación en búsquedas
- [ ] `wire:model.live` en filtros
- [ ] No incluir campos readonly en `#[Validate]`
- [ ] Campos calculados no deben ser editables
- [ ] Auth::id() en creadoPor/actualizadoPor

#### Vistas
- [ ] Mensajes flash de éxito/error
- [ ] Estado vacío informativo
- [ ] Paginación con conteo de registros
- [ ] Botones de acción consistentes
- [ ] Confirmación en acciones destructivas
- [ ] Tamaños consistentes (sm en todo)

#### Funcionalidad
- [ ] Crear registro funciona
- [ ] Editar registro funciona
- [ ] No se pueden editar campos que no deberían cambiar
- [ ] Activar/Inactivar funciona
- [ ] Eliminar (soft delete) funciona
- [ ] Búsquedas funcionan
- [ ] Filtros funcionan
- [ ] Validaciones previenen datos incorrectos

### 🧪 CASOS DE PRUEBA MÍNIMOS

```
1. Crear registro con datos válidos ✓
2. Crear registro con datos inválidos (validación) ✓
3. Crear registro duplicado (unique) ✓
4. Editar registro existente ✓
5. Editar sin cambiar unique field ✓
6. Buscar registros ✓
7. Filtrar por estado ✓
8. Paginar resultados ✓
9. Activar/Inactivar ✓
10. Eliminar registro ✓
```

---

## 9. ORDEN DE DESARROLLO

### 📋 SECUENCIA RECOMENDADA

```
1. PLANIFICACIÓN
   - Definir entidades y relaciones
   - Diagrama de base de datos
   - Identificar campos calculados vs almacenados

2. MIGRACIONES
   - Crear en orden jerárquico
   - Probar: php artisan migrate:fresh
   - Verificar en base de datos

3. MODELOS
   - Definir fillable, casts, table
   - Implementar relaciones
   - Crear scopes básicos
   - Implementar SoftDeletes y Auditable

4. LIVEWIRE COMPONENTS
   - Index primero (para ver datos)
   - Create segundo
   - Edit tercero
   - Probar cada uno antes de continuar

5. VISTAS
   - Index con estado vacío
   - Create con validaciones visuales
   - Edit reutilizando estructura de Create

6. RUTAS
   - Agrupar por módulo
   - Usar resource si aplica
   - Nombres consistentes

7. CONFIGURACIÓN
   - Menú en adminlte.php
   - Permisos en seeder
   - Cualquier config específica

8. TESTING
   - Checklist de verificación
   - Casos de prueba mínimos
   - Correcciones iterativas
```

---

## 10. ERRORES COMUNES Y SOLUCIONES

### 🐛 ERROR 1: Database connection [schema] not configured

**Causa**: Usar string `'unique:schema.TABLA'` en validación

**Solución**:
```php
// Cambiar de:
'codigo' => 'unique:empresa.DEPOSITOS,codigo'

// A:
'codigo' => Rule::unique(Deposito::class, 'codigo')
```

**Configuración adicional**:
```php
// config/database.php
'search_path' => 'empresa,public',
```

---

### 🐛 ERROR 2: Check violation en ENUM

**Causa**: Intentar insertar string vacío en campo ENUM

**Código problemático**:
```php
// Checkbox marcado pero select vacío
'es_electronico' => true,
'cdc_ambiente' => '',  // ← String vacío, no NULL
```

**Solución**:
```php
// Validación
'cdc_ambiente' => [
    Rule::requiredIf($this->es_electronico),  // ← Requiere si checkbox marcado
    'nullable',
    Rule::in(['produccion', 'test'])
],

// Al guardar
'cdc_ambiente' => $this->es_electronico ? $this->cdc_ambiente : null,  // ← NULL, no ''
```

---

### 🐛 ERROR 3: Invalid text representation (empty foreign key)

**Causa**: Validación ejecutándose con foreign key vacía

**Código problemático**:
```php
Rule::unique(Deposito::class, 'codigo')
    ->where('sucursal_id', $this->sucursal_id)  // ← Si sucursal_id está vacío!
```

**Solución**:
```php
Rule::unique(Deposito::class, 'codigo')
    ->when($this->sucursal_id, function ($rule) {  // ← Solo si tiene valor
        return $rule->where('sucursal_id', $this->sucursal_id);
    })
```

---

### 🐛 ERROR 4: Porcentaje de uso incorrecto desde inicio

**Causa**: Permitir que usuario ingrese `numero_actual` en formulario

**Solución**:
```php
// NO incluir numero_actual en formulario CREATE
// Calcularlo automáticamente:
'numero_actual' => $this->numero_desde - 1,  // ← Antes del primer número

// En EDIT, mostrarlo como readonly
<x-adminlte-input name="numero_actual"
    value="{{ number_format($numero_actual, 0, '', '.') }}"
    readonly>
```

---

### 🐛 ERROR 5: String too long for tipo character(N)

**Causa**: Campo definido muy pequeño en migración

**Prevención**:
```php
// ❌ Evitar char(N) muy pequeños
$table->char('codigo', 3);

// ✅ Usar string con espacio suficiente
$table->string('codigo', 10);
```

**Solución si ya existe**:
```php
// Modificar migración
$table->string('codigo', 10);

// Y ejecutar ALTER en base de datos
DB::statement('ALTER TABLE schema.TABLA ALTER COLUMN codigo TYPE VARCHAR(10);');
```

---

### 🐛 ERROR 6: Campos readonly editables por error

**Causa**: Incluir campos de solo lectura en `#[Validate]`

**Código problemático**:
```php
#[Validate]
public $numero_actual = 0;  // ← Campo que NO debe editarse
```

**Solución**:
```php
// Sin #[Validate], solo para mostrar
public $numero_actual;

// No incluir en rules()
protected function rules() {
    return [
        // 'numero_actual' NO debe estar aquí
    ];
}

// En vista, readonly
<x-adminlte-input value="{{ $numero_actual }}" readonly>
```

---

### 🐛 ERROR 7: Validación unique falla en edición

**Causa**: No ignorar el registro actual al validar unique

**Código problemático**:
```php
// En Edit component
Rule::unique(Deposito::class, 'codigo')  // ← Detecta su propio registro como duplicado!
```

**Solución**:
```php
Rule::unique(Deposito::class, 'codigo')
    ->ignore($this->deposito->id)  // ← Ignorar registro actual
    ->when($this->sucursal_id, function ($rule) {
        return $rule->where('sucursal_id', $this->sucursal_id);
    })
```

---

## 📚 RECURSOS Y REFERENCIAS

### Documentación Oficial
- [Laravel 12 Validation](https://laravel.com/docs/12.x/validation)
- [Livewire 3 Documentation](https://livewire.laravel.com/docs)
- [PostgreSQL Schemas](https://www.postgresql.org/docs/current/ddl-schemas.html)

### Packages Utilizados
- `jeroennoten/laravel-adminlte` - UI AdminLTE
- `owen-it/laravel-auditing` - Auditoría
- `livewire/livewire` - Componentes reactivos

### Convenciones del Proyecto
- **Idioma**: Español en mensajes, inglés en código
- **Naming**: camelCase en PHP, kebab-case en rutas, UPPER_SNAKE_CASE en BD
- **Tamaños**: `sm` para todos los componentes AdminLTE

---

## 🎯 RESUMEN EJECUTIVO

### LAS 10 REGLAS DE ORO

1. **NUNCA** usar strings en validaciones con PostgreSQL schemas → Usar `Rule::`
2. **SIEMPRE** configurar `search_path` en config/database.php
3. **SIEMPRE** usar `->when()` para validaciones condicionales con FKs
4. **NUNCA** permitir editar campos calculados automáticamente
5. **SIEMPRE** usar `->ignore()` en unique para formularios de edición
6. **SIEMPRE** validar con `Rule::requiredIf()` para campos enum condicionales
7. **NUNCA** asumir tamaños de campos → Dar espacio suficiente desde el inicio
8. **SIEMPRE** implementar SoftDeletes y Auditoría en entidades principales
9. **SIEMPRE** probar casos extremos en campos calculados
10. **SIEMPRE** completar el checklist pre-entrega

### MANTRA DEL DESARROLLADOR

> "Lee el modelo, valida con Rules, calcula en accessors, y prueba los extremos"

---

**Última actualización**: Diciembre 2025
**Basado en**: Módulo Empresa - SIGEA v4
**Errores documentados y resueltos**: 7 categorías principales
