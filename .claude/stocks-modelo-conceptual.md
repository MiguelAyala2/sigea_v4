# MODELO CONCEPTUAL Y JERÁRQUICO COMPLETO - MÓDULO STOCKS
## Sistema Integrado de Gestión de Inventario para Ferretería Especializada en Aguatería

---

## ÍNDICE
1. [Filosofía del Módulo](#1-filosofía-del-módulo)
2. [Jerarquía Conceptual](#2-jerarquía-conceptual)
3. [Relaciones Cruciales con Módulo Empresa](#3-relaciones-cruciales-con-módulo-empresa)
4. [Jerarquía de Carpetas y Archivos](#4-jerarquía-de-carpetas-y-archivos)
5. [Flujos de Trabajo Principales](#5-flujos-de-trabajo-principales)
6. [Consideraciones Especiales para Paraguay](#6-consideraciones-especiales-para-paraguay)
7. [Preparación para Módulos Futuros](#7-preparación-para-módulos-futuros)
8. [Estrategia de Implementación](#8-estrategia-de-implementación)
9. [Beneficios de esta Estructura](#9-beneficios-de-esta-estructura)
10. [Checklist de Éxito](#10-checklist-de-éxito)

---

## 1. FILOSOFÍA DEL MÓDULO

### 1.1 Principios Fundamentales

- **Livewire como motor**: Toda la lógica de negocio en componentes reactivos
- **Eloquent como ORM**: Modelos robustos con relaciones, scopes y traits
- **Separación clara**: Controladores solo retornan vistas, Livewire maneja lógica
- **Auditoría completa**: Todos los modelos auditan cambios con Owen-IT
- **Soft Deletes**: Eliminación lógica en entidades principales
- **Patrón Container-Component**: Vistas contenedoras llaman a componentes Livewire

### 1.2 Integración con Sistema Existente

- **Schema independiente**: `stocks` separado de `empresa` pero relacionado
- **Relación clave**: Stocks vinculados a Depósitos del módulo Empresa
- **Preparado para expansión**: Diseñado para integrarse con módulos futuros (Compras, Ventas)
- **Configuración unificada**: Mismo AdminLTE, mismos estilos, mismos patrones

---

## 2. JERARQUÍA CONCEPTUAL

### 2.1 Nivel 0: Schema y Configuración

```
stocks (SCHEMA POSTGRESQL)
├── Configuración en search_path: 'stocks,empresa,public'
├── Relación con empresa.DEPOSITOS (foreign keys)
└── Preparado para empresa.PROVEEDORES (módulo Compras futuro)
```

### 2.2 Nivel 1: Catálogos Base (Independientes)

#### CATEGORIAS (Jerarquía árbol)
```
CATEGORIAS
├── Parent-Child relación recursiva
├── Código jerárquico automático (01.01.01)
├── Niveles: Grupo → Subgrupo → Especialización
└── Ejemplo: Motobombas → Sumergibles → 4 Pulgadas
```

**Características**:
- Tabla: `stocks.CATEGORIAS`
- Relación: `parent_id` recursiva (self-referencing)
- Campos: `codigo`, `nombre`, `descripcion`, `parent_id`, `nivel`, `orden`
- Scopes especiales: `raices()`, `hijosDirectos()`, `descendientes()`

#### MARCAS (Catálogo simple)
```
MARCAS
├── Marcas de productos (Grundfos, Pedrollo, etc.)
├── Con país de origen
└── Relación 1:N con Productos
```

**Características**:
- Tabla: `stocks.MARCAS`
- Campos: `nombre`, `pais_origen`, `descripcion`, `logo_path`
- Relación: `hasMany` → Productos

#### UNIDADES_MEDIDA (Catálogo simple)
```
UNIDADES_MEDIDA
├── Unidades básicas (Unidad, Metro, Litro, Kg)
├── Control de decimales (kg y L vs Unidad)
└── Símbolo para display (Un, m, L, kg)
```

**Características**:
- Tabla: `stocks.UNIDADES_MEDIDA`
- Campos: `nombre`, `simbolo`, `permite_decimales` (boolean)
- Relación: `hasMany` → Productos

### 2.3 Nivel 2: Entidad Central - PRODUCTOS

```
PRODUCTOS (Corazón del módulo)
├── Información básica: nombre, descripción, modelo
├── Clasificación: tipo (PRODUCTO/INSUMO/SERVICIO/KIT), origen (NACIONAL/IMPORTADO)
├── Relaciones:
│   ├── belongsTo CATEGORIA (jerarquía)
│   ├── belongsTo MARCA (opcional)
│   ├── belongsTo UNIDAD_MEDIDA (obligatorio)
│   ├── hasMany ATRIBUTOS (especificaciones)
│   ├── hasMany IMAGENES (galería)
│   ├── hasMany STOCKS (por depósito)
│   ├── hasMany MOVIMIENTOS_STOCK (kardex)
│   ├── hasMany PRECIOS (histórico)
│   └── belongsToMany PROVEEDORES (pivot con info específica)
└── Campos calculados: stock_total, necesita_reposicion
```

**Características**:
- Tabla: `stocks.PRODUCTOS`
- Códigos: `codigo_producto` (interno), `codigo_barras`, `codigo_fabricante`
- Tipos ENUM: `PRODUCTO`, `INSUMO`, `SERVICIO`, `KIT`
- Origen ENUM: `NACIONAL`, `IMPORTADO`
- Campos extensos: `descripcion` (text), `aplicacion` (text)
- Flags: `permite_venta`, `permite_compra`, `maneja_stock`

### 2.4 Nivel 3: Especificaciones Técnicas

#### ATRIBUTOS_TIPO (Catálogo de características)
```
ATRIBUTOS_TIPO
├── Tipos de atributos: Potencia, Diámetro, Caudal, Material, etc.
├── Configurables: unidad, orden, es_filtrable
└── Base para búsquedas avanzadas
```

**Características**:
- Tabla: `stocks.ATRIBUTOS_TIPO`
- Campos: `nombre`, `unidad`, `orden`, `es_filtrable`, `es_requerido`
- Por categoría: Atributos específicos según tipo de producto

#### ATRIBUTOS (Valores por producto)
```
ATRIBUTOS
├── Relación: PRODUCTO → ATRIBUTO_TIPO → VALOR
├── Ejemplo: Producto "Bomba 1HP" → Atributo "Potencia" → Valor "1.5 HP"
└── Permite filtros específicos: "Mostrar bombas de 1.5 HP"
```

**Características**:
- Tabla: `stocks.ATRIBUTOS`
- Foreign keys: `producto_id`, `atributo_tipo_id`
- Campo: `valor` (string flexible)

#### IMAGENES_PRODUCTO (Galería)
```
IMAGENES_PRODUCTO
├── Múltiples imágenes por producto
├── Una imagen principal destacada
└── Orden personalizable
```

**Características**:
- Tabla: `stocks.IMAGENES_PRODUCTO`
- Foreign key: `producto_id`
- Campos: `path`, `es_principal` (boolean), `orden`

### 2.5 Nivel 4: Gestión de Stock (Core del módulo)

#### STOCKS (Stock por depósito)
```
STOCKS
├── Relación: PRODUCTO + DEPOSITO (empresa) = STOCK
├── Campos: stock_actual, stock_minimo (reorden), stock_maximo
├── Trazabilidad: ubicacion, lote, fecha_vencimiento
├── Unique constraint: (producto_id, deposito_id)
└── Estados calculados: normal, bajo, agotado, alto
```

**Características**:
- Tabla: `stocks.STOCKS`
- Foreign keys: `producto_id`, `deposito_id` → `empresa.DEPOSITOS`
- Unique: `(producto_id, deposito_id)`
- Campos de control: `stock_actual`, `stock_minimo`, `stock_maximo`
- Campos de ubicación: `ubicacion`, `pasillo`, `estante`
- Lote y vencimiento: `lote`, `fecha_vencimiento`
- Accessor `estado`: retorna 'normal', 'bajo', 'agotado', 'alto'

#### MOVIMIENTOS_STOCK (Kardex completo)
```
MOVIMIENTOS_STOCK
├── Historial de TODOS los movimientos
├── Tipos: ENTRADA_COMPRA, SALIDA_VENTA, AJUSTE, TRANSFERENCIA
├── Información completa: antes/después, costos, usuario, documento
├── Vinculación con documentos: COMPRA_ID, VENTA_ID, NOTA_REMISION_ID
└── Propósito: Trazabilidad, auditoría, cálculos de costo
```

**Características**:
- Tabla: `stocks.MOVIMIENTOS_STOCK`
- Foreign keys: `producto_id`, `deposito_id`, `usuario_id`
- Tipos ENUM: `ENTRADA_COMPRA`, `SALIDA_VENTA`, `AJUSTE_POSITIVO`, `AJUSTE_NEGATIVO`, `TRANSFERENCIA_ORIGEN`, `TRANSFERENCIA_DESTINO`
- Campos de movimiento: `cantidad`, `stock_anterior`, `stock_posterior`
- Campos de costo: `costo_unitario`, `costo_total`
- Documento vinculado: `documento_tipo`, `documento_id`
- Motivo: `motivo` (para ajustes y transferencias)

### 2.6 Nivel 5: Gestión Comercial

#### PRECIOS (Histórico de precios)
```
PRECIOS
├── Precio compra y precio venta
├── Margen porcentual calculado
├── IVA configurable (10% general, 5% básicos, exentas, en Paraguay)
├── Moneda dual: PYG (predeterminado) o USD
├── Tipo de cambio para productos importados
└── Solo un precio actual por producto
```

**Características**:
- Tabla: `stocks.PRECIOS`
- Foreign key: `producto_id`
- Campos de precio: `precio_compra`, `precio_venta`, `margen_porcentaje`
- IVA ENUM: `10`, `5`, `exenta`
- Moneda ENUM: `PYG`, `USD`
- Campo: `tipo_cambio` (para USD)
- Flag: `es_actual` (boolean) - Solo un registro true por producto
- Unique: `(producto_id) WHERE es_actual = true`

#### PRODUCTO_PROVEEDOR (Tabla pivot N:M)
```
PRODUCTO_PROVEEDOR
├── Relación productos-proveedores con datos específicos
├── Campos: precio_referencia, tiempo_entrega, es_principal
├── Historial: ultimo_precio_compra, ultima_compra_fecha
└── Preparado para módulo Compras futuro
```

**Características**:
- Tabla: `stocks.PRODUCTO_PROVEEDOR`
- Composite key: `(producto_id, proveedor_id)`
- Foreign keys: `producto_id`, `proveedor_id` → `empresa.PROVEEDORES` (futuro)
- Campos: `codigo_proveedor`, `precio_referencia`, `tiempo_entrega_dias`
- Flags: `es_principal`, `activo`
- Historial: `ultimo_precio_compra`, `ultima_compra_fecha`

---

## 3. RELACIONES CRUCIALES CON MÓDULO EMPRESA

### 3.1 Depósitos como Ubicaciones Físicas

```
stocks.STOCKS.deposito_id → empresa.DEPOSITOS.id
```

- **Propósito**: Cada stock está físicamente en un depósito específico
- **Implicación**: Control de stock por sucursal (empresa.SUCURSALES → empresa.DEPOSITOS)
- **Cascade**: `onDelete('restrict')` - No permitir eliminar depósito con stock

### 3.2 Integración con Facturación

```
stocks.MOVIMIENTOS_STOCK.documento_id → empresa.TIMBRADOS.id
```

- **Cuando**: `documento_tipo = 'NOTA_REMISION'`
- **Propósito**: Trazabilidad de salidas de stock con documentos fiscales
- **Cascade**: `onDelete('set null')` - Mantener movimiento si se elimina timbrado

### 3.3 Proveedores Futuros

```
stocks.PRODUCTO_PROVEEDOR.proveedor_id → empresa.PROVEEDORES.id
```

- **Preparación**: Para módulo Compras futuro
- **Propósito**: Historial de compras y evaluación de proveedores
- **Estado**: Tabla creada pero módulo PROVEEDORES pendiente

---

## 4. JERARQUÍA DE CARPETAS Y ARCHIVOS

### 4.1 Estructura Livewire (Lógica de Negocio)

```
app/Livewire/Stocks/
├── Categorias/
│   ├── Index.php        # Listado con árbol, búsqueda, ordenamiento
│   ├── Create.php       # Form con selección de parent, validaciones
│   └── Edit.php         # Similar a create con datos precargados
│
├── Marcas/
│   ├── Index.php        # CRUD simple
│   ├── Create.php
│   └── Edit.php
│
├── UnidadesMedida/
│   ├── Index.php        # CRUD simple
│   ├── Create.php
│   └── Edit.php
│
├── Productos/
│   ├── Index.php        # Tabla avanzada con múltiples filtros
│   ├── Create.php       # Form multipaso (1.Info básica 2.Atributos 3.Stock/Precio)
│   ├── Edit.php         # Similar multipaso para edición
│   ├── Show.php         # Vista detalle con pestañas (Info, Stock, Movimientos, Imágenes)
│   └── Kardex.php       # Movimientos específicos de un producto
│
├── Stocks/
│   ├── Index.php        # Vista general de stock (grid por depósito, alertas)
│   ├── Ajuste.php       # Ajustes manuales (+/- stock con justificación)
│   ├── Transferencia.php # Movimiento entre depósitos (origen→destino)
│   └── Inventario.php   # Conteo físico, comparación con sistema
│
├── Reportes/
│   ├── StockBajo.php    # Productos bajo stock mínimo (alertas)
│   ├── Rotacion.php     # Análisis ABC de rotación
│   └── Valorizado.php   # Valor total de inventario por depósito
│
└── Configuracion/
    └── AtributosTipo.php # CRUD de tipos de atributos técnicos
```

### 4.2 Estructura de Modelos (Datos y Relaciones)

```
app/Models/Stocks/
├── Categoria.php          # Jerarquía árbol, scopes para búsqueda por nivel
├── Marca.php              # Simple con relaciones a productos
├── UnidadMedida.php       # Catálogo con control de decimales
├── Producto.php           # Entidad central con todas las relaciones
├── AtributoTipo.php       # Catálogo de características configurables
├── Atributo.php           # Valores específicos por producto
├── ImagenProducto.php     # Galería con imagen principal
├── Stock.php              # Stock por depósito con estados calculados
├── MovimientoStock.php    # Kardex con scopes por tipo/fecha
├── Precio.php             # Histórico con control de precio actual único
└── ProductoProveedor.php  # Pivot con datos específicos relación
```

### 4.3 Estructura de Vistas (Interfaz de Usuario)

#### 4.3.1 Vistas Contenedoras (AdminLTE Layout)

```
resources/views/stocks/
├── categorias/
│   ├── index.blade.php     # @livewire('stocks.categorias.index')
│   ├── create.blade.php    # @livewire('stocks.categorias.create')
│   └── edit.blade.php      # @livewire('stocks.categorias.edit')
│
├── marcas/                 # Similar estructura
├── unidades-medida/        # Similar estructura
│
├── productos/
│   ├── index.blade.php     # Página listado productos
│   ├── create.blade.php    # Página creación multipaso
│   ├── edit.blade.php      # Página edición multipaso
│   ├── show.blade.php      # Página detalle con pestañas
│   └── kardex.blade.php    # Página kardex específico
│
├── stocks/
│   ├── index.blade.php
│   ├── ajuste.blade.php
│   ├── transferencia.blade.php
│   └── inventario.blade.php
│
├── reportes/
│   ├── stock-bajo.blade.php
│   ├── rotacion.blade.php
│   └── valorizado.blade.php
│
└── configuracion/
    └── atributos-tipo.blade.php
```

#### 4.3.2 Vistas de Componentes Livewire (Reactivas)

```
resources/views/livewire/stocks/
├── categorias/
│   ├── index.blade.php     # Tabla con árbol colapsable
│   ├── create.blade.php    # Form con select de parent
│   └── edit.blade.php      # Form similar con datos
│
├── productos/
│   ├── index.blade.php     # Tabla avanzada con filtros en cabecera
│   ├── create.blade.php    # Multipaso con progress bar
│   ├── edit.blade.php      # Multipaso con datos cargados
│   ├── show.blade.php      # Pestañas: Info, Stock, Mov, Imgs
│   └── kardex.blade.php    # Tabla de movimientos con filtros
│
├── stocks/
│   ├── index.blade.php         # Grid tarjetas por depósito
│   ├── ajuste.blade.php        # Form con producto, depósito, cantidad
│   ├── transferencia.blade.php # Form con origen, destino, productos
│   └── inventario.blade.php    # Lista para conteo físico
│
└── reportes/                   # Tablas y gráficos
```

---

## 5. FLUJOS DE TRABAJO PRINCIPALES

### 5.1 Alta de Nuevo Producto (Multipaso)

```
PASO 1: Información Básica
├── Selección categoría (jerárquica)
├── Selección marca (opcional)
├── Selección unidad medida
├── Códigos: producto, barras, fabricante
├── Nombre, descripción, modelo
├── Tipo: PRODUCTO/INSUMO/SERVICIO/KIT
├── Origen: NACIONAL/IMPORTADO
└── Aplicación específica

PASO 2: Especificaciones Técnicas
├── Selección de atributos tipo disponibles
├── Asignación de valores a cada atributo
├── Ordenamiento de atributos
└── Configuración de filtros

PASO 3: Stock y Precios
├── Asignación stock inicial por depósito
├── Definición de stock mínimo/máximo por depósito
├── Ubicación física (estante, fila)
├── Precio de compra
├── Margen deseado
├── Cálculo automático precio venta
├── Configuración IVA (10% o 5%)
└── Moneda (PYG o USD)
```

### 5.2 Movimiento de Stock (Kardex)

#### ENTRADA por Compra (futuro módulo)
```
1. Seleccionar producto y depósito destino
2. Ingresar cantidad y costo unitario
3. Sistema calcula stock nuevo
4. Registra movimiento tipo ENTRADA_COMPRA
5. Actualiza stock_actual en tabla STOCKS
6. Actualiza ultimo_precio_compra en PROVEEDOR
```

#### SALIDA por Venta
```
1. Seleccionar producto y depósito origen
2. Verificar stock disponible
3. Ingresar cantidad y precio unitario
4. Sistema calcula stock nuevo
5. Registra movimiento tipo SALIDA_VENTA
6. Puede vincularse a NOTA_REMISION (timbrado)
```

#### AJUSTE Manual
```
1. Motivo: conteo físico, deterioro, donación
2. Seleccionar producto y depósito
3. Ingresar diferencia (+/-)
4. Sistema registra movimiento tipo AJUSTE
5. Actualiza stock_actual
```

#### TRANSFERENCIA entre Depósitos
```
1. Seleccionar depósito origen y destino
2. Seleccionar productos y cantidades
3. Sistema registra 2 movimientos:
   - SALIDA en origen (tipo TRANSFERENCIA_ORIGEN)
   - ENTRADA en destino (tipo TRANSFERENCIA_DESTINO)
4. Actualiza ambos stocks
```

### 5.3 Control y Alertas

#### Monitoreo Automático
```
1. Stock Bajo: stock_actual ≤ stock_minimo
   → Alerta visual en dashboard
   → Reporte diario de productos a reponer

2. Vencimientos Próximos: fecha_vencimiento ≤ 30 días
   → Alerta para venta prioritaria
   → Reporte semanal

3. Stock Cero por > 30 días
   → Sugerencia de desactivación producto
   → Análisis de rotación
```

#### Reportes Programados
- **Diario**: Stock bajo, movimientos del día
- **Semanal**: Rotación, vencimientos
- **Mensual**: Valorizado, análisis ABC

---

## 6. CONSIDERACIONES ESPECIALES PARA PARAGUAY

### 6.1 Aspectos Fiscales

- **IVA Dual**: 10% para la mayoría, 5% para productos de canasta básica
- **Moneda Dual**: Precios en Gs, compras en USD para importados
- **Timbrados**: Integración con numeración fiscal para salidas
- **Facturación Electrónica**: Preparado para SET

### 6.2 Características del Mercado

- **Estacionalidad**: Mayor demanda en verano (sistemas riego, piscinas)
- **Importación**: Largos plazos (30-60 días), considerar en stock mínimo
- **Competencia**: Marcas brasileñas/argentinas vs chinas
- **Logística**: Desafíos distribución al interior

### 6.3 Especificaciones Técnicas Relevantes

#### Motobombas
```
- Potencia: HP (no KW como en otros países)
- Voltaje: 220V monofásico (residencial), 380V trifásico (industrial)
- Material: Resistencia a agua dura (alto contenido mineral)
```

#### Tanques/Cisternas
```
- Capacidad: Litros (no galones)
- Material: Polietileno para agua potable
- Certificación: Requerida para contacto con agua
```

#### Cañerías
```
- Medidas: Pulgadas (1/2", 3/4", 1", etc.)
- Material: PVC para presión, CPVC para agua caliente
- Normativas: Requisitos específicos Paraguay
```

#### Válvulas/Accesorios
```
- Rosca: Métrica o pulgada según origen
- Presión: Considerar presión municipal variable
```

### 6.4 Prácticas Comerciales Locales

- **Ventas a Crédito**: Común (30, 60, 90 días)
- **Garantías Extendidas**: 1-2 años para motobombas
- **Servicio Técnico**: Componente crítico de venta
- **Contraestación**: Menor movimiento junio-agosto

---

## 7. PREPARACIÓN PARA MÓDULOS FUTUROS

### 7.1 Integración con Compras

#### Puntos de Conexión
```
1. MOVIMIENTOS_STOCK.tipo_movimiento = 'ENTRADA_COMPRA'
2. MOVIMIENTOS_STOCK.documento_id = ORDEN_COMPRA.id
3. PRODUCTO_PROVEEDOR.ultimo_precio_compra actualizado
4. STOCKS.stock_actual incrementado automáticamente
```

#### Flujo Previsto
```
Orden Compra → Recepción Mercadería →
  1. Registro movimiento entrada
  2. Actualización stock
  3. Actualización precio proveedor
  4. Generación cuenta por pagar
```

### 7.2 Integración con Ventas

#### Puntos de Conexión
```
1. MOVIMIENTOS_STOCK.tipo_movimiento = 'SALIDA_VENTA'
2. MOVIMIENTOS_STOCK.documento_id = VENTA.id o NOTA_REMISION.id
3. Validación stock disponible antes de vender
4. STOCKS.stock_actual decrementado automáticamente
```

#### Flujo Previsto
```
Venta → Validación Stock → Generación Documento →
  1. Registro movimiento salida
  2. Actualización stock
  3. Vinculación con timbrado (nota remisión)
```

### 7.3 Integración con Facturación Electrónica

#### Puntos de Conexión
```
1. MOVIMIENTOS_STOCK.documento_tipo = 'NOTA_REMISION'
2. MOVIMIENTOS_STOCK.documento_id = TIMBRADO.id
3. Trazabilidad completa: producto → movimiento → documento fiscal
```

#### Requisitos
- Numeración secuencial por punto expedición
- Control de timbrados vigentes
- Generación CDC (Código de Control)

---

## 8. ESTRATEGIA DE IMPLEMENTACIÓN

### 8.1 Fase 1: Fundamentos

```
1. Configuración: search_path, menú AdminLTE, rutas básicas
2. Migraciones: Tablas catálogos (categorías, marcas, unidades)
3. Modelos Base: Con relaciones básicas y scopes
4. Livewire Simple: CRUD catálogos (categorías, marcas)
5. Vistas Container: Layout AdminLTE básico
```

### 8.2 Fase 2: Núcleo Productos

```
1. Migraciones: productos, atributos, imágenes
2. Modelo Producto: Con todas las relaciones
3. Livewire Productos: Index (filtros), Create (multipaso), Show (pestañas)
4. Vistas Complejas: Tabla avanzada, formulario multipaso
5. Subida imágenes: Integración con Storage
```

### 8.3 Fase 3: Gestión Stock

```
1. Migraciones: stocks, movimientos_stock, precios
2. Modelos Stock: Con estados calculados y scopes
3. Livewire Stocks: Index (grid), Ajuste, Transferencia
4. Kardex: Movimientos con filtros por producto/depósito
5. Validaciones: Stock disponible, fechas vencimiento
```

### 8.4 Fase 4: Reportes y Pulido

```
1. Livewire Reportes: Stock bajo, rotación, valorizado
2. Configuración: Atributos tipo, permisos
3. Testing: Casos complejos, integración módulo empresa
4. Optimización: Eager loading, índices, caché
5. Documentación: Manual de usuario, flujos operativos
```

### 8.5 Fase 5: Preparación Expansión

```
1. Integración: Pruebas con timbrados (notas remisión)
2. Preparación: Estructura para módulo compras
3. Permisos: Roles específicos para stocks
4. Backup/Restore: Procedimientos operativos
5. Capacitación: Usuarios finales
```

---

## 9. BENEFICIOS DE ESTA ESTRUCTURA

### 9.1 Para el Desarrollo

- **Modularidad**: Cada componente tiene responsabilidad única
- **Mantenibilidad**: Código organizado por funcionalidad
- **Escalabilidad**: Fácil agregar nuevas características
- **Reutilización**: Componentes Livewire independientes
- **Testing**: Modelos y componentes testables por separado

### 9.2 Para la Operación

- **Control Total**: Stock por ubicación exacta
- **Trazabilidad**: Historial completo de cada producto
- **Alertas Proactivas**: Stock bajo, vencimientos, inactivos
- **Análisis**: Reportes para decisiones informadas
- **Integración**: Listo para compras, ventas, facturación

### 9.3 Para el Negocio

- **Reducción Pérdidas**: Control estricto de inventario
- **Optimización Capital**: Stock mínimo necesario
- **Mejor Servicio**: Productos disponibles cuando se necesitan
- **Decisiones Data-Driven**: Reportes de rotación, rentabilidad
- **Cumplimiento**: Auditoría completa, preparado fiscalmente

---

## 10. CHECKLIST DE ÉXITO

### 10.1 Técnico

- [ ] Schema `stocks` creado y configurado en search_path
- [ ] 12 migraciones ejecutadas correctamente
- [ ] 11 modelos con relaciones y scopes implementados
- [ ] 15+ componentes Livewire funcionando
- [ ] 30+ vistas Blade con AdminLTE integrado
- [ ] Rutas agrupadas y protegidas
- [ ] Permisos configurados en seeder
- [ ] Menú AdminLTE actualizado

### 10.2 Funcional

- [ ] CRUD completo de categorías (jerárquico)
- [ ] CRUD completo de marcas y unidades
- [ ] Alta de productos multipaso funcional
- [ ] Búsqueda avanzada con múltiples filtros
- [ ] Gestión de stock por depósito operativa
- [ ] Movimientos de stock registrando kardex
- [ ] Precios históricos con control de actual
- [ ] Reportes generando información útil
- [ ] Integración con depósitos módulo empresa

### 10.3 Operativo

- [ ] Usuarios pueden dar alta productos completos
- [ ] Stock se actualiza correctamente en movimientos
- [ ] Alertas de stock bajo funcionando
- [ ] Reportes son comprensibles y útiles
- [ ] Sistema responde en tiempos aceptables
- [ ] Data real puede cargarse y procesarse
- [ ] Backup y restore probados

---

## RESUMEN EJECUTIVO

Este modelo conceptual proporciona:

- ✅ Visión completa del módulo Stocks desde arquitectura hasta operación
- ✅ Jerarquía clara de entidades y sus relaciones
- ✅ Estructura detallada de archivos y carpetas
- ✅ Flujos de trabajo principales para cada funcionalidad
- ✅ Consideraciones específicas para el mercado paraguayo
- ✅ Preparación explícita para módulos futuros (Compras, Ventas)
- ✅ Ruta de implementación faseada y realista

El módulo está diseñado para ser:

- **Robusto**: Manejo completo de stock con trazabilidad
- **Escalable**: Fácil expansión a otras funcionalidades
- **Integrado**: Conexión natural con módulos existentes y futuros
- **Práctico**: Resuelve necesidades reales de ferretería con aguatería
- **Paraguayo**: Considera particularidades locales fiscales y comerciales

---

**Fecha de documentación**: Diciembre 2025
**Versión del sistema**: SIGEA v4
**Framework**: Laravel 12.26.4 / PHP 8.4.15 / PostgreSQL
**Módulo relacionado**: Empresa (configuración base)
