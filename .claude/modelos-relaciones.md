# MODELOS Y RELACIONES - SIGEA v4

## 📋 Tabla de Contenidos

1. [Schema Public - Sistema](#schema-public---sistema)
2. [Schema Empresa - Módulo Empresa](#schema-empresa---módulo-empresa)
3. [Schema Stock - Módulo Stock](#schema-stock---módulo-stock)
4. [Relaciones Cross-Schema](#relaciones-cross-schema)
5. [Traits y Características Comunes](#traits-y-características-comunes)
6. [Scopes Útiles](#scopes-útiles)
7. [Ejemplos de Uso](#ejemplos-de-uso)

---

## Schema `public` - Sistema

### User (App\Models\User)

**Tabla:** `public.users`

**Campos:**
```php
id                  - bigint
name                - varchar(255)
usuario             - varchar(255) unique
email               - varchar(255) unique
password            - varchar(255)
activo              - boolean (default true)
remember_token      - varchar(100) nullable
created_at          - timestamp
updated_at          - timestamp
deleted_at          - timestamp nullable
```

**Traits:**
```php
use HasFactory;
use Notifiable;
use SoftDeletes;
use HasRoles;  // Spatie Permission
```

**Relaciones:**
```php
// Relaciones Spatie Permission
roles()           - belongsToMany(Role::class)
permissions()     - belongsToMany(Permission::class)

// Relaciones con otros módulos
empresasCreadas() - hasMany(Empresa::class, 'creadoPor')
```

**Configuración de tabla:**
```php
protected $table = 'users';  // En schema public
```

---

## Schema `empresa` - Módulo Empresa

### Empresa (App\Models\Empresa\Empresa)

**Tabla:** `empresa.EMPRESA`

**Campos:**
```php
id                              - bigint
razon_social                    - varchar(200)
nombre_fantasia                 - varchar(150) nullable
ruc                             - varchar(15) unique
dv                              - char(1)
tipo_contribuyente              - enum('fisica', 'juridica')
regimen_tributario              - enum('general', 'simplificado', 'resimple')
obligado_factura_electronica    - boolean
direccion                       - varchar(255)
departamento                    - varchar(50)
ciudad                          - varchar(50)
telefono                        - varchar(30) nullable
email                           - varchar(100) nullable
sitio_web                       - varchar(100) nullable
logo_path                       - varchar(255) nullable
fecha_inicio_actividad          - date nullable
activo                          - boolean
creadoPor                       - bigint nullable
actualizadoPor                  - bigint nullable
created_at                      - timestamp
updated_at                      - timestamp
deleted_at                      - timestamp nullable
```

**Traits:**
```php
use HasFactory;
use SoftDeletes;
use Auditable;
```

**Relaciones:**
```php
// 1:N con Sucursales
sucursales() - hasMany(Sucursal::class)

// 1:N con Timbrados
timbrados() - hasMany(Timbrado::class)

// N:N con Actividades Económicas
actividadesEconomicas() - belongsToMany(ActividadEconomica::class,
    'empresa.EMPRESA_ACTIVIDAD_ECONOMICA',
    'empresa_id',
    'actividad_economica_id'
)

// Relación con User (schema public)
creador() - belongsTo(User::class, 'creadoPor')
actualizador() - belongsTo(User::class, 'actualizadoPor')
```

**Configuración de tabla:**
```php
protected $connection = 'pgsql';
protected $table = 'empresa.EMPRESA';
protected $guarded = ['id'];
```

**Casts:**
```php
protected $casts = [
    'obligado_factura_electronica' => 'boolean',
    'fecha_inicio_actividad' => 'date',
    'activo' => 'boolean',
];
```

---

### Sucursal (App\Models\Empresa\Sucursal)

**Tabla:** `empresa.SUCURSALES`

**Campos:**
```php
id                      - bigint
empresa_id              - bigint FK → EMPRESA
codigo_establecimiento  - varchar(3)
nombre                  - varchar(150)
direccion               - varchar(255)
departamento            - varchar(50)
ciudad                  - varchar(50)
telefono                - varchar(30) nullable
email                   - varchar(100) nullable
es_casa_central         - boolean
activo                  - boolean
creadoPor               - bigint nullable
actualizadoPor          - bigint nullable
created_at              - timestamp
updated_at              - timestamp
deleted_at              - timestamp nullable
```

**Traits:**
```php
use HasFactory;
use SoftDeletes;
use Auditable;
```

**Relaciones:**
```php
// N:1 con Empresa
empresa() - belongsTo(Empresa::class)

// 1:N con Depósitos
depositos() - hasMany(Deposito::class)

// 1:N con Puntos de Expedición
puntosExpedicion() - hasMany(PuntoExpedicion::class)

// 1:N con Timbrados (a través de empresa)
timbrados() - hasMany(Timbrado::class, 'empresa_id', 'empresa_id')
```

**Configuración:**
```php
protected $table = 'empresa.SUCURSALES';
protected $guarded = ['id'];
```

**Scopes:**
```php
scopeActivas($query)
scopeCasaCentral($query)
scopePorEmpresa($query, $empresaId)
```

---

### Deposito (App\Models\Empresa\Deposito)

**Tabla:** `empresa.DEPOSITOS`

**Campos:**
```php
id                  - bigint
sucursal_id         - bigint FK → SUCURSALES
codigo              - varchar(20) unique
nombre              - varchar(100)
descripcion         - text nullable
es_principal        - boolean
permite_venta       - boolean
activo              - boolean
creadoPor           - bigint nullable
actualizadoPor      - bigint nullable
created_at          - timestamp
updated_at          - timestamp
deleted_at          - timestamp nullable
```

**Traits:**
```php
use HasFactory;
use SoftDeletes;
use Auditable;
```

**Relaciones:**
```php
// N:1 con Sucursal
sucursal() - belongsTo(Sucursal::class)

// 1:N con Stock (cross-schema a stock.STOCK)
stocks() - hasMany(\App\Models\Stock\Stock::class)
```

**Configuración:**
```php
protected $table = 'empresa.DEPOSITOS';
protected $guarded = ['id'];
```

**Scopes:**
```php
scopeActivos($query)
scopePrincipales($query)
scopePermiteVenta($query)
scopePorSucursal($query, $sucursalId)
```

---

### Timbrado (App\Models\Empresa\Timbrado)

**Tabla:** `empresa.TIMBRADOS`

**Campos:**
```php
id                      - bigint
empresa_id              - bigint FK → EMPRESA
numero_timbrado         - varchar(20)
fecha_inicio_vigencia   - date
fecha_fin_vigencia      - date
tipo_documento          - enum('factura', 'nota_credito', 'nota_debito', 'remision')
numero_desde            - bigint
numero_hasta            - bigint
numero_actual           - bigint
es_electronico          - boolean
cdc_ambiente            - enum('test', 'produccion')
activo                  - boolean
creadoPor               - bigint nullable
actualizadoPor          - bigint nullable
created_at              - timestamp
updated_at              - timestamp
```

**Traits:**
```php
use HasFactory;
use Auditable;
```

**Relaciones:**
```php
// N:1 con Empresa
empresa() - belongsTo(Empresa::class)
```

**Configuración:**
```php
protected $table = 'empresa.TIMBRADOS';
protected $guarded = ['id'];
```

**Casts:**
```php
protected $casts = [
    'fecha_inicio_vigencia' => 'date',
    'fecha_fin_vigencia' => 'date',
    'es_electronico' => 'boolean',
    'activo' => 'boolean',
];
```

**Scopes:**
```php
scopeActivos($query)
scopeVigentes($query)
scopeElectronicos($query)
scopePorTipoDocumento($query, $tipo)
```

---

## Schema `stock` - Módulo Stock

### Marca (App\Models\Stock\Marca)

**Tabla:** `stock.MARCAS`

**Campos:**
```php
id              - bigint
codigo          - varchar(20) unique
nombre          - varchar(100)
descripcion     - text nullable
activo          - boolean
creadoPor       - bigint nullable (⚠️ siempre NULL)
actualizadoPor  - bigint nullable (⚠️ siempre NULL)
created_at      - timestamp
updated_at      - timestamp
deleted_at      - timestamp nullable
```

**Traits:**
```php
use HasFactory;
use SoftDeletes;
use Auditable;
```

**Relaciones:**
```php
// 1:N con Productos
productos() - hasMany(Producto::class)
```

**Configuración:**
```php
protected $table = 'stock.MARCAS';
protected $guarded = ['id'];
```

**Scopes:**
```php
scopeActivas($query)
scopeBuscar($query, $termino)
```

---

### UnidadMedida (App\Models\Stock\UnidadMedida)

**Tabla:** `stock.UNIDADES_MEDIDA`

**Campos:**
```php
id                  - bigint
codigo              - varchar(20) unique
nombre              - varchar(50)
simbolo             - varchar(10)
permite_decimales   - boolean
activo              - boolean
creadoPor           - bigint nullable
actualizadoPor      - bigint nullable
created_at          - timestamp
updated_at          - timestamp
deleted_at          - timestamp nullable
```

**Traits:**
```php
use HasFactory;
use SoftDeletes;
use Auditable;
```

**Relaciones:**
```php
// 1:N con Productos
productos() - hasMany(Producto::class, 'unidad_medida_id')
```

**Configuración:**
```php
protected $table = 'stock.UNIDADES_MEDIDA';
protected $guarded = ['id'];
```

**Casts:**
```php
protected $casts = [
    'permite_decimales' => 'boolean',
    'activo' => 'boolean',
];
```

---

### Categoria (App\Models\Stock\Categoria)

**Tabla:** `stock.CATEGORIAS`

**Campos:**
```php
id              - bigint
parent_id       - bigint nullable FK → CATEGORIAS (recursiva)
codigo          - varchar(50) unique
nombre          - varchar(150)
descripcion     - text nullable
nivel           - integer
activo          - boolean
creadoPor       - bigint nullable
actualizadoPor  - bigint nullable
created_at      - timestamp
updated_at      - timestamp
deleted_at      - timestamp nullable
```

**Traits:**
```php
use HasFactory;
use SoftDeletes;
use Auditable;
```

**Relaciones:**
```php
// Relación recursiva
parent()    - belongsTo(Categoria::class, 'parent_id')
hijos()     - hasMany(Categoria::class, 'parent_id')

// 1:N con Productos
productos() - hasMany(Producto::class, 'categoria_id')
```

**Configuración:**
```php
protected $table = 'stock.CATEGORIAS';
protected $guarded = ['id'];
```

**Scopes:**
```php
scopeActivas($query)
scopeRaices($query)              // Categorías sin parent_id
scopeNivel($query, $nivel)
scopeDescendientes($query, $categoriaId)  // Recursivo
```

**Métodos Auxiliares:**
```php
// Obtener toda la jerarquía de padres
getAncestors()

// Obtener todos los hijos y descendientes
getDescendants()

// Verificar si es categoría raíz
isRoot()

// Verificar si es categoría hoja (sin hijos)
isLeaf()
```

---

### AtributoTipo (App\Models\Stock\AtributoTipo)

**Tabla:** `stock.ATRIBUTOS_TIPO`

**Campos:**
```php
id              - bigint
codigo          - varchar(50) unique
nombre          - varchar(100)
unidad          - varchar(20) nullable
descripcion     - text nullable
orden           - integer
es_filtrable    - boolean
es_requerido    - boolean
activo          - boolean
creadoPor       - bigint nullable
actualizadoPor  - bigint nullable
created_at      - timestamp
updated_at      - timestamp
```

**Traits:**
```php
use HasFactory;
use Auditable;
```

**Relaciones:**
```php
// N:N con Productos (a través de ATRIBUTOS_PRODUCTO)
productos() - belongsToMany(Producto::class,
    'stock.ATRIBUTOS_PRODUCTO',
    'atributo_tipo_id',
    'producto_id'
)->withPivot('valor')->withTimestamps()
```

**Configuración:**
```php
protected $table = 'stock.ATRIBUTOS_TIPO';
protected $guarded = ['id'];
```

**Casts:**
```php
protected $casts = [
    'es_filtrable' => 'boolean',
    'es_requerido' => 'boolean',
    'activo' => 'boolean',
];
```

**Scopes:**
```php
scopeActivos($query)
scopeFiltrables($query)
scopeRequeridos($query)
scopeOrdenados($query)
```

---

### Producto (App\Models\Stock\Producto)

**Tabla:** `stock.PRODUCTOS`

**Campos:**
```php
id                  - bigint
codigo              - varchar(50) unique
nombre              - varchar(255)
descripcion         - text nullable
modelo              - varchar(100) nullable
marca_id            - bigint FK → MARCAS
categoria_id        - bigint FK → CATEGORIAS
unidad_medida_id    - bigint FK → UNIDADES_MEDIDA
stock_minimo        - decimal(12,2) default 0
activo              - boolean
creadoPor           - bigint nullable
actualizadoPor      - bigint nullable
created_at          - timestamp
updated_at          - timestamp
deleted_at          - timestamp nullable
```

**Traits:**
```php
use HasFactory;
use SoftDeletes;
use Auditable;
```

**Relaciones:**
```php
// N:1 con catálogos
marca()         - belongsTo(Marca::class)
categoria()     - belongsTo(Categoria::class)
unidadMedida()  - belongsTo(UnidadMedida::class)

// 1:N con tablas relacionadas
precios()       - hasMany(Precio::class)->orderBy('created_at', 'desc')
imagenes()      - hasMany(ImagenProducto::class)
stocks()        - hasMany(Stock::class)

// N:N con Atributos
atributos()     - belongsToMany(AtributoTipo::class,
    'stock.ATRIBUTOS_PRODUCTO',
    'producto_id',
    'atributo_tipo_id'
)->withPivot('valor')->withTimestamps()

// Relación con precio actual
precioActual()  - hasOne(Precio::class)->where('es_actual', true)
```

**Configuración:**
```php
protected $table = 'stock.PRODUCTOS';
protected $guarded = ['id'];
```

**Casts:**
```php
protected $casts = [
    'stock_minimo' => 'decimal:2',
    'activo' => 'boolean',
];
```

**Scopes:**
```php
scopeActivos($query)
scopeBuscar($query, $termino)
scopePorMarca($query, $marcaId)
scopePorCategoria($query, $categoriaId)
scopeConStockBajo($query)  // stock_actual < stock_minimo
```

**Accessors:**
```php
// Obtener nombre completo con marca
getNombreCompletoAttribute()  // "Motobomba Grundfos JP5"

// Obtener stock total en todos los depósitos
getStockTotalAttribute()
```

---

### Precio (App\Models\Stock\Precio)

**Tabla:** `stock.PRECIOS`

**Campos:**
```php
id                  - bigint
producto_id         - bigint FK → PRODUCTOS
precio_compra       - decimal(12,2)
precio_venta        - decimal(12,2)
margen_porcentaje   - decimal(5,2) nullable
iva                 - enum('0', '5', '10')
moneda              - varchar(3) default 'PYG'
es_actual           - boolean
creadoPor           - bigint nullable
created_at          - timestamp
updated_at          - timestamp
```

**Traits:**
```php
use HasFactory;
use Auditable;
```

**Relaciones:**
```php
// N:1 con Producto
producto() - belongsTo(Producto::class)
```

**Configuración:**
```php
protected $table = 'stock.PRECIOS';
protected $guarded = ['id'];
```

**Casts:**
```php
protected $casts = [
    'precio_compra' => 'decimal:2',
    'precio_venta' => 'decimal:2',
    'margen_porcentaje' => 'decimal:2',
    'es_actual' => 'boolean',
];
```

**Scopes:**
```php
scopeActuales($query)
scopePorMoneda($query, $moneda)
```

---

### Stock (App\Models\Stock\Stock)

**Tabla:** `stock.STOCK`

**Campos:**
```php
id                  - bigint
producto_id         - bigint FK → PRODUCTOS
deposito_id         - bigint FK → empresa.DEPOSITOS
stock_actual        - decimal(12,2)
stock_minimo        - decimal(12,2)
stock_maximo        - decimal(12,2) nullable
ubicacion           - varchar(100) nullable
pasillo             - varchar(50) nullable
estante             - varchar(50) nullable
lote                - varchar(100) nullable
fecha_vencimiento   - date nullable
creadoPor           - bigint nullable
actualizadoPor      - bigint nullable
created_at          - timestamp
updated_at          - timestamp
deleted_at          - timestamp nullable
```

**Traits:**
```php
use HasFactory;
use SoftDeletes;
use Auditable;
```

**Relaciones:**
```php
// N:1 con Producto (mismo schema)
producto() - belongsTo(Producto::class)

// N:1 con Deposito (cross-schema a empresa)
deposito() - belongsTo(\App\Models\Empresa\Deposito::class)

// 1:N con Movimientos de Stock
movimientos() - hasMany(MovimientoStock::class)
```

**Configuración:**
```php
protected $table = 'stock.STOCK';
protected $guarded = ['id'];
```

**Casts:**
```php
protected $casts = [
    'stock_actual' => 'decimal:2',
    'stock_minimo' => 'decimal:2',
    'stock_maximo' => 'decimal:2',
    'fecha_vencimiento' => 'date',
];
```

**Scopes:**
```php
scopePorProducto($query, $productoId)
scopePorDeposito($query, $depositoId)
scopeBajoMinimo($query)
scopeProximoVencer($query, $dias = 30)
```

**Unique Constraint:**
```php
// Un producto solo puede tener un registro de stock por depósito
unique(['producto_id', 'deposito_id'])
```

---

### MovimientoStock (App\Models\Stock\MovimientoStock)

**Tabla:** `stock.MOVIMIENTOS_STOCK`

**Campos:**
```php
id                  - bigint
stock_id            - bigint FK → STOCK
tipo_movimiento     - enum('entrada', 'salida', 'ajuste', 'transferencia')
cantidad            - decimal(12,2)
stock_anterior      - decimal(12,2)
stock_nuevo         - decimal(12,2)
motivo              - text nullable
documento_referencia - varchar(100) nullable
creadoPor           - bigint nullable
created_at          - timestamp
updated_at          - timestamp
```

**Traits:**
```php
use HasFactory;
use Auditable;
```

**Relaciones:**
```php
// N:1 con Stock
stock() - belongsTo(Stock::class)

// Relación indirecta con Producto
producto() - through('stock')->has('producto')
```

**Configuración:**
```php
protected $table = 'stock.MOVIMIENTOS_STOCK';
protected $guarded = ['id'];
```

**Casts:**
```php
protected $casts = [
    'cantidad' => 'decimal:2',
    'stock_anterior' => 'decimal:2',
    'stock_nuevo' => 'decimal:2',
];
```

**Scopes:**
```php
scopePorTipo($query, $tipo)
scopeEntradas($query)
scopeSalidas($query)
scopePorFecha($query, $desde, $hasta)
scopePorProducto($query, $productoId)
```

---

## Relaciones Cross-Schema

### ⚠️ Consideraciones Importantes

**Foreign Keys Cross-Schema:**
PostgreSQL no maneja bien las foreign keys entre schemas diferentes. Por esta razón:

1. **En schema `stock`:**
   ```php
   // ❌ NO crear FK constraint en migración
   $table->foreign('deposito_id')
       ->references('id')
       ->on('empresa.DEPOSITOS');  // Evitar

   // ✅ Solo definir en modelo Eloquent
   public function deposito()
   {
       return $this->belongsTo(\App\Models\Empresa\Deposito::class);
   }
   ```

2. **Campo `creadoPor` y `actualizadoPor`:**
   ```php
   // En schema empresa (OK - mismo schema que users vía public)
   'creadoPor' => User::first()->id

   // En schema stock (⚠️ SIEMPRE NULL)
   'creadoPor' => null
   ```

### Relaciones Actuales Cross-Schema

```
stock.STOCK → empresa.DEPOSITOS
   └─ deposito_id (sin FK constraint, solo relación Eloquent)
```

---

## Traits y Características Comunes

### SoftDeletes
**Modelos que lo usan:**
- User
- Empresa, Sucursal, Deposito
- Marca, UnidadMedida, Categoria, Producto, Stock

**Métodos disponibles:**
```php
// Eliminar (soft delete)
$modelo->delete();

// Restaurar
$modelo->restore();

// Forzar eliminación permanente
$modelo->forceDelete();

// Consultar solo no eliminados
Modelo::all();

// Incluir eliminados
Modelo::withTrashed()->get();

// Solo eliminados
Modelo::onlyTrashed()->get();
```

### Auditable (Owen-IT)
**Modelos que lo usan:** Todos excepto tablas pivot

**Métodos disponibles:**
```php
// Obtener auditorías
$modelo->audits;

// Última auditoría
$modelo->audits()->latest()->first();

// Auditorías por usuario
$modelo->audits()->where('user_id', $userId)->get();
```

**Eventos auditados:**
- `created`
- `updated`
- `deleted`
- `restored`

---

## Scopes Útiles

### Scopes Globales Comunes

**En todos los modelos:**
```php
// Filtrar activos
Modelo::activos()->get();

// Buscar por término
Modelo::buscar('texto')->get();
```

### Scopes Específicos

**Categorías:**
```php
// Solo raíces
Categoria::raices()->get();

// Por nivel
Categoria::nivel(1)->get();

// Descendientes de una categoría
Categoria::descendientes($categoriaId)->get();
```

**Stock:**
```php
// Stock bajo mínimo
Stock::bajoMinimo()->get();

// Próximo a vencer
Stock::proximoVencer(30)->get();  // 30 días
```

**Timbrados:**
```php
// Vigentes
Timbrado::vigentes()->get();

// Electrónicos
Timbrado::electronicos()->get();

// Por tipo
Timbrado::porTipoDocumento('factura')->get();
```

---

## Ejemplos de Uso

### Crear Producto con Precio y Atributos

```php
use App\Models\Stock\Producto;
use App\Models\Stock\Precio;
use DB;

DB::beginTransaction();
try {
    // Crear producto
    $producto = Producto::create([
        'codigo' => 'MOTO-001',
        'nombre' => 'Motobomba Grundfos JP5',
        'descripcion' => 'Motobomba periférica 0.5 HP',
        'marca_id' => 1,
        'categoria_id' => 5,
        'unidad_medida_id' => 1,
        'stock_minimo' => 2,
        'activo' => true,
        'creadoPor' => null,
    ]);

    // Crear precio
    Precio::create([
        'producto_id' => $producto->id,
        'precio_compra' => 850000,
        'precio_venta' => 1200000,
        'margen_porcentaje' => 41.18,
        'iva' => '10',
        'moneda' => 'PYG',
        'es_actual' => true,
        'creadoPor' => null,
    ]);

    // Agregar atributos
    $producto->atributos()->attach(1, ['valor' => '0.5 HP']);  // Potencia
    $producto->atributos()->attach(2, ['valor' => '220V']);     // Voltaje

    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

### Consultar Producto con Todas sus Relaciones

```php
$producto = Producto::with([
    'marca',
    'categoria',
    'unidadMedida',
    'precioActual',
    'atributos',
    'stocks.deposito.sucursal',
    'imagenes'
])->find(1);

// Acceder a datos
echo $producto->nombre;                          // "Motobomba Grundfos JP5"
echo $producto->marca->nombre;                   // "Grundfos"
echo $producto->precioActual->precio_venta;      // 1200000
echo $producto->categoria->nombre;               // "Motobombas Periféricas"
echo $producto->unidadMedida->simbolo;           // "UN"

// Stock en todos los depósitos
foreach ($producto->stocks as $stock) {
    echo $stock->deposito->nombre;               // "Depósito Casa Matriz"
    echo $stock->stock_actual;                   // 5
}

// Atributos
foreach ($producto->atributos as $atributo) {
    echo $atributo->nombre;                      // "Potencia"
    echo $atributo->pivot->valor;                // "0.5 HP"
}
```

### Movimiento de Stock

```php
use App\Models\Stock\Stock;
use App\Models\Stock\MovimientoStock;

DB::beginTransaction();
try {
    $stock = Stock::where('producto_id', 1)
                  ->where('deposito_id', 1)
                  ->lockForUpdate()
                  ->first();

    $cantidadMovimiento = 10;
    $stockAnterior = $stock->stock_actual;
    $stockNuevo = $stockAnterior + $cantidadMovimiento;

    // Actualizar stock
    $stock->update([
        'stock_actual' => $stockNuevo,
    ]);

    // Registrar movimiento
    MovimientoStock::create([
        'stock_id' => $stock->id,
        'tipo_movimiento' => 'entrada',
        'cantidad' => $cantidadMovimiento,
        'stock_anterior' => $stockAnterior,
        'stock_nuevo' => $stockNuevo,
        'motivo' => 'Compra a proveedor',
        'documento_referencia' => 'FC-001-0001234',
        'creadoPor' => null,
    ]);

    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

### Búsqueda de Productos

```php
// Búsqueda simple
$productos = Producto::buscar('motobomba')->get();

// Búsqueda con filtros
$productos = Producto::activos()
    ->porMarca(1)
    ->porCategoria(5)
    ->conStockBajo()
    ->with('marca', 'categoria', 'precioActual')
    ->paginate(20);

// Productos con stock en un depósito específico
$productos = Producto::whereHas('stocks', function($query) use ($depositoId) {
    $query->where('deposito_id', $depositoId)
          ->where('stock_actual', '>', 0);
})->get();
```

### Jerarquía de Categorías

```php
// Obtener árbol completo
$categorias = Categoria::raices()
    ->with('hijos.hijos')  // 3 niveles
    ->get();

// Renderizar árbol
foreach ($categorias as $nivel1) {
    echo $nivel1->nombre . "\n";

    foreach ($nivel1->hijos as $nivel2) {
        echo "  ├─ " . $nivel2->nombre . "\n";

        foreach ($nivel2->hijos as $nivel3) {
            echo "  │  └─ " . $nivel3->nombre . "\n";
        }
    }
}

// Obtener breadcrumbs de una categoría
function getCategoryBreadcrumb($categoria) {
    $breadcrumb = [$categoria->nombre];

    $parent = $categoria->parent;
    while ($parent) {
        array_unshift($breadcrumb, $parent->nombre);
        $parent = $parent->parent;
    }

    return implode(' > ', $breadcrumb);
}

// Resultado: "Motobombas > Periféricas > Residenciales 0.5 HP"
```

---

**Última actualización:** 2025-12-07
**Versión:** 1.0
**Autor:** Claude Code
