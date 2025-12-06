# MODELO CONCEPTUAL - MÓDULO EMPRESA
## Sistema Integrado de Gestión Empresarial (SIGEA v4)

---

## DIAGRAMA JERÁRQUICO

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                   EMPRESA                                    │
│━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━│
│ Entidad raíz del sistema (Singleton)                                        │
│                                                                              │
│ • Razón Social, Nombre Fantasía                                             │
│ • RUC + DV (Dígito Verificador)                                             │
│ • Tipo Contribuyente (Física/Jurídica)                                      │
│ • Régimen Tributario                                                         │
│ • Obligado Factura Electrónica                                              │
│ • Datos de Contacto (dirección, teléfono, email, web)                       │
│ • Logo, Fecha Inicio Actividad                                              │
└──────┬────────────────────────────────┬──────────────────────────────────────┘
       │                                │
       │ 1:N                            │ N:M
       ▼                                ▼
┌─────────────────────┐    ┌──────────────────────────────┐
│    SUCURSALES       │    │ ACTIVIDADES ECONÓMICAS       │
│━━━━━━━━━━━━━━━━━━━━━│    │━━━━━━━━━━━━━━━━━━━━━━━━━━━━━│
│ Establecimientos    │    │ Rubros de la empresa         │
│                     │    │                              │
│ • Código            │    │ • Código                     │
│   Establecimiento   │    │ • Descripción                │
│ • Nombre            │    │ • Activo                     │
│ • Dirección         │    │                              │
│ • Departamento      │    │ Relación N:M con Empresa     │
│ • Ciudad            │    │ Pivot: es_principal          │
│ • Teléfono, Email   │    │                              │
│ • Es Casa Central   │    └──────────────────────────────┘
│ • Activo            │
└──────┬──────┬───────┘
       │      │
       │ 1:N  │ 1:N
       ▼      ▼
┌──────────────────┐  ┌────────────────────────┐
│   DEPÓSITOS      │  │ PUNTOS DE EXPEDICIÓN   │
│━━━━━━━━━━━━━━━━━━│  │━━━━━━━━━━━━━━━━━━━━━━━━│
│ Almacenes        │  │ Puntos de facturación  │
│                  │  │                        │
│ • Código         │  │ • Código               │
│ • Nombre         │  │ • Nombre               │
│ • Descripción    │  │ • Tipo:                │
│ • Es Principal   │  │   - Caja               │
│ • Permite Venta  │  │   - Terminal           │
│ • Activo         │  │   - Web                │
│                  │  │ • Activo               │
│ Código único por │  │                        │
│ sucursal         │  │ Código único por       │
└──────────────────┘  │ sucursal               │
                      └────────────────────────┘

       ┌─────────────────────────────────────────┐
       │            TIMBRADOS                    │
       │━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━│
       │ Autorizaciones de facturación           │
       │                                         │
       │ • Número Timbrado                       │
       │ • Fecha Inicio/Fin Vigencia             │
       │ • Tipo Documento:                       │
       │   - Factura                             │
       │   - Nota de Crédito                     │
       │   - Nota de Débito                      │
       │   - Nota de Remisión                    │
       │   - Comprobante de Retención            │
       │ • Rango Numeración (desde-hasta)        │
       │ • Número Actual                         │
       │ • Es Electrónico                        │
       │ • Ambiente CDC (Producción/Test)        │
       │ • Porcentaje de Uso (calculado)         │
       │ • Estado: Vigente/Agotado/Por Vencer    │
       │                                         │
       │ Relación directa 1:N con EMPRESA        │
       └─────────────────────────────────────────┘
```

---

## JERARQUÍA Y RELACIONES

### Nivel 1: EMPRESA (Raíz)
- **Cardinalidad**: Singleton (una sola empresa por sistema)
- **Propósito**: Entidad principal que representa la organización
- **Método especial**: `Empresa::actual()` retorna la instancia única
- **Tabla**: `empresa.EMPRESA`

### Nivel 2: Entidades Dependientes de Empresa

#### 2.1 SUCURSALES
- **Relación**: `1:N` (Una empresa tiene muchas sucursales)
- **Jerarquía**: Nivel 2
- **Tabla**: `empresa.SUCURSALES`
- **Características**:
  - Establecimientos físicos de la empresa
  - Una debe ser marcada como "Casa Central"
  - Código de establecimiento único por empresa
  - Foreign Key: `empresa_id`

#### 2.2 ACTIVIDADES ECONÓMICAS
- **Relación**: `N:M` (Una empresa tiene muchas actividades, una actividad puede estar en muchas empresas)
- **Tabla Principal**: `empresa.ACTIVIDADES_ECONOMICAS`
- **Tabla Pivot**: `empresa.EMPRESA_ACTIVIDAD_ECONOMICA`
- **Atributo Pivot**: `es_principal` (indica actividad económica principal)
- **Características**: Rubros o giros comerciales de la empresa
- **Foreign Keys Pivot**: `empresa_id`, `actividad_economica_id`

#### 2.3 TIMBRADOS
- **Relación**: `1:N` (Una empresa tiene muchos timbrados)
- **Jerarquía**: Nivel 2
- **Tabla**: `empresa.TIMBRADOS`
- **Propósito**: Autorizaciones fiscales para emitir documentos
- **Foreign Key**: `empresa_id`
- **Características**:
  - Control de numeración de documentos fiscales
  - Vigencia temporal
  - Seguimiento de uso (porcentaje utilizado)
  - Soporte para facturación electrónica

### Nivel 3: Entidades Dependientes de Sucursal

#### 3.1 DEPÓSITOS
- **Relación**: `1:N` (Una sucursal tiene muchos depósitos)
- **Jerarquía**: Nivel 3
- **Tabla**: `empresa.DEPOSITOS`
- **Propósito**: Almacenes o depósitos de inventario
- **Foreign Key**: `sucursal_id`
- **Características**:
  - Uno puede ser marcado como "Principal"
  - Control de si permite ventas directas
  - Código único por sucursal
  - Ubicación completa: `Sucursal / Depósito`
  - Unique constraint: `(sucursal_id, codigo)`

#### 3.2 PUNTOS DE EXPEDICIÓN
- **Relación**: `1:N` (Una sucursal tiene muchos puntos de expedición)
- **Jerarquía**: Nivel 3
- **Tabla**: `empresa.PUNTOS_EXPEDICION`
- **Propósito**: Puntos físicos o virtuales donde se emiten facturas
- **Foreign Key**: `sucursal_id`
- **Tipos**: Caja, Terminal, Web
- **Características**:
  - Código único por sucursal
  - Código completo: `CódigoEstablecimiento-CódigoPunto`
  - Usado para numeración de comprobantes
  - Unique constraint: `(sucursal_id, codigo)`

---

## CONCEPTOS CLAVE

### 1. Auditoría
Todas las entidades implementan auditoría mediante `OwenIt\Auditing`:
- Registro de creación y modificación
- Campos: `creadoPor`, `actualizadoPor` (relación con User)
- Soft Deletes en entidades principales
- Timestamps automáticos

### 2. Reglas de Negocio

#### Unicidad de Códigos
- **Depósitos**: código único por sucursal
- **Puntos Expedición**: código único por sucursal
- **Sucursales**: código establecimiento único por empresa

#### Restricciones Lógicas
- Solo una sucursal puede ser Casa Central
- Solo un depósito por sucursal puede ser Principal
- Solo una actividad económica puede ser Principal

#### Timbrados - Lógica Especial
- `numero_actual` se inicializa en `numero_desde - 1`
- Porcentaje de uso calculado:
  ```php
  $total = numero_hasta - numero_desde + 1;
  $usado = numero_actual - numero_desde + 1;
  porcentaje = (usado / total) * 100
  ```
- Estados calculados mediante accessors:
  - `esta_vigente`: fecha actual entre fecha_inicio y fecha_fin
  - `esta_agotado`: numero_actual >= numero_hasta
  - `proximo_a_vencer`: <= 30 días para vencimiento
- Método `obtenerSiguienteNumero()` para consumir numeración

### 3. Validación con PostgreSQL

#### Problema de Schema
Laravel interpreta `empresa.TABLA` como `conexion.tabla`, no como `schema.tabla`

**Solución aplicada**:
```php
// ❌ INCORRECTO
'unique:empresa.DEPOSITOS,codigo'

// ✅ CORRECTO
Rule::unique(Deposito::class, 'codigo')
    ->when($this->sucursal_id, function ($rule) {
        return $rule->where('sucursal_id', $this->sucursal_id);
    })
```

#### Configuración PostgreSQL
```php
// config/database.php
'search_path' => 'empresa,public',
```

### 4. Campos Calculados vs Almacenados

#### Campos Calculados (Accessors)
- `porcentaje_uso` (Timbrado)
- `esta_vigente` (Timbrado)
- `esta_agotado` (Timbrado)
- `ruc_completo` (Empresa)
- `nombre_completo` (varias entidades)

#### Campos Almacenados
- `numero_actual` (Timbrado) - se incrementa con uso
- `es_principal` (Deposito, ActividadEconomica)
- `es_casa_central` (Sucursal)

---

## FLUJO JERÁRQUICO

```
EMPRESA (Singleton)
  ├─► ACTIVIDADES ECONÓMICAS (N:M) - Clasificación
  ├─► TIMBRADOS (1:N) - Para facturación
  └─► SUCURSALES (1:N) - Establecimientos
        ├─► DEPÓSITOS (1:N) - Para inventario
        └─► PUNTOS EXPEDICIÓN (1:N) - Para facturación
```

---

## ESQUEMA DE BASE DE DATOS

### Información General
- **Schema PostgreSQL**: `empresa`
- **Convención de nombres**: MAYÚSCULAS con guiones bajos
- **Configuración especial**: `search_path = 'empresa,public'`

### Tablas del Sistema

1. **empresa.EMPRESA**
   - Primary Key: `id`
   - Unique: `ruc`
   - Relaciones: hasMany(Sucursales, Timbrados), belongsToMany(ActividadesEconomicas)

2. **empresa.SUCURSALES**
   - Primary Key: `id`
   - Foreign Key: `empresa_id`
   - Relaciones: belongsTo(Empresa), hasMany(Depositos, PuntosExpedicion)

3. **empresa.DEPOSITOS**
   - Primary Key: `id`
   - Foreign Key: `sucursal_id`
   - Unique: `(sucursal_id, codigo)`
   - Relaciones: belongsTo(Sucursal)

4. **empresa.PUNTOS_EXPEDICION**
   - Primary Key: `id`
   - Foreign Key: `sucursal_id`
   - Unique: `(sucursal_id, codigo)`
   - Relaciones: belongsTo(Sucursal)

5. **empresa.ACTIVIDADES_ECONOMICAS**
   - Primary Key: `id`
   - Unique: `codigo`
   - Relaciones: belongsToMany(Empresas)

6. **empresa.EMPRESA_ACTIVIDAD_ECONOMICA** (Pivot)
   - Composite Primary Key: `(empresa_id, actividad_economica_id)`
   - Atributo: `es_principal`

7. **empresa.TIMBRADOS**
   - Primary Key: `id`
   - Foreign Key: `empresa_id`
   - Enums: `tipo_documento`, `cdc_ambiente`
   - Relaciones: belongsTo(Empresa)

---

## CASOS DE USO TÍPICOS

### 1. Configuración Inicial del Sistema
```
Paso 1: Crear EMPRESA
Paso 2: Asignar ACTIVIDADES ECONÓMICAS
Paso 3: Crear SUCURSAL (Casa Central)
Paso 4: Crear DEPÓSITO (Principal) en la sucursal
Paso 5: Crear PUNTO EXPEDICIÓN (Caja 1) en la sucursal
Paso 6: Crear TIMBRADO para facturación

Resultado: Sistema listo para operar
```

### 2. Emisión de Comprobante Fiscal
```
Componentes necesarios:
- EMPRESA (datos del emisor)
- TIMBRADO (autorización y numeración)
- SUCURSAL (establecimiento)
- PUNTO EXPEDICIÓN (punto de emisión)

Flujo:
1. Obtener siguiente número del TIMBRADO
2. Formato: RUC-Establecimiento-PuntoExpedición-Número
3. Ejemplo: 80012345-001-001-0000001
```

### 3. Gestión Multi-sucursal
```
EMPRESA
  ├─► SUCURSAL: Casa Central (001)
  │     ├─► DEPÓSITO: Principal
  │     └─► PUNTO EXPEDICIÓN: Caja 1, Caja 2, Web
  │
  ├─► SUCURSAL: Sucursal Este (002)
  │     ├─► DEPÓSITO: Depósito Este
  │     └─► PUNTO EXPEDICIÓN: Caja 1
  │
  └─► SUCURSAL: Sucursal Oeste (003)
        ├─► DEPÓSITO: Depósito Oeste
        └─► PUNTO EXPEDICIÓN: Caja 1, Terminal

Cada sucursal tiene control independiente de:
- Stock (por depósito)
- Facturación (por punto de expedición)
```

### 4. Control de Stock por Ubicación
```
Consulta: ¿Dónde está el producto X?

Respuesta:
- Sucursal Casa Central / Depósito Principal: 100 unidades
- Sucursal Este / Depósito Este: 50 unidades
- Sucursal Oeste / Depósito Oeste: 75 unidades

Total: 225 unidades distribuidas en 3 ubicaciones
```

### 5. Seguimiento de Timbrados
```
TIMBRADO: 12345678901234
- Tipo: Factura
- Rango: 1 - 10.000
- Actual: 0
- Uso: 0.00%
- Estado: Vigente
- Vigencia: 01/12/2025 - 31/12/2025

Después de usar 100 facturas:
- Actual: 100
- Uso: 1.00%
- Disponibles: 9.900
```

---

## ORDEN DE CREACIÓN DEL MÓDULO

### 1. MIGRACIONES
```
2025_12_06_004854_create_empresa_schema.php
2025_12_06_004906_create_empresa_table.php
2025_12_06_005020_create_actividades_economicas_table.php
2025_12_06_005041_create_empresa_actividad_economica_table.php
2025_12_06_005047_create_sucursales_table.php
2025_12_06_005053_create_depositos_table.php
2025_12_06_005100_create_puntos_expedicion_table.php
2025_12_06_005104_create_timbrados_table.php
```

### 2. MODELS
```
App\Models\Empresa\Empresa.php
App\Models\Empresa\ActividadEconomica.php
App\Models\Empresa\Sucursal.php
App\Models\Empresa\Deposito.php
App\Models\Empresa\PuntoExpedicion.php
App\Models\Empresa\Timbrado.php
```

### 3. LIVEWIRE COMPONENTS
```
App\Livewire\Empresa\Empresa\Index.php
App\Livewire\Empresa\Empresa\Create.php
App\Livewire\Empresa\Empresa\Edit.php

App\Livewire\Empresa\Sucursales\Index.php
App\Livewire\Empresa\Sucursales\Create.php
App\Livewire\Empresa\Sucursales\Edit.php

App\Livewire\Empresa\Depositos\Index.php
App\Livewire\Empresa\Depositos\Create.php
App\Livewire\Empresa\Depositos\Edit.php

App\Livewire\Empresa\PuntosExpedicion\Index.php
App\Livewire\Empresa\PuntosExpedicion\Create.php
App\Livewire\Empresa\PuntosExpedicion\Edit.php

App\Livewire\Empresa\Timbrados\Index.php
App\Livewire\Empresa\Timbrados\Create.php
App\Livewire\Empresa\Timbrados\Edit.php
```

### 4. VIEWS (Blade)
```
resources/views/empresa/index.blade.php
resources/views/empresa/create.blade.php
resources/views/empresa/edit.blade.php

resources/views/livewire/empresa/sucursales/
resources/views/livewire/empresa/depositos/
resources/views/livewire/empresa/puntos-expedicion/
resources/views/livewire/empresa/timbrados/
```

### 5. RUTAS
```
routes/empresa.php
```

### 6. CONFIGURACIÓN
```
config/adminlte.php (actualización del menú)
database/seeders/RolYPermisoSeeder.php (permisos)
config/database.php (search_path de PostgreSQL)
```

---

## NOTAS TÉCNICAS IMPORTANTES

### PostgreSQL Schema Support
El sistema usa schemas de PostgreSQL para organización lógica. Configuración requerida:
- `search_path = 'empresa,public'` en `config/database.php`
- Validaciones deben usar `Rule` facade con modelos, no strings

### Livewire 3.x
- Atributo `#[Validate]` para propiedades validables
- Métodos `mount()`, `render()`, `guardar()`
- Scopes en modelos para filtros reutilizables

### AdminLTE Components
- `x-adminlte-card`, `x-adminlte-input`, `x-adminlte-select`
- Tamaño consistente: `igroup-size="sm"`
- Iconos Font Awesome

### Auditoría
- Package: `owen-it/laravel-auditing`
- Todas las entidades principales implementan `Auditable`
- Soft deletes en entidades principales

---

**Fecha de documentación**: Diciembre 2025
**Versión del sistema**: SIGEA v4
**Framework**: Laravel 12.26.4 / PHP 8.4.15 / PostgreSQL
