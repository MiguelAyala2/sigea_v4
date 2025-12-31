# SIGEA v4 - Documentación de Módulos

Sistema Integrado de Gestión Empresarial para Aguatería

## Información del Sistema

- **Versión**: 4.0
- **Framework**: Laravel 12.26.4
- **PHP**: 8.4.16
- **Base de Datos**: PostgreSQL
- **UI Framework**: AdminLTE 3.x con Bootstrap 4.6.2
- **Frontend Framework**: Livewire 3.x

## Esquema de Base de Datos

El sistema utiliza PostgreSQL con esquemas organizados por módulo:
- `empresa` - Datos de la empresa
- `stock` - Gestión de inventario
- `compras` - Gestión de compras
- `ventas` - Gestión de ventas
- `servicios` - Gestión de servicios técnicos

Todas las tablas utilizan **UPPERCASE** para nombres y soft deletes.

---

## Módulos Implementados

### 1. Dashboard (Home)
**URL**: `/home`
**Componente**: `App\Livewire\Dashboard\Home`
**Vista**: `resources/views/home.blade.php`

#### Características:
- **Estadísticas en tiempo real**:
  - Ventas del día y del mes
  - Cuentas por cobrar (con saldo pendiente)
  - Compras del mes
  - Cuentas por pagar pendientes
  - Servicios por estado (pendiente, en proceso, finalizados)
  - Solicitudes de servicio pendientes
  - Productos con stock bajo

- **Widgets informativos**:
  - Últimas 5 ventas con detalle
  - Top 5 productos más vendidos (últimos 30 días)
  - Resumen financiero del mes (ingresos, egresos, balance)
  - Sistema de alertas y notificaciones

- **Accesos rápidos**:
  - Nueva Venta
  - Nueva Compra
  - Nueva Solicitud de Servicio
  - Inventario
  - Registrar Cobro
  - Historial de Servicios

#### Modelos Utilizados:
- `Factura` (ventas.FACTURAS)
- `CuentaPorCobrar` (ventas.CUENTAS_POR_COBRAR)
- `Compra` (compras.compras)
- `CuentaPorPagar` (compras.cuentas_por_pagar)
- `OrdenServicio` (servicios.ORDENES_SERVICIO)
- `SolicitudServicio` (servicios.SOLICITUDES_SERVICIO)
- `Producto` (stock.PRODUCTOS)
- `Stock` (stock.STOCK)

#### Queries Importantes:
```php
// Productos con stock bajo
DB::table('stock.STOCK as s')
    ->join('stock.PRODUCTOS as p', 's.producto_id', '=', 'p.id')
    ->whereRaw('s.stock_actual <= s.stock_minimo')
    ->where('p.activo', true)
    ->whereNull('p.deleted_at')
    ->whereNull('s.deleted_at')
    ->distinct('p.id')
    ->count();
```

---

### 2. Módulo de Ventas

#### 2.1 Facturas
**URL**: `/ventas/facturas`
**Rutas**:
- `ventas.facturas.index` - Listado
- `ventas.facturas.crear` - Crear nueva factura
- `ventas.facturas.show` - Ver detalle
- `ventas.facturas.edit` - Editar
- `ventas.facturas.pdf` - Imprimir PDF
- `ventas.facturas.anular` - Anular factura
- `ventas.facturas.emitir` - Emitir factura electrónica
- `ventas.facturas.set` - Enviar a SET (SIFEN)

#### 2.2 Cuentas por Cobrar
**URL**: `/ventas/cuentas-cobrar`
**Rutas**:
- `ventas.cuentas-cobrar.index` - Listado de cuentas
- `ventas.cuentas-cobrar.cobrar` - Registrar cobro
- `ventas.cuentas-cobrar.pago` - Procesar pago

**Características**:
- Gestión de saldo pendiente
- Estado de cuentas (pendiente, parcial, pagada)
- Historial de pagos

#### 2.3 Cobranzas
**URL**: `/ventas/cobranzas/registrar`
**Ruta**: `ventas.cobranzas.registrar`

**Componente**: `App\Livewire\Ventas\Cobranzas\RegistrarCobranza`

**Características**:
- Búsqueda de cliente
- Selección de facturas pendientes
- Registro de múltiples pagos
- Formas de cobro
- Generación de recibo

#### 2.4 Libro de Ventas
**URL**: `/ventas/libro-ventas`
**Componente**: `App\Livewire\Ventas\LibroVentas`

**Características**:
- Filtros por fecha y estado
- Totales por IVA (10%, 5%, exenta)
- Exportación a Excel y PDF
- Cumplimiento tributario

#### 2.5 Pedidos de Clientes
**URL**: `/ventas/pedidos`

**Características**:
- Gestión de pedidos
- Estados (pendiente, aprobado, facturado)
- Conversión a factura

---

### 3. Módulo de Compras

#### 3.1 Compras
**URL**: `/compras/compras`
**Rutas**:
- `compras.compras.index` - Listado
- `compras.compras.create` - Crear nueva compra
- `compras.compras.show` - Ver detalle
- `compras.compras.edit` - Editar
- `compras.compras.aprobar` - Aprobar compra
- `compras.compras.anular` - Anular
- `compras.compras.imprimir-pdf` - Imprimir

**Características**:
- Registro de compras a proveedores
- Control de timbrado y factura
- Actualización automática de stock
- Generación de cuentas por pagar

#### 3.2 Cuentas por Pagar
**URL**: `/compras/cuentas-pagar`
**Rutas**:
- `compras.cuentas-pagar.index` - Listado
- `compras.cuentas-pagar.show` - Ver detalle

**Características**:
- Gestión de saldo pendiente
- Control de vencimientos
- Historial de pagos a proveedores

#### 3.3 Notas de Crédito/Débito
**Características**:
- Ajustes de compras
- Devoluciones
- Correcciones de valores

---

### 4. Módulo de Servicios

#### 4.1 Solicitudes de Servicio
**URL**: `/servicios/solicitudes`
**Rutas**:
- `servicios.solicitudes.index` - Listado
- `servicios.solicitudes.create` - Nueva solicitud
- `servicios.solicitudes.edit` - Editar

**Características**:
- Registro de solicitudes de clientes
- Estados (pendiente, aprobada, rechazada)
- Asignación de técnico

#### 4.2 Órdenes de Servicio
**URL**: `/servicios/ordenes`
**Rutas**:
- `servicios.ordenes.index` - Listado
- `servicios.ordenes.edit` - Editar orden
- `servicios.ordenes.imprimir-orden` - Imprimir orden de trabajo
- `servicios.ordenes.imprimir-contrato` - Imprimir contrato

**Características**:
- Gestión de órdenes de trabajo
- Estados (pendiente, en proceso, finalizada, entregada, cancelada)
- Progreso de trabajo (0-100%)
- Generación de PDF de orden y contrato
- Asignación de técnico
- Control de tiempos

#### 4.3 Entrega/Cierre de Servicios
**URL**: `/servicios/entrega`
**Ruta**: `servicios.entrega.index`
**Componente**: `App\Livewire\Servicios\Entrega\Index`

**Características**:
- Listado de servicios finalizados
- Estadísticas de entregas
- Modal para registrar entrega
- Campos:
  - Fecha de entrega
  - Recibido por (nombre)
  - Documento del receptor (opcional)
  - Observaciones de entrega
- Cambio de estado a "entregada"
- Registro de quien recibe

#### 4.4 Historial de Servicios por Cliente
**URL**: `/servicios/clientes/historial`
**Ruta**: `servicios.clientes.historial`
**Componente**: `App\Livewire\Servicios\Clientes\HistorialServicios`

**Características**:
- Búsqueda de cliente (nombre, documento, teléfono)
- Información del cliente seleccionado
- Historial completo de servicios
- Filtros por estado y fecha
- Estadísticas por cliente:
  - Total de servicios
  - Servicios pendientes
  - Servicios finalizados
  - Total facturado

---

### 5. Módulo de Stock

#### 5.1 Productos
**URL**: `/stock/productos`
**Rutas**:
- `stock.productos.index` - Listado
- `stock.productos.create` - Crear producto
- `stock.productos.show` - Ver detalle
- `stock.productos.edit` - Editar
- `stock.productos.kardex` - Ver kardex
- `stock.productos.exportar-excel` - Exportar a Excel
- `stock.productos.exportar-pdf` - Exportar a PDF

**Características**:
- Gestión completa de productos
- Código automático (PRD-XXXXXX)
- Atributos personalizables
- Imágenes de productos
- Control de precios
- Relación con proveedores

#### 5.2 Stock por Depósito
**URL**: `/stock/stock`
**Rutas**:
- `stock.stock.index` - Listado
- `stock.stock.ajuste` - Ajustes de stock
- `stock.stock.transferencia` - Transferencias entre depósitos
- `stock.stock.inventario` - Toma de inventario

**Tabla**: `stock.STOCK`
**Campos importantes**:
- `producto_id`
- `deposito_id`
- `stock_actual`
- `stock_minimo`
- `stock_maximo`
- `ubicacion`, `pasillo`, `estante`
- `lote`, `fecha_vencimiento`

#### 5.3 Reportes de Stock
**URL**: `/stock/reportes`
**Rutas**:
- `stock.reportes.stock-bajo` - Productos con stock bajo
- `stock.reportes.valorizado` - Stock valorizado
- `stock.reportes.rotacion` - Rotación de productos

#### 5.4 Categorías y Marcas
**URLs**:
- `/stock/categorias` - Gestión de categorías
- `/stock/marcas` - Gestión de marcas
- `/stock/unidades-medida` - Unidades de medida
- `/stock/atributos-tipo` - Tipos de atributos

---

### 6. Módulo de Empresa

#### 6.1 Datos de Empresa
**Características**:
- Información fiscal
- Sucursales
- Depósitos
- Timbrados
- Puntos de expedición

#### 6.2 Usuarios y Roles
**Características**:
- Gestión de usuarios
- Roles y permisos
- Auditoría de cambios (usando `OwenIt\Auditing`)

---

## Características Técnicas del Sistema

### Livewire Components
El sistema utiliza Livewire 3.x para componentes reactivos:

```php
// Ejemplo de componente
namespace App\Livewire\Ventas;

use Livewire\Component;
use Livewire\WithPagination;

class Facturas extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        return view('livewire.ventas.facturas', [
            'facturas' => Factura::where('numero_factura', 'like', '%' . $this->search . '%')
                ->paginate(15)
        ]);
    }
}
```

### Modelos Eloquent
Todos los modelos implementan:
- Soft Deletes
- Auditoría (cuando aplicable)
- Relaciones definidas
- Casts de tipos
- Scopes útiles

```php
// Ejemplo de modelo
class Factura extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'ventas.FACTURAS';

    protected $fillable = [
        'numero_factura',
        'fecha_emision',
        'cliente_id',
        'total',
        // ...
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'total' => 'decimal:2',
        'es_electronica' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
```

### Convenciones de Nombres

#### Tablas
- Formato: `UPPERCASE`
- Esquema: `schema.TABLA`
- Ejemplo: `ventas.FACTURAS`, `stock.PRODUCTOS`

#### Rutas
- Formato: `modulo.recurso.accion`
- Ejemplos:
  - `ventas.facturas.index`
  - `stock.productos.crear`
  - `servicios.ordenes.imprimir-orden`

#### Componentes Livewire
- Namespace: `App\Livewire\Modulo\Componente`
- Ejemplo: `App\Livewire\Ventas\Facturas\Index`

#### Vistas
- Ubicación: `resources/views/livewire/modulo/componente.blade.php`
- Ejemplo: `resources/views/livewire/ventas/facturas/index.blade.php`

### Soft Deletes
Todas las tablas principales usan soft deletes:

```php
// En queries
->whereNull('deleted_at')

// En relaciones
public function producto()
{
    return $this->belongsTo(Producto::class)->withTrashed();
}
```

### Auditoría
Sistema de auditoría usando `owen-it/laravel-auditing`:

```php
// Ver historial de cambios
$factura->audits;

// Campos auditados automáticamente
- old_values
- new_values
- user_id
- event (created, updated, deleted)
```

---

## Integraciones

### SIFEN (Sistema Integrado de Facturación Electrónica Nacional)
- Emisión de facturas electrónicas
- Generación de CDC (Código de Control)
- Envío al SET
- QR para validación

### Reportes PDF
Uso de DomPDF para generación de:
- Facturas
- Órdenes de trabajo
- Contratos de servicio
- Reportes de stock
- Libros contables

### Excel Export
Exportación de datos usando PhpSpreadsheet:
- Libro de ventas
- Libro de compras
- Stock valorizado
- Listados de productos

---

## Seguridad

### Autenticación
- Laravel Breeze
- Middleware `auth`
- Protección de rutas

### Autorización
- Gates y Policies
- Middleware personalizado
- Roles y permisos

### Validación
- Form Requests
- Validación en tiempo real con Livewire
- Reglas personalizadas

---

## Variables de Entorno Importantes

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sigea_v4
DB_SCHEMA=public

# SIFEN
SIFEN_ENABLED=true
SIFEN_URL=https://sifen.set.gov.py
SIFEN_RUC=
SIFEN_CERTIFICATE=
```

---

## Comandos Útiles

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Migraciones
php artisan migrate
php artisan migrate:refresh --seed

# Livewire
php artisan livewire:make Modulo/Componente

# Rutas
php artisan route:list
php artisan route:list | grep ventas
```

---

## Estructura de Directorios

```
sigea_v4/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   ├── Livewire/
│   │   ├── Dashboard/
│   │   ├── Ventas/
│   │   ├── Compras/
│   │   ├── Servicios/
│   │   └── Stock/
│   ├── Models/
│   │   ├── Empresa/
│   │   ├── Ventas/
│   │   ├── Compras/
│   │   ├── Servicios/
│   │   └── Stock/
├── resources/
│   └── views/
│       ├── livewire/
│       ├── ventas/
│       ├── compras/
│       ├── servicios/
│       └── stock/
├── routes/
│   ├── web.php
│   ├── ventas.php
│   ├── compras.php
│   ├── servicios.php
│   └── stock.php
└── database/
    └── migrations/
```

---

## Notas de Desarrollo

### Colores del Sistema
- **Primario**: `#005cbf` (Azul)
- **Secundario**: `#6c757d` (Gris)
- **Fondos**: `#ffffff`, `#f8f9fa`

### AdminLTE Components
```blade
<x-adminlte-card title="Título" theme="primary" icon="fas fa-icon">
    Contenido
</x-adminlte-card>

<x-adminlte-input name="campo" label="Etiqueta" placeholder="..." />

<x-adminlte-select name="select" label="Seleccione">
    <option value="">Seleccione</option>
</x-adminlte-select>
```

### Iconos FontAwesome
El sistema usa FontAwesome 5 para iconos:
- `fas fa-shopping-cart` - Ventas
- `fas fa-boxes` - Stock
- `fas fa-tools` - Servicios
- `fas fa-chart-line` - Reportes

---

## Próximas Implementaciones

- [ ] Módulo de Reportes Avanzados
- [ ] Integración con WhatsApp para notificaciones
- [ ] App móvil para técnicos
- [ ] Dashboard con gráficos interactivos
- [ ] Sistema de respaldos automáticos
- [ ] API REST para integraciones externas

---

**Última actualización**: 2025-12-31
**Versión de documentación**: 1.0
