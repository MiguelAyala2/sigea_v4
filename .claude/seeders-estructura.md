# Estructura de Seeders - SIGEA v4

## 📋 Convenciones y Estándares

### 1. Ubicación de Archivos
```
database/seeders/
├── DatabaseSeeder.php           # Seeder principal de Laravel
├── RolYPermisoSeeder.php        # Seeders del sistema
├── ParaguayDemoSeeder.php       # Seeder principal de datos demo
├── Empresa/                     # Seeders del módulo Empresa
│   ├── EmpresaSeeder.php
│   ├── SucursalSeeder.php
│   ├── DepositoSeeder.php
│   └── TimbradoSeeder.php
└── Stock/                       # Seeders del módulo Stock
    ├── MarcaSeeder.php
    ├── UnidadMedidaSeeder.php
    ├── CategoriaSeeder.php
    ├── AtributoTipoSeeder.php
    ├── ProductoSeeder.php
    └── StockInicialSeeder.php
```

### 2. Namespace por Módulo
```php
// Módulo Empresa
namespace Database\Seeders\Empresa;

// Módulo Stock
namespace Database\Seeders\Stock;
```

### 3. Estructura Básica de un Seeder

```php
<?php

namespace Database\Seeders\[Modulo];

use App\Models\[Modulo]\[Modelo];
use Illuminate\Database\Seeder;

class [Nombre]Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Validar dependencias
        // 2. Definir datos
        // 3. Crear registros
        // 4. Mostrar mensaje de éxito
    }
}
```

## 🔧 Patrones Implementados

### Patrón 1: Seeder Simple (Catálogos)
**Usado en:** MarcaSeeder, UnidadMedidaSeeder, AtributoTipoSeeder

```php
class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            ['codigo' => 'XXX', 'nombre' => 'Nombre', 'descripcion' => 'Desc'],
            // ... más registros
        ];

        foreach ($marcas as $marca) {
            Marca::create([
                'codigo' => $marca['codigo'],
                'nombre' => $marca['nombre'],
                'descripcion' => $marca['descripcion'],
                'activo' => true,
                'creadoPor' => null, // ⚠️ IMPORTANTE: null para evitar FK cross-schema
            ]);
        }

        $this->command->info('✓ ' . count($marcas) . ' marcas creadas');
    }
}
```

### Patrón 2: Seeder con Dependencias
**Usado en:** SucursalSeeder, DepositoSeeder, TimbradoSeeder

```php
class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. VALIDAR DEPENDENCIA
        $empresa = Empresa::where('ruc', '80012345-6')->first();

        if (!$empresa) {
            $this->command->error('× Error: Empresa no encontrada. Ejecutar EmpresaSeeder primero.');
            return;
        }

        // 2. DEFINIR DATOS
        $sucursales = [
            [
                'empresa_id' => $empresa->id,
                'codigo_establecimiento' => '001',
                // ... más campos
                'creadoPor' => null,
            ],
        ];

        // 3. CREAR REGISTROS
        foreach ($sucursales as $sucursal) {
            Sucursal::create($sucursal);
            $this->command->info("✓ Sucursal creada: {$sucursal['nombre']}");
        }
    }
}
```

### Patrón 3: Seeder Jerárquico
**Usado en:** CategoriaSeeder

```php
class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        // NIVEL 1: Categorías principales
        $motobombas = $this->crearCategoria('MOTB', 'Motobombas', null, 1);

        // NIVEL 2: Subcategorías
        $perifericas = $this->crearCategoria('MOTB-PER', 'Periféricas', $motobombas, 2);

        // NIVEL 3: Sub-subcategorías
        $this->crearCategoria('MOTB-PER-05', 'Periféricas 1/2 HP', $perifericas, 3);

        $this->command->info('✓ Estructura de categorías creada (3 niveles jerárquicos)');
    }

    private function crearCategoria(string $codigo, string $nombre, ?Categoria $parent, int $nivel): Categoria
    {
        return Categoria::create([
            'parent_id' => $parent?->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => "Categoría: {$nombre}",
            'nivel' => $nivel,
            'activo' => true,
            'creadoPor' => null,
        ]);
    }
}
```

### Patrón 4: Seeder con Relaciones Múltiples
**Usado en:** ProductoSeeder

```php
class ProductoSeeder extends Seeder
{
    private $marcas;
    private $categorias;
    private $unidades;
    private $atributos;

    public function run(): void
    {
        // 1. CARGAR REFERENCIAS
        $this->cargarReferencias();

        // 2. CREAR PRODUCTOS POR CATEGORÍA
        $this->crearMotobombas();
        $this->crearTanques();
        $this->crearTubos();

        $this->command->info('✓ Productos creados con atributos y precios');
    }

    private function cargarReferencias(): void
    {
        $this->marcas = Marca::pluck('id', 'codigo')->toArray();
        $this->categorias = Categoria::pluck('id', 'codigo')->toArray();
        $this->unidades = UnidadMedida::pluck('id', 'codigo')->toArray();
        $this->atributos = AtributoTipo::pluck('id', 'codigo')->toArray();
    }

    private function crearMotobombas(): void
    {
        $producto = $this->crearProducto([
            'codigo' => 'MOTO-GRU-JP5-05',
            'nombre' => 'Motobomba Grundfos JP 5',
            'marca' => 'GRU',
            'categoria' => 'MOTB-PER-05',
            'unidad' => 'UN',
            'precio_compra' => 850000,
            'precio_venta' => 1200000,
            'stock_minimo' => 2,
        ]);

        $this->agregarAtributos($producto, [
            'POT' => '0.5',
            'VOLT' => '220',
        ]);
    }

    private function crearProducto(array $datos): Producto
    {
        // Crear producto
        $producto = Producto::create([
            'codigo' => $datos['codigo'],
            'nombre' => $datos['nombre'],
            'marca_id' => $this->marcas[$datos['marca']],
            'categoria_id' => $this->categorias[$datos['categoria']],
            'unidad_medida_id' => $this->unidades[$datos['unidad']],
            'stock_minimo' => $datos['stock_minimo'],
            'activo' => true,
            'creadoPor' => null,
        ]);

        // Crear precio
        Precio::create([
            'producto_id' => $producto->id,
            'precio_compra' => $datos['precio_compra'],
            'precio_venta' => $datos['precio_venta'],
            'iva' => '10',
            'moneda' => 'PYG',
            'es_actual' => true,
            'creadoPor' => null,
        ]);

        return $producto;
    }

    private function agregarAtributos(Producto $producto, array $atributos): void
    {
        foreach ($atributos as $codigo => $valor) {
            if (isset($this->atributos[$codigo])) {
                DB::table('stock.ATRIBUTOS_PRODUCTO')->insert([
                    'producto_id' => $producto->id,
                    'atributo_tipo_id' => $this->atributos[$codigo],
                    'valor' => $valor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
```

### Patrón 5: Seeder Maestro (Orquestador)
**Usado en:** ParaguayDemoSeeder

```php
class ParaguayDemoSeeder extends Seeder
{
    public function run(): void
    {
        // BANNER INICIAL
        $this->mostrarBanner();

        // VALIDAR PREREQUISITOS
        $user = User::first();
        if (!$user) {
            $this->command->error('× ERROR: No hay usuarios. Ejecutar RolYPermisoSeeder primero.');
            return;
        }

        // MÓDULO EMPRESA
        $this->command->info('🏢 MÓDULO EMPRESA');
        $this->call(EmpresaSeeder::class);
        $this->call(SucursalSeeder::class);
        $this->call(DepositoSeeder::class);
        $this->call(TimbradoSeeder::class);

        // MÓDULO STOCK - CATÁLOGOS
        $this->command->info('📦 MÓDULO STOCK - CATÁLOGOS BASE');
        $this->call(MarcaSeeder::class);
        $this->call(UnidadMedidaSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(AtributoTipoSeeder::class);

        // PRODUCTOS Y STOCK
        $this->command->info('🛒 PRODUCTOS Y STOCK INICIAL');
        $this->call(ProductoSeeder::class);
        $this->call(StockInicialSeeder::class);

        // RESUMEN FINAL
        $this->mostrarResumen();
    }

    private function mostrarBanner(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('   SEEDER DE DATOS DEMO - PARAGUAY');
        $this->command->info('   AGUATERÍA Y PLOMERÍA SIGEA S.A.');
        $this->command->info('═══════════════════════════════════════════════════════════');
    }

    private function mostrarResumen(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('   ✓ DATOS DE DEMOSTRACIÓN CARGADOS EXITOSAMENTE');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📊 RESUMEN:');
        $this->command->info('   EMPRESA: 1 empresa, 3 sucursales, 3 depósitos');
        $this->command->info('   STOCK: 15 marcas, 29 unidades, 45+ categorías');
        $this->command->info('🚀 Sistema listo para usar!');
    }
}
```

## ⚠️ REGLAS CRÍTICAS

### 1. Campo `creadoPor`
```php
// ❌ NUNCA HACER ESTO (causa problemas de FK cross-schema)
'creadoPor' => 1,

// ✅ SIEMPRE HACER ESTO
'creadoPor' => null,

// ⚡ EXCEPCIÓN: Solo en EmpresaSeeder (schema empresa)
'creadoPor' => User::first()->id,  // OK porque User está en public schema
```

**Razón:** PostgreSQL no maneja bien foreign keys entre schemas diferentes (`empresa`/`stock` → `public.users`)

### 2. Validar Estructura de Migraciones

**Antes de crear un seeder:**
1. Leer la migración correspondiente
2. Verificar nombres exactos de columnas
3. Verificar tipos y longitudes
4. No asumir campos que no existen

```php
// ❌ NO ASUMIR
'pais_origen' => 'Paraguay',  // Este campo puede no existir

// ✅ LEER MIGRACIÓN PRIMERO
// Si la migración no tiene 'pais_origen', usar 'descripcion'
'descripcion' => 'Tanques - Paraguay',
```

### 3. Orden de Ejecución

**CRÍTICO: Respetar dependencias**
```
1. RolYPermisoSeeder (crea usuarios)
2. EmpresaSeeder (requiere usuarios)
3. SucursalSeeder (requiere empresa)
4. DepositoSeeder (requiere sucursales)
5. TimbradoSeeder (requiere sucursales)
6. MarcaSeeder (independiente)
7. UnidadMedidaSeeder (independiente)
8. CategoriaSeeder (independiente)
9. AtributoTipoSeeder (independiente)
10. ProductoSeeder (requiere marcas, categorías, unidades)
11. StockInicialSeeder (requiere productos y depósitos)
```

### 4. Manejo de Errores

```php
// ✅ VALIDAR DEPENDENCIAS
$empresa = Empresa::first();
if (!$empresa) {
    $this->command->error('× Error: Empresa no encontrada.');
    return;  // Salir sin hacer nada
}

// ✅ VALIDAR COLECCIONES
if ($sucursales->isEmpty()) {
    $this->command->warn('× No hay sucursales.');
    continue;  // O return según el caso
}
```

### 5. Mensajes de Consola

```php
// ✅ Usar colores y símbolos consistentes
$this->command->info('✓ Registro creado exitosamente');     // Verde
$this->command->warn('⚠ Advertencia');                       // Amarillo
$this->command->error('× Error crítico');                    // Rojo

// ✅ Mostrar progreso
$this->command->info("✓ {$count} registros creados");
$this->command->info("✓ Producto creado: {$producto->nombre}");
```

## 🚀 Cómo Agregar un Nuevo Seeder

### Paso 1: Crear el archivo
```bash
php artisan make:seeder [Modulo]/[Nombre]Seeder
```

### Paso 2: Elegir el patrón apropiado
- **Catálogo simple** → Patrón 1
- **Tiene dependencias** → Patrón 2
- **Estructura jerárquica** → Patrón 3
- **Relaciones múltiples** → Patrón 4

### Paso 3: Implementar el seeder
```php
<?php

namespace Database\Seeders\[Modulo];

use App\Models\[Modulo]\[Modelo];
use Illuminate\Database\Seeder;

class [Nombre]Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Leer migración para verificar campos

        // 2. Validar dependencias si las hay

        // 3. Definir datos
        $datos = [
            // ...
        ];

        // 4. Crear registros
        foreach ($datos as $dato) {
            [Modelo]::create([
                // ...
                'creadoPor' => null,  // ⚠️ IMPORTANTE
            ]);
        }

        // 5. Mensaje de éxito
        $this->command->info('✓ ' . count($datos) . ' registros creados');
    }
}
```

### Paso 4: Agregar al seeder maestro
```php
// En ParaguayDemoSeeder.php
$this->call([Modulo]\[Nombre]Seeder::class);
```

### Paso 5: Probar
```bash
# Limpiar y ejecutar todo
php artisan migrate:fresh --seed

# O ejecutar solo tu seeder
php artisan db:seed --class="Database\\Seeders\\[Modulo]\\[Nombre]Seeder"
```

## 📝 Ejemplo Completo: Agregar ProveedorSeeder

```php
<?php

namespace Database\Seeders\Empresa;

use App\Models\Empresa\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'codigo' => 'PROV-001',
                'razon_social' => 'Distribuidora Aqua S.A.',
                'ruc' => '80023456-7',
                'telefono' => '(021) 555-9999',
                'email' => 'ventas@aqua.com.py',
                'activo' => true,
            ],
            [
                'codigo' => 'PROV-002',
                'razon_social' => 'Importadora Plomería Global',
                'ruc' => '80034567-8',
                'telefono' => '(021) 555-8888',
                'email' => 'info@plomglobal.com.py',
                'activo' => true,
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create([
                'codigo' => $proveedor['codigo'],
                'razon_social' => $proveedor['razon_social'],
                'ruc' => $proveedor['ruc'],
                'telefono' => $proveedor['telefono'],
                'email' => $proveedor['email'],
                'activo' => $proveedor['activo'],
                'creadoPor' => null,  // ⚠️ IMPORTANTE
            ]);

            $this->command->info("✓ Proveedor creado: {$proveedor['razon_social']}");
        }

        $this->command->info('✓ ' . count($proveedores) . ' proveedores creados');
    }
}
```

Agregar a `ParaguayDemoSeeder.php`:
```php
$this->call(ProveedorSeeder::class);
```

## 🔍 Troubleshooting Común

### Error: "Undefined column"
**Causa:** Campo no existe en la migración
**Solución:** Leer la migración y verificar nombres exactos

### Error: "Foreign key violation"
**Causa:** `creadoPor` apunta a usuario inexistente o cross-schema FK
**Solución:** Usar `creadoPor => null`

### Error: "Unique violation"
**Causa:** Seeder ejecutado múltiples veces
**Solución:** Limpiar datos antes: `DB::table('tabla')->delete();`

### Error: "String data too long"
**Causa:** Código o campo excede longitud definida
**Solución:** Acortar códigos o ajustar migración

## 📊 Estado Actual de Seeders

### ✅ Completados y Funcionando
- EmpresaSeeder
- SucursalSeeder
- DepositoSeeder
- TimbradoSeeder
- MarcaSeeder
- UnidadMedidaSeeder
- CategoriaSeeder
- AtributoTipoSeeder
- StockInicialSeeder
- ParaguayDemoSeeder

### ⚠️ Requiere Ajuste Menor
- **ProductoSeeder**: Algunos códigos de producto exceden 20 caracteres
  - Solución: Acortar códigos en el método `crearProducto()`

---
Contenido del Documento
1. Convenciones y Estándares
Ubicación de archivos y estructura de carpetas
Namespaces por módulo
Estructura básica de un seeder
2. 5 Patrones de Diseño Implementados
Patrón 1: Seeders Simples (catálogos como Marcas, Unidades)
Patrón 2: Seeders con Dependencias (Sucursales, Depósitos)
Patrón 3: Seeders Jerárquicos (Categorías con 3 niveles)
Patrón 4: Seeders con Relaciones Múltiples (Productos con precios y atributos)
Patrón 5: Seeder Maestro Orquestador (ParaguayDemoSeeder)
3. ⚠️ Reglas Críticas
Campo creadoPor: Siempre null para evitar FK cross-schema
Validar Migraciones: Leer migración antes de crear seeder
Orden de Ejecución: Diagrama de dependencias
Manejo de Errores: Validaciones y mensajes
Mensajes de Consola: Convenciones de colores y símbolos
4. 🚀 Guía Paso a Paso
Cómo agregar un nuevo seeder en 5 pasos
Ejemplo completo: ProveedorSeeder
Comandos para ejecutar y probar
5. 🔍 Troubleshooting
Errores comunes y sus soluciones
Estado actual de los seeders (qué funciona y qué necesita ajuste)
Este documento te servirá como referencia rápida para:
✅ Entender cómo están organizados los seeders actuales
✅ Crear nuevos seeders siguiendo los patrones establecidos
✅ Evitar errores comunes (FK cross-schema, campos faltantes, etc.)
✅ Mantener consistencia en el código


**Última actualización:** 2025-12-07
**Autor:** Claude Code
**Sistema:** SIGEA v4 - Paraguay
