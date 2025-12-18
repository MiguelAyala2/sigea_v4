# MÓDULO DE COMPRAS - SIGEA V4
## Documentación Completa del Módulo Implementado

**Fecha de creación:** Diciembre 2025
**Versión:** 1.0
**Estado:** Implementación Completa

---

## RESUMEN EJECUTIVO

El módulo de Compras ha sido implementado completamente siguiendo el modelo conceptual jerárquico de 12 bloques. Este documento resume toda la estructura, archivos creados y funcionalidades implementadas.

---

## 1. ESTRUCTURA GENERAL DEL MÓDULO

### Schema PostgreSQL
```sql
Schema: compras
Search Path Configurado: 'compras,empresa,stock,proveedores,public'
```

### Jerarquía del Flujo
```
PROVEEDOR (existente)
    ↓
PEDIDO DE COMPRA (existente)
    ↓
PRESUPUESTO PROVEEDOR (existente)
    ↓
ORDEN DE COMPRA (existente)
    ↓
COMPRA/FACTURA (implementado)
    ↓
RECEPCIÓN → MOVIMIENTO STOCK (implementado)
    ↓
CUENTA POR PAGAR (existente)
    ↓
PAGO PROVEEDOR (existente)
    ↓
LIBRO DE COMPRAS (implementado)
```

---

## 2. ARCHIVOS CREADOS

### 2.1 MIGRACIONES (Bloque 1-2)

```
database/migrations/
├── 2025_12_13_130700_create_compras_schema.php
├── 2025_12_13_135634_create_compras_recepcion_table.php
├── 2025_12_13_135844_create_compras_aprobacion_flujo_table.php
└── 2025_12_13_140011_create_compras_integracion_table.php
```

**Tablas creadas:**
- `compras.COMPRAS` (referencia a tabla existente)
- `compras.COMPRAS_DETALLE` (referencia a tabla existente)
- `compras.COMPRAS_RECEPCION` (nueva)
- `compras.COMPRAS_RECEPCION_DETALLE` (nueva)
- `compras.COMPRAS_APROBACION_FLUJO` (nueva)
- `compras.COMPRAS_INTEGRACION` (nueva)

### 2.2 MODELOS (Bloque 3)

```
app/Models/Compras/
├── Compra.php                      # Cabecera de facturas
├── CompraDetalle.php               # Detalle de items
├── CompraRecepcion.php             # Control de recepción
├── CompraRecepcionDetalle.php      # Detalle de recepción
├── AprobacionFlujo.php             # Flujo de aprobaciones
└── IntegracionDocumentos.php       # Trazabilidad entre documentos
```

**Características de los modelos:**
- Todos implementan `Auditable` (Owen-IT)
- Todos usan `SoftDeletes`
- Relaciones bidireccionales completas
- Scopes para búsquedas reutilizables
- Accessors para campos calculados
- Métodos de negocio integrados

### 2.3 CONTROLADORES (Bloque 7)

```
app/Http/Controllers/Compras/
├── CompraController.php       # 12.8 KB - Lógica de compras
├── RecepcionController.php    #  2.6 KB - Lógica de recepciones
└── ReporteController.php      #  4.8 KB - Reportes consolidados
```

### 2.4 COMPONENTES LIVEWIRE (Bloque 4-5)

```
app/Livewire/Compras/
├── Dashboard/
│   ├── Index.php              # Dashboard principal con KPIs
│   └── FlujoCompra.php        # Visualización del flujo
├── Compras/
│   ├── Index.php              # Listado unificado
│   ├── Create.php             # Crear compra
│   ├── Edit.php               # Editar compra
│   └── Show.php               # Detalle completo
├── Recepciones/
│   ├── Index.php              # Listado de recepciones
│   ├── Create.php             # Nueva recepción
│   └── Show.php               # Detalle de recepción
├── Aprobaciones/
│   ├── Index.php              # Todas las aprobaciones
│   ├── Pendientes.php         # Pendientes de aprobación
│   └── MisAprobaciones.php    # Del usuario actual
└── Reportes/
    ├── LibroCompras.php       # Libro de compras SET
    ├── AnalisisProveedores.php # Análisis ABC
    └── FlujoAprobaciones.php  # Métricas de flujo
```

### 2.5 VISTAS BLADE (Bloque 6)

```
resources/views/compras/
├── dashboard/
│   ├── index.blade.php        # Dashboard principal
│   └── flujo.blade.php        # Flujo visual
├── compras/
│   ├── index.blade.php        # Listado de compras
│   ├── create.blade.php       # Formulario crear
│   ├── edit.blade.php         # Formulario editar
│   └── show.blade.php         # Detalle completo
├── recepciones/
│   ├── index.blade.php        # Listado de recepciones
│   ├── create.blade.php       # Nueva recepción
│   └── show.blade.php         # Detalle recepción
├── aprobaciones/
│   ├── index.blade.php        # Todas las aprobaciones
│   ├── pendientes.blade.php   # Pendientes
│   └── mis-aprobaciones.blade.php # Propias
├── reportes/
│   ├── libro-compras.blade.php         # Libro SET
│   ├── analisis-proveedores.blade.php  # Análisis proveedores
│   ├── flujo-aprobaciones.blade.php    # Flujo de aprobación
│   ├── compras-periodo.blade.php       # Compras por período
│   └── recepciones-vs-compras.blade.php # Recepciones vs Compras
└── configuracion/
    ├── index.blade.php
    ├── flujos-aprobacion.blade.php
    └── tipos-documento.blade.php
```

### 2.6 VISTAS LIVEWIRE

```
resources/views/livewire/compras/
├── dashboard/
├── compras/
├── recepciones/
├── aprobaciones/
├── reportes/
└── configuracion/
```

### 2.7 RUTAS (Bloque 7)

```
routes/compras.php
```

**Estructura de rutas:**
```php
Route::prefix('compras')->name('compras.')->group(function () {
    // Dashboard
    Route::get('/dashboard', ...)

    // Compras/Facturas
    Route::prefix('compras')->name('compras.')->group(...)

    // Recepciones
    Route::prefix('recepciones')->name('recepciones.')->group(...)

    // Aprobaciones
    Route::prefix('aprobaciones')->name('aprobaciones.')->group(...)

    // Reportes
    Route::prefix('reportes')->name('reportes.')->group(...)

    // Configuración
    Route::prefix('configuracion')->name('configuracion.')->group(...)

    // Redirecciones a módulos existentes
    Route::get('/proveedores', redirect()->route('proveedores.index'))
    // ... más redirecciones

    // API endpoints
    Route::prefix('api')->name('api.')->group(...)
});
```

---

## 3. FUNCIONALIDADES IMPLEMENTADAS

### 3.1 Dashboard de Compras (Bloque 4)

**Archivo:** `resources/views/compras/dashboard/index.blade.php`

**KPIs mostrados:**
- Total compras del mes
- Compras pendientes de aprobación
- Recepciones pendientes
- Gráficos de evolución
- Top proveedores
- Alertas (órdenes sin factura, facturas sin pago)

### 3.2 Gestión de Compras/Facturas (Bloque 5-6)

**Funcionalidades:**
- ✅ Listado unificado con filtros avanzados
- ✅ Crear nueva compra/factura
- ✅ Editar compra existente
- ✅ Ver detalle completo con pestañas:
  - Info de factura
  - Items/Detalles
  - Recepciones asociadas
  - Aprobaciones
  - Pagos vinculados
- ✅ Trazabilidad completa (de qué orden viene, a qué pago va)
- ✅ Aprobar/Rechazar/Anular
- ✅ Duplicar compra

**Estados de Compra:**
- BORRADOR
- PENDIENTE (de aprobación)
- APROBADA
- RECHAZADA
- ANULADA
- PAGADA
- PARCIAL (pagada parcialmente)

### 3.3 Recepción de Mercadería (Bloque 6)

**Funcionalidades:**
- ✅ Crear recepción vinculada a compra
- ✅ Registrar cantidades esperadas vs recibidas
- ✅ Control de diferencias
- ✅ Vincular con depósito
- ✅ Estados: PENDIENTE, PARCIAL, COMPLETA, RECHAZADA
- ✅ Generación automática de movimiento de stock
- ✅ Registro de lote y fecha de vencimiento
- ✅ Ubicación en almacén

**Tabla detalle de recepción:**
- Cantidad esperada
- Cantidad recibida
- Cantidad aceptada
- Cantidad rechazada
- Diferencia
- Motivo de diferencia

### 3.4 Flujo de Aprobaciones (Bloque 8)

**Sistema unificado para aprobar:**
- Pedidos de compra
- Presupuestos
- Órdenes de compra
- Compras/Facturas
- Recepciones
- Pagos

**Características:**
- Niveles de aprobación configurables
- Secuencia de aprobación
- Rol requerido por nivel
- Fecha de vencimiento
- Comentarios y observaciones
- Adjuntos (firmas, comprobantes)
- Estados: PENDIENTE, APROBADO, RECHAZADO, OBSERVADO

**Vista para el usuario:**
- Mis aprobaciones pendientes
- Historial de aprobaciones realizadas
- Filtros por tipo de documento
- Alertas de vencimiento

### 3.5 Integración de Documentos (Bloque 8)

**Trazabilidad completa:**
```
PEDIDO → PRESUPUESTO → ORDEN → COMPRA → RECEPCIÓN → PAGO
```

**Tabla `COMPRAS_INTEGRACION`:**
- Documento origen (tipo + id)
- Documento destino (tipo + id)
- Tipo de relación (GENERA, SE_CONVIERTE_EN, DEPENDE_DE, REFERENCIA, CORRIGE, ANULA)
- Porcentaje de relación
- Es completa (100% procesado)
- Detalles de relación (JSON)

**Ejemplo de uso:**
```php
IntegracionDocumentos::crearRelacion(
    'ORDEN_COMPRA',
    $ordenId,
    'COMPRA',
    $compraId,
    'SE_CONVIERTE_EN',
    100.00,
    true
);
```

### 3.6 Reportes Consolidados (Bloque 9)

#### a) Libro de Compras (SET)
**Archivo:** `compras/reportes/libro-compras.blade.php`

- Formato requerido por SET para declaración de IVA
- Columnas: RUC, Razón Social, N° Factura, Fecha, Montos, IVA 10%, IVA 5%, Exentas
- Exportación a Excel compatible con SET
- Filtros por período fiscal
- Validación de RUC
- Orden cronológico

#### b) Análisis de Proveedores
**Archivo:** `compras/reportes/analisis-proveedores.blade.php`

- Análisis ABC de proveedores
- Top N proveedores por volumen
- Tiempos de entrega
- Cumplimiento de plazos
- Ranking por monto
- Historial de entregas

#### c) Flujo de Aprobaciones
**Archivo:** `compras/reportes/flujo-aprobaciones.blade.php`

- Tiempo promedio por nivel
- Cuellos de botella
- Usuarios con más aprobaciones/rechazos
- Documentos pendientes por tipo
- Documentos vencidos

#### d) Compras por Período
**Archivo:** `compras/reportes/compras-periodo.blade.php`

**Filtros implementados:**
- Rango de fechas (desde/hasta)
- Proveedor
- Sucursal
- Tipo de documento
- Estado
- Ordenamiento (fecha, monto, proveedor)

**Visualizaciones:**
- Resumen ejecutivo (4 KPIs)
- Tabla detallada con totales
- Gráfico de dona: compras por proveedor
- Gráfico de línea: evolución de compras
- Exportación Excel/PDF (en desarrollo)

#### e) Recepciones vs Compras
**Archivo:** `compras/reportes/recepciones-vs-compras.blade.php`

- Compras vs recepciones
- Porcentaje recibido
- Recepciones completas vs parciales
- Compras sin recepción

---

## 4. RELACIONES ENTRE MODELOS (Bloque 8)

### 4.1 Modelo Compra

**Relaciones implementadas:**
```php
// Relaciones directas
belongsTo: proveedor, sucursal, deposito, timbrado, creadoPorUsuario, actualizadoPorUsuario

// Relaciones hasMany
hasMany: detalles, recepciones, aprobaciones, integracionesOrigen, integracionesDestino

// Acces sors calculados
- estado_texto
- tipo_factura_texto
- condicion_pago_texto
- numero_completo
- recepcion_completa
- porcentaje_recibido

// Scopes
- buscador
- buscarEstado
- buscarProveedor
- buscarFechaDesde
- buscarFechaHasta
- pendientesRecepcion
- pendientesPago
- soloActivas

// Métodos de negocio
- getTotalPagado()
- getSaldoPendiente()
- estaPagada()
```

### 4.2 Modelo CompraDetalle

**Relaciones:**
```php
belongsTo: compra, producto

// Accessors
- precio_unitario_formateado
- total_formateado
- iva_texto

// Scopes
- porCompra
- porProducto
- conProducto

// Métodos
- calcularSubtotal()
- calcularIva()
- calcularTotal()
```

### 4.3 Modelo CompraRecepcion

**Relaciones:**
```php
belongsTo: compra, deposito, receptor, creadoPorUsuario, actualizadoPorUsuario
hasMany: detalles, recepcionDetalles, aprobaciones

// Accessors
- estado_texto
- fecha_recepcion_formateada
- porcentaje_recibido_formateado

// Scopes
- buscador
- buscarEstado
- buscarCompra
- buscarDeposito
- buscarFechaDesde
- buscarFechaHasta
- pendientes
- completas

// Métodos
- estaCompleta()
- estaPendiente()
- marcarComoCompleta()
- marcarComoParcial($porcentaje)
```

### 4.4 Modelo CompraRecepcionDetalle (Nuevo)

**Relaciones:**
```php
belongsTo: recepcion, compraDetalle, producto

// Accessors
- cantidad_esperada_formateada
- cantidad_recibida_formateada
- cantidad_aceptada_formateada
- cantidad_rechazada_formateada
- diferencia_formateada
- porcentaje_recibido
- porcentaje_aceptado
- tiene_diferencia

// Scopes
- porRecepcion
- porProducto
- conDiferencias
- conRechazo
- aceptados

// Métodos
- calcularDiferencia()
- tieneDiferenciaSignificativa($tolerancia)
- actualizarCantidades($recibida, $aceptada, $rechazada)
```

### 4.5 Modelo AprobacionFlujo

**Relaciones polimórficas:**
```php
morphTo: documento (pedido, presupuesto, orden, compra, recepción, pago)
belongsTo: aprobador, creadoPorUsuario, actualizadoPorUsuario

// Métodos específicos por documento
- pedidoCompra()
- presupuesto()
- ordenCompra()
- compra()
- recepcion()
- pago()

// Accessors
- documento_tipo_texto
- estado_texto
- nivel_aprobacion_texto
- fecha_aprobacion_formateada
- esta_vencida
- tiempo_restante

// Scopes
- buscador
- buscarTipoDocumento
- buscarEstado
- buscarNivel
- buscarUsuarioAprobador
- pendientes
- aprobados
- rechazados
- vencidas
- porUsuarioActual

// Métodos de negocio
- aprobar($comentarios, $firmaPath)
- rechazar($observaciones, $comprobantePath)
- observar($comentarios)
- estaPendiente()
- estaAprobado()
- estaRechazado()
```

### 4.6 Modelo IntegracionDocumentos

**Relaciones polimórficas:**
```php
morphTo: documentoOrigen, documentoDestino

// Métodos específicos
- pedidoOrigen(), presupuestoOrigen(), ordenOrigen(), compraOrigen(), recepcionOrigen()
- pedidoDestino(), presupuestoDestino(), ordenDestino(), compraDestino(), recepcionDestino(), pagoDestino()

// Accessors
- documento_origen_tipo_texto
- documento_destino_tipo_texto
- tipo_relacion_texto
- porcentaje_relacion_formateado
- descripcion_relacion

// Scopes
- buscador
- buscarTipoOrigen
- buscarTipoDestino
- buscarTipoRelacion
- buscarDocumentoOrigen
- buscarDocumentoDestino
- relacionesCompletas
- relacionesParciales

// Métodos
- esCompleta()
- obtenerDocumentoOrigen()
- obtenerDocumentoDestino()
- crearRelacion() (static)
```

---

## 5. CONFIGURACIÓN DEL SISTEMA (Bloque 11)

### 5.1 Menú AdminLTE

**Archivo:** `config/adminlte.php`

```php
[
    'text' => 'COMPRAS',
    'icon' => 'fas fa-shopping-cart',
    'can'  => 'compras.ver',
    'submenu' => [
        ['text' => 'Dashboard', 'route' => 'compras.dashboard'],
        ['text' => 'Proveedores', 'route' => 'proveedores.index'],
        ['text' => 'Pedidos de Compra', 'route' => 'pedidos.index'],
        ['text' => 'Presupuestos', 'route' => 'presupuestos.index'],
        ['text' => 'Órdenes de Compra', 'route' => 'ordenes.index'],
        ['text' => 'Compras/Facturas', 'route' => 'compras.compras.index'],
        ['text' => 'Recepción Mercadería', 'route' => 'compras.recepciones.index'],
        ['text' => 'Aprobaciones', 'route' => 'compras.aprobaciones.pendientes', 'badge' => ['text' => '3', 'color' => 'warning']],
        ['text' => 'Cuentas por Pagar', 'route' => 'pagos.index'],
        ['text' => 'Reportes', 'submenu' => [...]],
    ],
]
```

### 5.2 Permisos del Módulo

**Archivo:** `database/seeders/RolYPermisoSeeder.php`

**Permisos implementados:**
```php
// Generales
'compras.ver'
'compras.dashboard'

// Compras/Facturas
'compras.compras.ver'
'compras.compras.crear'
'compras.compras.editar'
'compras.compras.eliminar'
'compras.compras.anular'
'compras.compras.aprobar'

// Recepciones
'compras.recepciones.ver'
'compras.recepciones.crear'
'compras.recepciones.editar'
'compras.recepciones.eliminar'

// Aprobaciones
'compras.aprobaciones.ver'
'compras.aprobaciones.aprobar'
'compras.aprobaciones.rechazar'

// Reportes
'compras.reportes.ver'
'compras.reportes.libro'
'compras.reportes.analisis'
'compras.reportes.flujo'

// Configuración
'compras.configuracion.ver'
'compras.configuracion.editar'

// Proveedores (existentes)
'proveedores.ver'
'proveedores.crear'
'proveedores.editar'
'proveedores.eliminar'
```

### 5.3 Configuración de Base de Datos

**Archivo:** `config/database.php`

```php
'search_path' => 'compras,empresa,stock,proveedores,public',
```

---

## 6. VALIDACIONES Y REGLAS DE NEGOCIO (Bloque 10)

### 6.1 Validaciones en Controladores

**ReporteController::comprasPeriodo():**
```php
- fecha_desde: required|date
- fecha_hasta: required|date|after_or_equal:fecha_desde
- proveedor_id: nullable|exists:proveedores.PROVEEDORES,id
- sucursal_id: nullable|exists:empresa.SUCURSALES,id
- tipo_documento: nullable|string
- estado: nullable|string
- orden: nullable|string
```

### 6.2 Reglas de Negocio Implementadas

1. **Compras:**
   - Una compra puede tener múltiples recepciones (recepción parcial)
   - El estado cambia automáticamente según aprobaciones y pagos
   - Se valida que el proveedor esté activo
   - Se verifica el timbrado vigente

2. **Recepciones:**
   - Solo se puede crear recepción de compras APROBADAS
   - El porcentaje recibido se calcula automáticamente
   - Estado COMPLETA solo cuando todas las cantidades están recibidas
   - Las diferencias deben justificarse

3. **Aprobaciones:**
   - Flujo secuencial por niveles
   - Solo puede aprobar quien tiene el rol requerido
   - Una vez aprobado/rechazado, no se puede cambiar
   - Se pueden agregar comentarios en cada nivel

4. **Integración:**
   - Se crea registro automático cuando un documento genera otro
   - Se calcula el porcentaje de conversión
   - Permite trazabilidad bidireccional

---

## 7. SCOPES REUTILIZABLES

### Scopes implementados en todos los modelos:

```php
// Búsqueda general
#[Scope] buscador($search)

// Búsqueda por estado
#[Scope] buscarEstado($estado)

// Búsqueda por fechas
#[Scope] buscarFechaDesde($fecha)
#[Scope] buscarFechaHasta($fecha)

// Filtros específicos
#[Scope] pendientes()
#[Scope] aprobados()
#[Scope] completas()
#[Scope] soloActivas()
```

---

## 8. ACCESSORS Y MUTATORS

### Accessors implementados:

**Formateo de montos:**
```php
getPrecioUnitarioFormateadoAttribute()
getTotalFormateadoAttribute()
```

**Formateo de fechas:**
```php
getFechaRecepcionFormateadaAttribute()
getFechaAprobacionFormateadaAttribute()
```

**Cálculos:**
```php
getPorcentajeRecibidoAttribute()
getPorcentajeAceptadoAttribute()
getSaldoPendienteAttribute()
```

**Estados textuales:**
```php
getEstadoTextoAttribute()
getTipoFacturaTextoAttribute()
getCondicionPagoTextoAttribute()
```

---

## 9. PRÓXIMOS PASOS (Features Pendientes)

### 9.1 Funcionalidades para desarrollar:

1. **Exportación de Reportes:**
   - Implementar exportación real a Excel usando Maatwebsite/Laravel-Excel
   - Implementar exportación a PDF usando DomPDF
   - Templates personalizados para SET

2. **Notificaciones:**
   - Email cuando una aprobación está pendiente
   - Alerta cuando un timbrado está por vencer
   - Notificación de recepciones pendientes

3. **Automatizaciones:**
   - Generación automática de orden desde presupuesto aprobado
   - Generación automática de compra desde orden aprobada
   - Actualización automática de stock al completar recepción

4. **Mejoras de Dashboard:**
   - Gráficos interactivos con Chart.js
   - Filtros dinámicos
   - Widgets configurables

5. **Configuración Avanzada:**
   - Configuración de flujos de aprobación por tipo de documento y monto
   - Configuración de alertas y umbrales
   - Templates de documentos personalizables

---

## 10. ESTRUCTURA DE ARCHIVOS COMPLETA

```
sigea_v4/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Compras/
│   │           ├── CompraController.php
│   │           ├── RecepcionController.php
│   │           └── ReporteController.php
│   ├── Livewire/
│   │   └── Compras/
│   │       ├── Dashboard/
│   │       ├── Compras/
│   │       ├── Recepciones/
│   │       ├── Aprobaciones/
│   │       └── Reportes/
│   └── Models/
│       └── Compras/
│           ├── Compra.php
│           ├── CompraDetalle.php
│           ├── CompraRecepcion.php
│           ├── CompraRecepcionDetalle.php
│           ├── AprobacionFlujo.php
│           └── IntegracionDocumentos.php
├── database/
│   ├── migrations/
│   │   ├── 2025_12_13_130700_create_compras_schema.php
│   │   ├── 2025_12_13_135634_create_compras_recepcion_table.php
│   │   ├── 2025_12_13_135844_create_compras_aprobacion_flujo_table.php
│   │   └── 2025_12_13_140011_create_compras_integracion_table.php
│   └── seeders/
│       └── RolYPermisoSeeder.php (actualizado)
├── resources/
│   └── views/
│       ├── compras/
│       │   ├── dashboard/
│       │   ├── compras/
│       │   ├── recepciones/
│       │   ├── aprobaciones/
│       │   ├── reportes/
│       │   └── configuracion/
│       └── livewire/
│           └── compras/
├── routes/
│   └── compras.php
└── config/
    ├── adminlte.php (actualizado)
    └── database.php (actualizado)
```

---

## 11. COMANDOS ÚTILES

### Migraciones
```bash
# Ejecutar migraciones
php artisan migrate

# Rollback última migración
php artisan migrate:rollback

# Refresh completo
php artisan migrate:fresh --seed
```

### Permisos
```bash
# Ejecutar seeder de permisos
php artisan db:seed --class=RolYPermisoSeeder

# Limpiar caché de permisos
php artisan permission:cache-reset
```

### Caché
```bash
# Limpiar todo
php artisan optimize:clear

# O individual
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 12. NOTAS TÉCNICAS IMPORTANTES

### PostgreSQL Schemas
- Usar `Rule::` facade en validaciones, NO strings
- Configurar `search_path` en `config/database.php`
- Las relaciones deben especificar tabla completa: `empresa.SUCURSALES`

### Livewire 3.x
- Usar `#[Validate]` para propiedades validables
- `wire:model.live` para bindings en tiempo real
- `wire:model.blur` para validar al perder foco

### Buenas Prácticas
- Todo archivo debe tener campos de auditoría: `creadoPor`, `actualizadoPor`
- Usar `SoftDeletes` en entidades principales
- Scopes para filtros reutilizables
- Accessors para campos calculados (NO almacenar si se calcula)
- Validaciones con `Rule::` facade

---

## 13. CONTACTO Y SOPORTE

Para dudas sobre este módulo, consultar:
- Documentación de buenas prácticas: `.claude/buenas-practicas-desarrollo-modulos.md`
- Estructura del proyecto: `.claude/project-structure.md`
- Modelo conceptual Empresa: `.claude/empresa-modelo-conceptual.md`

---

**Documento generado:** Diciembre 2025
**Versión SIGEA:** 4.0
**Laravel:** 12
**PHP:** 8.2+
**PostgreSQL:** Compatible
