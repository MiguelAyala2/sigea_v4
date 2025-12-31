# MODELO CONCEPTUAL Y JERÁRQUICO COMPLETO - MÓDULO VENTAS
## Sistema Integrado de Gestión de Ventas para Ferretería Especializada en Aguatería

---

## ÍNDICE
1. [Filosofía del Módulo](#1-filosofía-del-módulo)
2. [Jerarquía Conceptual](#2-jerarquía-conceptual)
3. [Relaciones Cruciales con Otros Módulos](#3-relaciones-cruciales-con-otros-módulos)
4. [Jerarquía de Carpetas y Archivos](#4-jerarquía-de-carpetas-y-archivos)
5. [Flujos de Trabajo Principales](#5-flujos-de-trabajo-principales)
6. [Consideraciones Especiales para Paraguay](#6-consideraciones-especiales-para-paraguay)
7. [Integración con Módulos Existentes](#7-integración-con-módulos-existentes)
8. [Modelo Lógico de Datos](#8-modelo-lógico-de-datos)
9. [Estrategia de Implementación](#9-estrategia-de-implementación)
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

- **Schema independiente**: `ventas` separado de `empresa`, `stock`, `servicios` pero relacionado
- **Relación clave**: Ventas vinculadas a Stock, Clientes, Timbrados y Cajas
- **Preparado para expansión**: Diseñado para integrarse con facturación electrónica SET
- **Configuración unificada**: Mismo AdminLTE, mismos estilos, mismos patrones

---

## 2. JERARQUÍA CONCEPTUAL

### 2.1 Nivel 0: Schema y Configuración

```
ventas (SCHEMA POSTGRESQL)
├── Configuración en search_path: 'ventas,stock,servicios,empresa,public'
├── Relación con stock.PRODUCTOS (foreign keys)
├── Relación con servicios.CLIENTES (foreign keys)
├── Relación con empresa.TIMBRADOS (foreign keys)
├── Relación con empresa.PUNTOS_EXPEDICION (foreign keys)
├── Relación con empresa.SUCURSALES (foreign keys)
└── Relación con empresa.DEPOSITOS (foreign keys)
```

### 2.2 Nivel 1: Proceso Pre-Venta

#### PEDIDOS_CLIENTES (Solicitudes de compra)
```
PEDIDOS_CLIENTES
├── Origen del flujo de ventas
├── Estados: BORRADOR → PENDIENTE → CONFIRMADO → EN_PREPARACION → LISTO_ENTREGAR
├── → PARCIALMENTE_ENTREGADO → COMPLETAMENTE_ENTREGADO → FACTURADO
├── Tipos de entrega: RETIRO_LOCAL, DELIVERY, ENVIO_TRANSPORTE
├── Condiciones de pago: CONTADO, 7_DIAS, 15_DIAS, 30_DIAS, 60_DIAS, 90_DIAS
└── Genera base para facturación
```

**Características**:
- Tabla: `ventas.PEDIDOS_CLIENTES`
- Número automático: `PC-000001`, `PC-000002`...
- Relaciones: `cliente_id`, `sucursal_id`, `deposito_id`, `vendedor_id`
- Totales calculados: `subtotal`, `iva_10`, `iva_5`, `exenta`, `total_iva`, `total`
- Control de entrega: `porcentaje_entregado`
- Auditoría: `creado_por`, `confirmado_por`, `facturado_por`

#### PEDIDOS_CLIENTES_DETALLE (Líneas del pedido)
```
PEDIDOS_CLIENTES_DETALLE
├── Productos solicitados por el cliente
├── Cantidades: solicitada, entregada, pendiente
├── Precios y descuentos por línea
├── Estados: PENDIENTE, PARCIALMENTE_ENTREGADO, COMPLETAMENTE_ENTREGADO, CANCELADO
└── Base para detalles de factura
```

**Características**:
- Tabla: `ventas.PEDIDOS_CLIENTES_DETALLE`
- Foreign keys: `pedido_cliente_id`, `producto_id`, `cotizacion_detalle_id`
- Control: `cantidad_solicitada`, `cantidad_entregada`, `cantidad_pendiente`
- Precios: `precio_unitario`, `descuento_porcentaje`, `descuento_monto`
- Cálculos: `subtotal`, `iva_porcentaje`, `iva_monto`, `total`

### 2.3 Nivel 2: Facturación (Core del módulo)

#### FACTURAS (Documento fiscal)
```
FACTURAS
├── Documento fiscal principal
├── Origen: desde Pedido Cliente, Orden de Servicio, o Venta Directa
├── Condiciones: CONTADO o CREDITO (con cuotas)
├── Estados: BORRADOR → EMITIDA → PARCIALMENTE_PAGADA → PAGADA / VENCIDA / ANULADA
├── Integración SET: es_electronica, CDC, XML firmado, estado_set
├── Tipos de venta:
│   ├── PEDIDOS (productos desde pedido cliente)
│   └── SERVICIOS (servicios + repuestos desde orden de servicio)
└── Genera movimientos de caja y cuentas por cobrar
```

**Características**:
- Tabla: `ventas.FACTURAS`
- Numeración por timbrado: `001-001-0000001`
- Foreign keys: `cliente_id`, `pedido_cliente_id`, `timbrado_id`, `punto_expedicion_id`
- Totales: `subtotal`, `iva_10`, `iva_5`, `exenta`, `total_iva`, `descuento_global`, `flete`, `total`
- Facturación electrónica: `es_electronica`, `cdc`, `qr_data`, `xml_firmado`, `estado_set`
- Auditoría completa: `creado_por`, `emitido_por`, `anulado_por`
- Constraint único: `(numero_factura, timbrado_id)`

#### FACTURAS_DETALLE (Líneas de productos)
```
FACTURAS_DETALLE
├── Productos facturados
├── Vinculación con pedido_detalle_id (si viene de pedido)
├── Precios incluyen IVA
├── Genera movimientos de stock (salida)
└── Cálculos: cantidad × precio - descuento + IVA
```

**Características**:
- Tabla: `ventas.FACTURAS_DETALLE`
- Foreign keys: `factura_id`, `producto_id`, `pedido_detalle_id`
- Cálculos automáticos en modelo: `subtotal`, `iva_monto`, `total`
- Observer: Al guardar genera movimiento de stock (SALIDA_VENTA)

#### FACTURAS_SERVICIOS (Líneas de servicios)
```
FACTURAS_SERVICIOS
├── Servicios facturados (no productos)
├── Origen: Orden de Servicio
├── No genera movimiento de stock
└── Cálculos: cantidad × precio_unitario + IVA
```

**Características**:
- Tabla: `ventas.FACTURAS_SERVICIOS`
- Foreign key: `factura_id`
- Campos: `codigo`, `descripcion`, `cantidad`, `precio_unitario`
- Cálculos: `subtotal`, `iva_porcentaje`, `iva_monto`, `total`

#### FACTURAS_FORMAS_PAGO (Medios de pago)
```
FACTURAS_FORMAS_PAGO
├── Formas de pago utilizadas (solo CONTADO)
├── Tipos: EFECTIVO, TARJETA_DEBITO, TARJETA_CREDITO, TRANSFERENCIA, CHEQUE, GIROS
├── Genera movimientos de caja
└── Validación: suma debe igualar total factura
```

**Características**:
- Tabla: `ventas.FACTURAS_FORMAS_PAGO`
- Foreign key: `factura_id`
- Campos: `forma_pago` (ENUM), `monto`, `referencia`
- Observer: Al guardar genera movimiento de caja (INGRESO_VENTA)

#### FACTURAS_CUOTAS (Plan de pagos a crédito)
```
FACTURAS_CUOTAS
├── Solo para facturas a CREDITO
├── Máximo 6 cuotas
├── Primera cuota vence el mismo día, resto cada 30 días
├── Estados: PENDIENTE, PARCIALMENTE_PAGADA, PAGADA, VENCIDA
└── Control de pagos: monto_pagado, saldo_pendiente
```

**Características**:
- Tabla: `ventas.FACTURAS_CUOTAS`
- Foreign key: `factura_id`
- Campos: `numero_cuota`, `monto`, `fecha_vencimiento`
- Control: `monto_pagado`, `saldo_pendiente`, `estado`
- Cálculo automático al crear factura a crédito

### 2.4 Nivel 3: Gestión de Cobranzas

#### CUENTAS_POR_COBRAR (Cuentas a cobrar)
```
CUENTAS_POR_COBRAR
├── Generada automáticamente al emitir factura a CREDITO
├── Una cuenta por cobrar por factura (unique constraint)
├── Estados: PENDIENTE → PARCIALMENTE_PAGADA → PAGADA / VENCIDA / ANULADA
├── Control de vencimientos
└── Genera alertas de cobranza
```

**Características**:
- Tabla: `ventas.CUENTAS_POR_COBRAR`
- Foreign keys: `factura_id` (unique), `cliente_id`
- Campos: `numero_factura`, `fecha_emision`, `fecha_vencimiento`
- Control: `monto_total`, `monto_pagado`, `saldo_pendiente`, `estado`
- Observer: Se crea automáticamente al emitir factura a crédito

#### PAGOS_CUENTAS_POR_COBRAR (Pagos recibidos)
```
PAGOS_CUENTAS_POR_COBRAR
├── Registro de pagos de clientes
├── Múltiples formas de pago por pago
├── Actualiza saldo de cuenta por cobrar
├── Genera movimientos de caja
└── Actualiza estado de cuotas
```

**Características**:
- Tabla: `ventas.PAGOS_CUENTAS_POR_COBRAR`
- Foreign keys: `cuenta_por_cobrar_id`, `factura_cuota_id`, `recibido_por`
- Campos: `fecha_pago`, `monto_pagado`, `forma_pago`, `referencia`
- Observer: Actualiza cuenta por cobrar y genera movimiento de caja

### 2.5 Nivel 4: Logística y Entregas

#### REMISIONES (Notas de remisión)
```
REMISIONES
├── Documento de entrega de mercadería
├── Origen: Factura o Pedido Cliente
├── Estados: BORRADOR → EMITIDA → EN_TRANSITO → ENTREGADA → ANULADA
├── Control de entrega: receptor_nombre, receptor_ci, fecha_recepcion
└── Genera movimientos de stock (si no vino de factura)
```

**Características**:
- Tabla: `ventas.REMISIONES`
- Número automático: `REM-000001`
- Foreign keys: `cliente_id`, `factura_id`, `pedido_cliente_id`, `deposito_id`
- Campos: `fecha_emision`, `fecha_entrega`, `direccion_entrega`
- Control de recepción: `receptor_nombre`, `receptor_ci`, `fecha_recepcion`
- Auditoría: `creado_por`, `emitido_por`, `anulado_por`

#### REMISIONES_DETALLE (Productos remitidos)
```
REMISIONES_DETALLE
├── Productos entregados
├── Vinculación con factura_detalle_id (si aplica)
├── Cantidades y precios de referencia
└── Puede generar movimiento de stock
```

**Características**:
- Tabla: `ventas.REMISIONES_DETALLE`
- Foreign keys: `remision_id`, `producto_id`, `factura_detalle_id`
- Campos: `producto_descripcion`, `cantidad`, `unidad_medida`
- Referencia: `precio_unitario`, `subtotal`

### 2.6 Nivel 5: Gestión de Caja

#### APERTURAS_CAJA (Apertura de caja)
```
APERTURAS_CAJA
├── Apertura diaria por punto de expedición
├── Obligatorio para facturar
├── Una apertura activa por usuario por día
├── Estados: ABIERTA → CERRADA / CANCELADA
└── Control: saldo_inicial
```

**Características**:
- Tabla: `ventas.APERTURAS_CAJA`
- Foreign keys: `punto_expedicion_id`, `usuario_id`
- Campos: `fecha_apertura`, `hora_apertura`, `saldo_inicial`, `estado`
- Constraint: Solo una apertura ABIERTA por usuario

#### MOVIMIENTOS_CAJA (Movimientos de caja)
```
MOVIMIENTOS_CAJA
├── Registro de todos los movimientos (ingresos y egresos)
├── Tipos de movimiento:
│   ├── INGRESO_VENTA (desde factura)
│   ├── INGRESO_COBRO (desde pago cuenta por cobrar)
│   ├── INGRESO_OTRO (otros ingresos)
│   ├── EGRESO_GASTO (gastos operativos)
│   ├── EGRESO_RETIRO (retiros de caja)
│   └── EGRESO_OTRO (otros egresos)
├── Formas de pago: EFECTIVO, TARJETA_DEBITO, TARJETA_CREDITO, etc.
└── Vinculación con documentos: factura_id, pago_cuenta_id
```

**Características**:
- Tabla: `ventas.MOVIMIENTOS_CAJA`
- Foreign keys: `apertura_caja_id`, `factura_id`, `pago_cuenta_por_cobrar_id`
- Campos: `tipo_movimiento` (ENUM), `forma_pago` (ENUM)
- Montos: `monto`, `referencia`, `concepto`
- Auditoría: `registrado_por`

#### CIERRES_CAJA (Cierre de caja)
```
CIERRES_CAJA
├── Cierre al final del día
├── Cálculos automáticos: saldo_sistema vs saldo_fisico
├── Diferencia: sobrante o faltante
├── Formas de pago desglosadas (efectivo, tarjetas, etc.)
└── Estados: CUADRADO, DIFERENCIA
```

**Características**:
- Tabla: `ventas.CIERRES_CAJA`
- Foreign key: `apertura_caja_id` (unique)
- Campos: `fecha_cierre`, `hora_cierre`
- Cálculos: `saldo_inicial`, `total_ingresos`, `total_egresos`, `saldo_sistema`
- Control: `saldo_fisico`, `diferencia`, `estado`
- Desglose: `efectivo_contado`, `tarjetas_contado`, etc.

#### ARQUEOS_CAJA (Conteos intermedios)
```
ARQUEOS_CAJA
├── Conteos durante el día (opcional)
├── No cierra la caja
├── Control de seguridad
└── Comparación con sistema
```

**Características**:
- Tabla: `ventas.ARQUEOS_CAJA`
- Foreign key: `apertura_caja_id`
- Campos: `fecha_arqueo`, `hora_arqueo`
- Montos: `monto_sistema`, `monto_fisico`, `diferencia`
- Auditoría: `realizado_por`

### 2.7 Nivel 6: Ajustes y Devoluciones

#### NOTAS_CREDITO_VENTAS (Devoluciones)
```
NOTAS_CREDITO_VENTAS
├── Devolución total o parcial de factura
├── Motivos: DEVOLUCION, ERROR_FACTURACION, DESCUENTO_POSTERIOR
├── Estados: BORRADOR → EMITIDA → APLICADA → ANULADA
├── Genera movimientos de stock inversos (entrada)
├── Genera movimientos de caja (egreso o nota de crédito)
└── Actualiza cuenta por cobrar (si aplica)
```

**Características**:
- Tabla: `ventas.NOTAS_CREDITO_VENTAS`
- Número automático con timbrado
- Foreign keys: `factura_id`, `cliente_id`, `timbrado_id`
- Campos: `motivo`, `fecha_emision`, `monto_total`
- Observer: Genera movimientos de stock (ENTRADA_DEVOLUCION)

#### NOTAS_DEBITO_VENTAS (Cargos adicionales)
```
NOTAS_DEBITO_VENTAS
├── Cargos adicionales a factura
├── Motivos: INTERES_MORA, GASTO_ADICIONAL, CORRECCION
├── Estados: BORRADOR → EMITIDA → APLICADA → ANULADA
├── Actualiza cuenta por cobrar
└── Requiere timbrado
```

**Características**:
- Tabla: `ventas.NOTAS_DEBITO_VENTAS`
- Número automático con timbrado
- Foreign keys: `factura_id`, `cliente_id`, `timbrado_id`
- Campos: `motivo`, `fecha_emision`, `monto_total`

---

## 3. RELACIONES CRUCIALES CON OTROS MÓDULOS

### 3.1 Relación con STOCK

```
ventas.FACTURAS_DETALLE.producto_id → stock.PRODUCTOS.id
```

- **Propósito**: Vincular productos facturados con inventario
- **Implicación**: Al emitir factura se genera movimiento de stock (SALIDA_VENTA)
- **Cascade**: `onDelete('restrict')` - No permitir eliminar producto con ventas
- **Observer**: `FacturaDetalleObserver` genera `MOVIMIENTOS_STOCK` automáticamente

```
ventas.REMISIONES_DETALLE.producto_id → stock.PRODUCTOS.id
```

- **Propósito**: Registrar productos entregados
- **Implicación**: Puede generar movimiento de stock si no vino de factura
- **Cascade**: `onDelete('restrict')`

### 3.2 Relación con SERVICIOS (Clientes)

```
ventas.FACTURAS.cliente_id → servicios.CLIENTES.id
ventas.PEDIDOS_CLIENTES.cliente_id → servicios.CLIENTES.id
ventas.CUENTAS_POR_COBRAR.cliente_id → servicios.CLIENTES.id
```

- **Propósito**: Identificar al cliente en todas las transacciones
- **Implicación**: Historial completo de ventas por cliente
- **Cascade**: `onDelete('restrict')` - No permitir eliminar cliente con ventas

### 3.3 Relación con EMPRESA (Timbrados y Configuración)

```
ventas.FACTURAS.timbrado_id → empresa.TIMBRADOS.id
ventas.FACTURAS.punto_expedicion_id → empresa.PUNTOS_EXPEDICION.id
ventas.APERTURAS_CAJA.punto_expedicion_id → empresa.PUNTOS_EXPEDICION.id
```

- **Propósito**: Control fiscal y numeración de documentos
- **Implicación**: Validación de vigencia de timbrados
- **Cascade**: `onDelete('restrict')`

```
ventas.FACTURAS.sucursal_id → empresa.SUCURSALES.id
ventas.FACTURAS.deposito_id → empresa.DEPOSITOS.id
```

- **Propósito**: Control de ubicación de venta y stock
- **Implicación**: Reportes por sucursal y control de stock por depósito
- **Cascade**: `onDelete('restrict')`

### 3.4 Relación con SERVICIOS (Órdenes de Servicio)

```
ventas.FACTURAS → servicios.ORDENES_SERVICIO
```

- **Propósito**: Facturar servicios técnicos + repuestos
- **Flujo**: Diagnóstico → Presupuesto → Orden → Factura
- **Implicación**: Factura puede incluir servicios (FACTURAS_SERVICIOS) y repuestos (FACTURAS_DETALLE)

---

## 4. JERARQUÍA DE CARPETAS Y ARCHIVOS

### 4.1 Estructura Livewire (Lógica de Negocio)

```
app/Livewire/Ventas/
├── PedidoClienteForm.php           # Alta/edición de pedidos
├── PedidoClienteList.php           # Listado de pedidos (historial)
├── FacturaForm.php                 # Alta/edición de facturas (PEDIDOS o SERVICIOS)
├── FacturaList.php                 # Listado de facturas
├── PagarCuenta.php                 # Pago de cuentas por cobrar
├── RemisionForm.php                # Alta/edición de remisiones
├── LibroVentas.php                 # Reporte libro de ventas
├── AperturaCajaForm.php            # Apertura de caja
├── CierreCajaForm.php              # Cierre de caja
├── ArqueoCajaForm.php              # Arqueo de caja
├── NotaCreditoForm.php             # Alta de notas de crédito
└── NotaDebitoForm.php              # Alta de notas de débito
```

### 4.2 Estructura de Modelos (Datos y Relaciones)

```
app/Models/Ventas/
├── PedidoCliente.php               # Modelo principal de pedidos
├── PedidoClienteDetalle.php        # Detalles de pedido
├── Factura.php                     # Modelo principal de facturas
├── FacturaDetalle.php              # Detalles de factura (productos)
├── FacturaServicio.php             # Detalles de factura (servicios)
├── FacturaFormaPago.php            # Formas de pago de factura
├── FacturaCuota.php                # Cuotas de factura a crédito
├── CuentaPorCobrar.php             # Cuenta por cobrar
├── PagoCuentaPorCobrar.php         # Pagos recibidos
├── Remision.php                    # Remisión
├── RemisionDetalle.php             # Detalles de remisión
├── AperturaCaja.php                # Apertura de caja
├── MovimientoCaja.php              # Movimientos de caja
├── CierreCaja.php                  # Cierre de caja
├── ArqueoCaja.php                  # Arqueo de caja
├── NotaCreditoVenta.php            # Nota de crédito
└── NotaDebitoVenta.php             # Nota de débito
```

### 4.3 Estructura de Vistas (Interfaz de Usuario)

#### 4.3.1 Vistas Contenedoras (AdminLTE Layout)

```
resources/views/ventas/
├── pedidos/
│   ├── form.blade.php              # Formulario de pedido
│   └── historial.blade.php         # Listado de pedidos
│
├── facturas/
│   ├── form.blade.php              # Formulario de factura
│   ├── index.blade.php             # Listado de facturas
│   └── show.blade.php              # Vista detalle de factura
│
├── cuentas-cobrar/
│   ├── index.blade.php             # Listado de cuentas por cobrar
│   └── pagar.blade.php             # Formulario de pago
│
├── remisiones/
│   ├── form.blade.php              # Formulario de remisión
│   └── index.blade.php             # Listado de remisiones
│
├── caja/
│   ├── apertura.blade.php          # Apertura de caja
│   ├── cierre.blade.php            # Cierre de caja
│   └── arqueo.blade.php            # Arqueo de caja
│
└── reportes/
    └── libro-ventas.blade.php      # Libro de ventas
```

#### 4.3.2 Vistas de Componentes Livewire (Reactivas)

```
resources/views/livewire/ventas/
├── pedido-cliente-form.blade.php   # Form reactivo de pedido
├── factura-form.blade.php          # Form reactivo de factura
├── pagar-cuenta.blade.php          # Form reactivo de pago
├── remision-form.blade.php         # Form reactivo de remisión
└── libro-ventas.blade.php          # Tabla reactiva de libro de ventas
```

### 4.4 Estructura de Observers (Eventos automáticos)

```
app/Observers/Ventas/
├── FacturaObserver.php             # Al crear/actualizar/emitir factura
├── FacturaDetalleObserver.php      # Al guardar detalle → genera movimiento stock
├── FacturaFormaPagoObserver.php    # Al guardar forma pago → genera movimiento caja
├── PagoCuentaObserver.php          # Al registrar pago → actualiza cuenta y caja
└── RemisionObserver.php            # Al emitir remisión → genera movimiento stock
```

---

## 5. FLUJOS DE TRABAJO PRINCIPALES

### 5.1 Flujo Completo: Pedido → Factura → Cobro

```
1. PEDIDO DE CLIENTE
   ├── Cliente solicita productos
   ├── Vendedor registra pedido en estado BORRADOR
   ├── Se verifica disponibilidad de stock
   ├── Se calculan precios y totales
   ├── Se confirma pedido → estado CONFIRMADO
   └── Se prepara mercadería → estado LISTO_ENTREGAR

2. FACTURACIÓN
   ├── Se abre caja (APERTURA_CAJA) si no está abierta
   ├── Se carga pedido en factura
   ├── Se verifican timbrados vigentes
   ├── Se completan datos del cliente
   ├── Se selecciona condición de pago (CONTADO o CREDITO)
   ├── Si CONTADO: se registran formas de pago
   ├── Si CREDITO: se calculan cuotas automáticamente
   ├── Se guarda factura en BORRADOR
   ├── Se emite factura:
   │   ├── Genera número de factura del timbrado
   │   ├── Estado → EMITIDA
   │   ├── Observer: Genera MOVIMIENTOS_STOCK (SALIDA_VENTA)
   │   ├── Si CONTADO: Observer genera MOVIMIENTOS_CAJA (INGRESO_VENTA)
   │   └── Si CREDITO: Observer genera CUENTA_POR_COBRAR

3. ENTREGA (Opcional)
   ├── Se genera REMISION desde factura
   ├── Se imprime remisión para chofer
   ├── Cliente firma recepción
   └── Observer: Actualiza stock si aplica

4. COBRO (Solo si es CREDITO)
   ├── Cliente realiza pago (total o parcial)
   ├── Se registra PAGO_CUENTA_POR_COBRAR
   ├── Observer: Actualiza CUENTA_POR_COBRAR (monto_pagado, saldo_pendiente)
   ├── Observer: Actualiza FACTURA_CUOTA (estado)
   ├── Observer: Genera MOVIMIENTO_CAJA (INGRESO_COBRO)
   └── Si saldo_pendiente = 0 → estado PAGADA

5. CIERRE DE CAJA
   ├── Al final del día se cierra caja
   ├── Se contabiliza efectivo físico
   ├── Se compara con saldo sistema
   ├── Se genera CIERRE_CAJA
   └── APERTURA_CAJA → estado CERRADA
```

### 5.2 Flujo Alternativo: Venta Directa (Sin Pedido)

```
1. FACTURA DIRECTA
   ├── Se abre caja
   ├── Se crea factura sin pedido previo
   ├── Se agregan productos manualmente (búsqueda en tiempo real)
   ├── Se completan datos del cliente
   ├── Se selecciona CONTADO (obligatorio para venta directa)
   ├── Se registran formas de pago
   ├── Se emite factura:
   │   ├── Genera número de factura
   │   ├── Observer: Genera MOVIMIENTOS_STOCK (SALIDA_VENTA)
   │   └── Observer: Genera MOVIMIENTOS_CAJA (INGRESO_VENTA)
   └── Cliente retira productos
```

### 5.3 Flujo de Servicios: Orden → Factura

```
1. ORDEN DE SERVICIO
   ├── Cliente solicita reparación/instalación
   ├── Se genera diagnóstico
   ├── Se crea presupuesto (servicios + repuestos)
   ├── Cliente aprueba presupuesto
   └── Se genera ORDEN_SERVICIO

2. EJECUCIÓN DEL SERVICIO
   ├── Técnico realiza el trabajo
   ├── Registra servicios realizados
   ├── Registra repuestos utilizados
   └── Finaliza orden → estado FINALIZADA

3. FACTURACIÓN DE SERVICIO
   ├── Se carga orden de servicio en factura
   ├── Tipo facturación: SERVICIOS
   ├── Se cargan automáticamente:
   │   ├── FACTURAS_SERVICIOS (tipos de servicio)
   │   └── FACTURAS_DETALLE (repuestos)
   ├── Se calcula total (servicios + repuestos + IVA)
   ├── Se selecciona condición de pago
   ├── Se emite factura:
   │   ├── Observer: Genera MOVIMIENTOS_STOCK (solo repuestos)
   │   └── Observer: Genera MOVIMIENTOS_CAJA o CUENTA_POR_COBRAR
   └── Cliente retira equipo reparado
```

### 5.4 Flujo de Devolución: Nota de Crédito

```
1. SOLICITUD DE DEVOLUCION
   ├── Cliente devuelve producto (defecto, error, etc.)
   ├── Se verifica factura original
   ├── Se verifica estado del producto
   └── Se autoriza devolución

2. GENERACIÓN DE NOTA DE CRÉDITO
   ├── Se crea NOTA_CREDITO_VENTA
   ├── Se vincula con factura original
   ├── Se selecciona motivo (DEVOLUCION, ERROR_FACTURACION, etc.)
   ├── Se cargan productos devueltos (total o parcial)
   ├── Se calcula monto a acreditar
   ├── Se emite nota de crédito:
   │   ├── Observer: Genera MOVIMIENTOS_STOCK (ENTRADA_DEVOLUCION)
   │   ├── Observer: Genera MOVIMIENTO_CAJA (EGRESO) o nota de crédito
   │   └── Observer: Actualiza CUENTA_POR_COBRAR (si aplica)

3. COMPENSACIÓN
   ├── Cliente puede usar nota de crédito en próxima compra
   ├── O se devuelve efectivo
   └── Se registra aplicación de nota de crédito
```

### 5.5 Flujo de Control de Caja

```
1. APERTURA DIARIA
   ├── Usuario abre caja del punto de expedición
   ├── Registra saldo inicial (efectivo en caja)
   ├── Sistema verifica que no haya otra apertura activa
   └── APERTURA_CAJA → estado ABIERTA

2. OPERACIONES DEL DÍA
   ├── Ingresos:
   │   ├── Ventas al contado (automático desde facturas)
   │   ├── Cobros de cuentas por cobrar (automático desde pagos)
   │   └── Otros ingresos (manual)
   ├── Egresos:
   │   ├── Gastos operativos (manual)
   │   ├── Retiros de caja (manual)
   │   ├── Devoluciones (automático desde notas de crédito)
   │   └── Otros egresos (manual)

3. ARQUEOS INTERMEDIOS (Opcional)
   ├── Se cuenta efectivo físico
   ├── Se compara con saldo sistema
   ├── Se registra ARQUEO_CAJA
   └── Si hay diferencia → se investiga

4. CIERRE DIARIO
   ├── Al final del día se procede al cierre
   ├── Se calculan totales automáticamente:
   │   ├── Saldo inicial
   │   ├── Total ingresos (por forma de pago)
   │   ├── Total egresos
   │   └── Saldo teórico sistema
   ├── Se cuenta efectivo físico
   ├── Se registra saldo físico
   ├── Sistema calcula diferencia:
   │   ├── Diferencia = 0 → estado CUADRADO
   │   └── Diferencia ≠ 0 → estado DIFERENCIA (requiere justificación)
   ├── Se genera CIERRE_CAJA
   ├── APERTURA_CAJA → estado CERRADA
   └── Se imprime reporte de cierre
```

---

## 6. CONSIDERACIONES ESPECIALES PARA PARAGUAY

### 6.1 Aspectos Fiscales

- **IVA Dual**: 10% para la mayoría, 5% para productos de canasta básica
- **Timbrados**: Numeración fiscal vigente por rango de fechas
- **Puntos de Expedición**: Cada caja tiene su numeración independiente
- **Facturación Electrónica**: Integración con SET (Sistema de Integración Tributaria)
  - CDC (Código de Control): 44 caracteres
  - XML firmado con certificado digital
  - Respuesta asíncrona de SET (APROBADO/RECHAZADO)
  - QR code en factura impresa
- **Formatos de numeración**: `001-001-0000001` (sucursal-punto-número)
- **Conservación**: Facturas deben conservarse 5 años

### 6.2 Condiciones Comerciales Locales

- **Ventas a Crédito**: Común en el rubro (30, 60, 90 días)
- **Cuotas**: Máximo 6 cuotas sin interés
- **Garantías**: Incluidas en precio, sin costo adicional
- **Servicio Post-Venta**: Crítico en aguatería (instalación, reparación)
- **Moneda**: Guaraníes (PYG) principalmente
  - USD para productos importados (conversión al momento)
- **Horarios**: Apertura típica 7:00-18:00 (Lu-Vi), 7:00-12:00 (Sá)

### 6.3 Prácticas del Rubro Aguatería

- **Venta + Instalación**: Común facturar producto + servicio de instalación
- **Urgencias**: Ventas fuera de horario (roturas, emergencias)
- **Delivery**: Para clientes frecuentes y obras en construcción
- **Descuentos**: Por volumen, por pronto pago, por cliente frecuente
- **Remisiones**: Obligatorias para transporte de mercadería
- **Stock Reservado**: Para pedidos grandes (construcciones)

---

## 7. INTEGRACIÓN CON MÓDULOS EXISTENTES

### 7.1 Integración con STOCK

#### Puntos de Conexión
```
1. FACTURAS_DETALLE → genera MOVIMIENTOS_STOCK (SALIDA_VENTA)
2. REMISIONES_DETALLE → puede generar MOVIMIENTOS_STOCK
3. NOTAS_CREDITO → genera MOVIMIENTOS_STOCK (ENTRADA_DEVOLUCION)
4. Validación de stock disponible antes de facturar
```

#### Flujo Integrado
```
Factura → Emisión →
  ├── FacturaDetalleObserver
  ├── Por cada detalle:
  │   ├── Obtener producto_id y deposito_id
  │   ├── Crear MOVIMIENTO_STOCK:
  │   │   ├── tipo_movimiento = 'SALIDA_VENTA'
  │   │   ├── cantidad = cantidad facturada
  │   │   ├── documento_tipo = 'FACTURA'
  │   │   ├── documento_id = factura_id
  │   │   ├── stock_anterior = stock actual
  │   │   └── stock_posterior = stock_anterior - cantidad
  │   └── Actualizar STOCK.stock_actual
  └── Stock actualizado en tiempo real
```

### 7.2 Integración con SERVICIOS

#### Puntos de Conexión
```
1. FACTURAS.cliente_id → CLIENTES.id (historial de compras)
2. FACTURAS_SERVICIOS ← ORDENES_SERVICIO (facturación de servicios)
3. Clientes tienen cuenta corriente unificada
```

#### Flujo Integrado
```
Orden Servicio → Finalizada →
  ├── Cargar en FACTURA (tipo SERVICIOS)
  ├── Importar automáticamente:
  │   ├── Diagnóstico.tiposServicio → FACTURAS_SERVICIOS
  │   └── Diagnóstico.repuestos → FACTURAS_DETALLE
  ├── Cliente aprueba factura
  ├── Emitir factura
  └── Orden Servicio → estado FACTURADA
```

### 7.3 Integración con EMPRESA

#### Puntos de Conexión
```
1. TIMBRADOS: Control de numeración fiscal
2. PUNTOS_EXPEDICION: Identificación de caja
3. SUCURSALES: Ubicación de venta
4. DEPOSITOS: Origen de stock
```

#### Validaciones Críticas
```
1. Timbrado vigente: fecha_actual BETWEEN fecha_inicio AND fecha_fin
2. Timbrado activo: activo = true
3. Numeración disponible: numero_actual <= numero_fin
4. Punto expedición activo: activo = true
5. Sucursal activa: activo = true
6. Depósito activo: activo = true
```

---

## 8. MODELO LÓGICO DE DATOS

### 8.1 Diagrama Entidad-Relación (Texto)

```
[CLIENTES] 1----N [PEDIDOS_CLIENTES] 1----N [PEDIDOS_CLIENTES_DETALLE] N----1 [PRODUCTOS]
    |                      |
    |                      1
    |                      |
    1                      N
    |                [FACTURAS] 1----N [FACTURAS_DETALLE] N----1 [PRODUCTOS]
    |                      |                    |
    N                      |                    └─→ genera MOVIMIENTOS_STOCK
    |                      1
[CUENTAS_POR_COBRAR]       |
    |                      N
    1                      |
    |                [FACTURAS_SERVICIOS]
    N                      |
    |                      1
[PAGOS_CUENTAS]            |
    |                [FACTURAS_FORMAS_PAGO]
    └─→ genera             |
        MOVIMIENTOS_CAJA ←─┘
              |
              N
              |
              1
        [APERTURAS_CAJA] 1----1 [CIERRES_CAJA]
              |
              1
              N
        [ARQUEOS_CAJA]

[FACTURAS] 1----N [REMISIONES] 1----N [REMISIONES_DETALLE] N----1 [PRODUCTOS]

[FACTURAS] 1----N [NOTAS_CREDITO_VENTAS]
              └─→ genera MOVIMIENTOS_STOCK (inverso)

[FACTURAS] 1----N [NOTAS_DEBITO_VENTAS]

[TIMBRADOS] 1----N [FACTURAS]
[PUNTOS_EXPEDICION] 1----N [FACTURAS]
[PUNTOS_EXPEDICION] 1----N [APERTURAS_CAJA]
```

### 8.2 Tablas del Schema `ventas`

#### Tabla: PEDIDOS_CLIENTES
```sql
id                          BIGSERIAL PRIMARY KEY
numero_pedido               VARCHAR(20) UNIQUE
fecha_pedido                DATE
fecha_entrega_estimada      DATE
cliente_id                  BIGINT → servicios.CLIENTES
sucursal_id                 BIGINT → empresa.SUCURSALES
deposito_id                 BIGINT → empresa.DEPOSITOS
vendedor_id                 BIGINT → users
cotizacion_id               BIGINT (futuro)
condicion_pago              ENUM('CONTADO', '7_DIAS', '15_DIAS', '30_DIAS', '60_DIAS', '90_DIAS')
tipo_pedido                 ENUM('NORMAL', 'URGENTE', 'ENTREGA_PROGRAMADA', 'MAYORISTA', 'MINORISTA')
tipo_entrega                ENUM('RETIRO_LOCAL', 'DELIVERY', 'ENVIO_TRANSPORTE')
subtotal                    DECIMAL(15,2)
iva_10                      DECIMAL(15,2)
iva_5                       DECIMAL(15,2)
exenta                      DECIMAL(15,2)
total_iva                   DECIMAL(15,2)
descuento_global            DECIMAL(15,2)
flete                       DECIMAL(15,2)
total                       DECIMAL(15,2)
estado                      ENUM('BORRADOR', 'PENDIENTE', 'CONFIRMADO', 'EN_PREPARACION',
                                 'LISTO_ENTREGAR', 'PARCIALMENTE_ENTREGADO',
                                 'COMPLETAMENTE_ENTREGADO', 'FACTURADO', 'CANCELADO', 'ANULADO')
porcentaje_entregado        DECIMAL(5,2)
observaciones               TEXT
condiciones_especiales      TEXT
direccion_entrega           VARCHAR(500)
creado_por, actualizado_por, confirmado_por, facturado_por BIGINT
confirmado_en, facturado_en TIMESTAMP
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: PEDIDOS_CLIENTES_DETALLE
```sql
id                          BIGSERIAL PRIMARY KEY
pedido_cliente_id           BIGINT → PEDIDOS_CLIENTES (CASCADE)
producto_id                 BIGINT → stock.PRODUCTOS
cotizacion_detalle_id       BIGINT (futuro)
cantidad_solicitada         DECIMAL(10,2)
cantidad_entregada          DECIMAL(10,2) DEFAULT 0
cantidad_pendiente          DECIMAL(10,2)
precio_unitario             DECIMAL(15,2)
descuento_porcentaje        DECIMAL(5,2)
descuento_monto             DECIMAL(15,2)
subtotal                    DECIMAL(15,2)
iva_porcentaje              DECIMAL(5,2)
iva_monto                   DECIMAL(15,2)
total                       DECIMAL(15,2)
estado                      ENUM('PENDIENTE', 'PARCIALMENTE_ENTREGADO',
                                 'COMPLETAMENTE_ENTREGADO', 'CANCELADO')
observaciones               TEXT
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: FACTURAS
```sql
id                          BIGSERIAL PRIMARY KEY
numero_factura              VARCHAR(20)
fecha_emision               DATE
fecha_vencimiento           DATE
cliente_id                  BIGINT → servicios.CLIENTES
pedido_cliente_id           BIGINT → PEDIDOS_CLIENTES
cotizacion_id               BIGINT (futuro)
timbrado_id                 BIGINT → empresa.TIMBRADOS
punto_expedicion_id         BIGINT → empresa.PUNTOS_EXPEDICION
numero_timbrado             VARCHAR(15)
sucursal_id                 BIGINT → empresa.SUCURSALES
deposito_id                 BIGINT → empresa.DEPOSITOS
vendedor_id                 BIGINT → users
condicion_pago              ENUM('CONTADO', 'CREDITO')
cantidad_cuotas             INTEGER
subtotal                    DECIMAL(15,2)
iva_10                      DECIMAL(15,2)
iva_5                       DECIMAL(15,2)
exenta                      DECIMAL(15,2)
total_iva                   DECIMAL(15,2)
descuento_global            DECIMAL(15,2)
flete                       DECIMAL(15,2)
total                       DECIMAL(15,2)
es_electronica              BOOLEAN
cdc                         VARCHAR(44) UNIQUE
qr_data                     TEXT
xml_firmado                 TEXT
estado_set                  ENUM('PENDIENTE', 'APROBADO', 'RECHAZADO', 'ANULADO')
fecha_envio_set             TIMESTAMP
fecha_respuesta_set         TIMESTAMP
mensaje_set                 TEXT
estado                      ENUM('BORRADOR', 'EMITIDA', 'PAGADA', 'PARCIALMENTE_PAGADA',
                                 'VENCIDA', 'ANULADA')
observaciones               TEXT
motivo_anulacion            TEXT
creado_por, actualizado_por, emitido_por, anulado_por BIGINT
emitido_en, anulado_en      TIMESTAMP
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP

UNIQUE (numero_factura, timbrado_id)
```

#### Tabla: FACTURAS_DETALLE
```sql
id                          BIGSERIAL PRIMARY KEY
factura_id                  BIGINT → FACTURAS (CASCADE)
pedido_detalle_id           BIGINT → PEDIDOS_CLIENTES_DETALLE
producto_id                 BIGINT → stock.PRODUCTOS
cantidad                    DECIMAL(10,2)
precio_unitario             DECIMAL(15,2)
descuento_porcentaje        DECIMAL(5,2)
descuento_monto             DECIMAL(15,2) (calculado)
subtotal                    DECIMAL(15,2) (calculado)
iva_porcentaje              DECIMAL(5,2)
iva_monto                   DECIMAL(15,2) (calculado)
total                       DECIMAL(15,2) (calculado)
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: FACTURAS_SERVICIOS
```sql
id                          BIGSERIAL PRIMARY KEY
factura_id                  BIGINT → FACTURAS (CASCADE)
codigo                      VARCHAR(50)
descripcion                 TEXT
cantidad                    DECIMAL(10,2)
precio_unitario             DECIMAL(15,2)
subtotal                    DECIMAL(15,2)
iva_porcentaje              DECIMAL(5,2)
iva_monto                   DECIMAL(15,2)
total                       DECIMAL(15,2)
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: FACTURAS_FORMAS_PAGO
```sql
id                          BIGSERIAL PRIMARY KEY
factura_id                  BIGINT → FACTURAS (CASCADE)
forma_pago                  ENUM('EFECTIVO', 'TARJETA_DEBITO', 'TARJETA_CREDITO',
                                 'TRANSFERENCIA', 'CHEQUE', 'GIROS')
monto                       DECIMAL(15,2)
referencia                  VARCHAR(100)
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: FACTURAS_CUOTAS
```sql
id                          BIGSERIAL PRIMARY KEY
factura_id                  BIGINT → FACTURAS (CASCADE)
numero_cuota                INTEGER
monto                       DECIMAL(15,2)
fecha_vencimiento           DATE
monto_pagado                DECIMAL(15,2) DEFAULT 0
saldo_pendiente             DECIMAL(15,2)
estado                      ENUM('PENDIENTE', 'PARCIALMENTE_PAGADA', 'PAGADA', 'VENCIDA')
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: CUENTAS_POR_COBRAR
```sql
id                          BIGSERIAL PRIMARY KEY
factura_id                  BIGINT UNIQUE → FACTURAS (RESTRICT)
numero_factura              VARCHAR(50)
cliente_id                  BIGINT → servicios.CLIENTES (RESTRICT)
fecha_emision               DATE
fecha_vencimiento           DATE
monto_total                 DECIMAL(15,2)
monto_pagado                DECIMAL(15,2) DEFAULT 0
saldo_pendiente             DECIMAL(15,2)
estado                      ENUM('PENDIENTE', 'PARCIALMENTE_PAGADA', 'PAGADA', 'VENCIDA', 'ANULADA')
observaciones               TEXT
creado_por, actualizado_por BIGINT
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: PAGOS_CUENTAS_POR_COBRAR
```sql
id                          BIGSERIAL PRIMARY KEY
cuenta_por_cobrar_id        BIGINT → CUENTAS_POR_COBRAR
factura_cuota_id            BIGINT → FACTURAS_CUOTAS
fecha_pago                  DATE
monto_pagado                DECIMAL(15,2)
forma_pago                  ENUM('EFECTIVO', 'TARJETA_DEBITO', 'TARJETA_CREDITO',
                                 'TRANSFERENCIA', 'CHEQUE', 'GIROS')
referencia                  VARCHAR(100)
observaciones               TEXT
recibido_por                BIGINT → users
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: REMISIONES
```sql
id                          BIGSERIAL PRIMARY KEY
numero_remision             VARCHAR(20) UNIQUE
fecha_emision               DATE
fecha_entrega               DATE
cliente_id                  BIGINT → servicios.CLIENTES
factura_id                  BIGINT → FACTURAS
pedido_cliente_id           BIGINT → PEDIDOS_CLIENTES
sucursal_id                 BIGINT → empresa.SUCURSALES
deposito_id                 BIGINT → empresa.DEPOSITOS
responsable_id              BIGINT → users
observaciones               TEXT
direccion_entrega           VARCHAR(500)
receptor_nombre             VARCHAR(200)
receptor_ci                 VARCHAR(20)
fecha_recepcion             TIMESTAMP
estado                      ENUM('BORRADOR', 'EMITIDA', 'EN_TRANSITO', 'ENTREGADA', 'ANULADA')
motivo_anulacion            TEXT
creado_por, actualizado_por, emitido_por, anulado_por BIGINT
emitido_en, anulado_en      TIMESTAMP
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: REMISIONES_DETALLE
```sql
id                          BIGSERIAL PRIMARY KEY
remision_id                 BIGINT → REMISIONES (CASCADE)
producto_id                 BIGINT → stock.PRODUCTOS
producto_descripcion        VARCHAR(500)
cantidad                    DECIMAL(15,2)
unidad_medida               VARCHAR(20)
precio_unitario             DECIMAL(15,2)
subtotal                    DECIMAL(15,2)
factura_detalle_id          BIGINT → FACTURAS_DETALLE
observaciones               TEXT
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: APERTURAS_CAJA
```sql
id                          BIGSERIAL PRIMARY KEY
punto_expedicion_id         BIGINT → empresa.PUNTOS_EXPEDICION
usuario_id                  BIGINT → users
fecha_apertura              DATE
hora_apertura               TIME
saldo_inicial               DECIMAL(15,2)
estado                      ENUM('ABIERTA', 'CERRADA', 'CANCELADA')
observaciones               TEXT
activo                      BOOLEAN
creadoPor, actualizadoPor   BIGINT
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: MOVIMIENTOS_CAJA
```sql
id                          BIGSERIAL PRIMARY KEY
apertura_caja_id            BIGINT → APERTURAS_CAJA
tipo_movimiento             ENUM('INGRESO_VENTA', 'INGRESO_COBRO', 'INGRESO_OTRO',
                                 'EGRESO_GASTO', 'EGRESO_RETIRO', 'EGRESO_OTRO')
forma_pago                  ENUM('EFECTIVO', 'TARJETA_DEBITO', 'TARJETA_CREDITO',
                                 'TRANSFERENCIA', 'CHEQUE', 'GIROS')
monto                       DECIMAL(15,2)
concepto                    VARCHAR(500)
referencia                  VARCHAR(100)
factura_id                  BIGINT → FACTURAS
pago_cuenta_por_cobrar_id   BIGINT → PAGOS_CUENTAS_POR_COBRAR
registrado_por              BIGINT → users
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: CIERRES_CAJA
```sql
id                          BIGSERIAL PRIMARY KEY
apertura_caja_id            BIGINT UNIQUE → APERTURAS_CAJA
fecha_cierre                DATE
hora_cierre                 TIME
saldo_inicial               DECIMAL(15,2)
total_ingresos              DECIMAL(15,2)
total_egresos               DECIMAL(15,2)
saldo_sistema               DECIMAL(15,2)
saldo_fisico                DECIMAL(15,2)
diferencia                  DECIMAL(15,2)
estado                      ENUM('CUADRADO', 'DIFERENCIA')
efectivo_contado            DECIMAL(15,2)
tarjetas_contado            DECIMAL(15,2)
efectivo_cobros             DECIMAL(15,2)
otros_ingresos              DECIMAL(15,2)
gastos                      DECIMAL(15,2)
retiros                     DECIMAL(15,2)
observaciones               TEXT
cerrado_por                 BIGINT → users
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: ARQUEOS_CAJA
```sql
id                          BIGSERIAL PRIMARY KEY
apertura_caja_id            BIGINT → APERTURAS_CAJA
fecha_arqueo                DATE
hora_arqueo                 TIME
monto_sistema               DECIMAL(15,2)
monto_fisico                DECIMAL(15,2)
diferencia                  DECIMAL(15,2)
observaciones               TEXT
realizado_por               BIGINT → users
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: NOTAS_CREDITO_VENTAS
```sql
id                          BIGSERIAL PRIMARY KEY
numero_nota_credito         VARCHAR(20) UNIQUE
factura_id                  BIGINT → FACTURAS
cliente_id                  BIGINT → servicios.CLIENTES
timbrado_id                 BIGINT → empresa.TIMBRADOS
fecha_emision               DATE
motivo                      ENUM('DEVOLUCION', 'ERROR_FACTURACION', 'DESCUENTO_POSTERIOR')
monto_total                 DECIMAL(15,2)
estado                      ENUM('BORRADOR', 'EMITIDA', 'APLICADA', 'ANULADA')
observaciones               TEXT
creado_por, emitido_por     BIGINT
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: NOTAS_DEBITO_VENTAS
```sql
id                          BIGSERIAL PRIMARY KEY
numero_nota_debito          VARCHAR(20) UNIQUE
factura_id                  BIGINT → FACTURAS
cliente_id                  BIGINT → servicios.CLIENTES
timbrado_id                 BIGINT → empresa.TIMBRADOS
fecha_emision               DATE
motivo                      ENUM('INTERES_MORA', 'GASTO_ADICIONAL', 'CORRECCION')
monto_total                 DECIMAL(15,2)
estado                      ENUM('BORRADOR', 'EMITIDA', 'APLICADA', 'ANULADA')
observaciones               TEXT
creado_por, emitido_por     BIGINT
activo                      BOOLEAN
created_at, updated_at, deleted_at TIMESTAMP
```

---

## 9. ESTRATEGIA DE IMPLEMENTACIÓN

### 9.1 Fase 1: Pedidos de Clientes (✅ COMPLETADO)

```
1. Migraciones: PEDIDOS_CLIENTES, PEDIDOS_CLIENTES_DETALLE
2. Modelos: PedidoCliente, PedidoClienteDetalle con relaciones
3. Livewire: PedidoClienteForm (alta/edición)
4. Vistas: Form y listado
5. Validaciones: Stock disponible, cliente activo
```

### 9.2 Fase 2: Facturación (✅ COMPLETADO)

```
1. Migraciones: FACTURAS, FACTURAS_DETALLE, FACTURAS_SERVICIOS, FACTURAS_FORMAS_PAGO
2. Modelos: Factura, FacturaDetalle, FacturaServicio, FacturaFormaPago con relaciones
3. Livewire: FacturaForm (2 modos: PEDIDOS y SERVICIOS)
4. Vistas: Form, listado, detalle
5. Observers: Generación de movimientos de stock y caja
6. Validaciones: Timbrado vigente, stock disponible, formas de pago
```

### 9.3 Fase 3: Gestión de Caja (✅ COMPLETADO)

```
1. Migraciones: APERTURAS_CAJA, MOVIMIENTOS_CAJA, CIERRES_CAJA, ARQUEOS_CAJA
2. Modelos: AperturaCaja, MovimientoCaja, CierreCaja, ArqueoCaja
3. Livewire: AperturaCajaForm, CierreCajaForm, ArqueoCajaForm
4. Vistas: Apertura, cierre, arqueo
5. Observers: Creación automática de movimientos desde facturas y pagos
6. Validaciones: Una apertura activa por usuario, cierre con arqueo
```

### 9.4 Fase 4: Cuentas por Cobrar (✅ COMPLETADO)

```
1. Migraciones: CUENTAS_POR_COBRAR, PAGOS_CUENTAS_POR_COBRAR, FACTURAS_CUOTAS
2. Modelos: CuentaPorCobrar, PagoCuentaPorCobrar, FacturaCuota
3. Livewire: PagarCuenta (registro de pagos)
4. Vistas: Listado de cuentas, form de pago
5. Observers: Creación automática desde factura a crédito, actualización de saldos
6. Reportes: Cuentas vencidas, por vencer, cliente moroso
```

### 9.5 Fase 5: Remisiones (✅ COMPLETADO)

```
1. Migraciones: REMISIONES, REMISIONES_DETALLE
2. Modelos: Remision, RemisionDetalle
3. Livewire: RemisionForm
4. Vistas: Form, listado, PDF para impresión
5. Observers: Actualización de stock si aplica
```

### 9.6 Fase 6: Ajustes y Reportes (⏳ PENDIENTE)

```
1. Migraciones: NOTAS_CREDITO_VENTAS, NOTAS_DEBITO_VENTAS
2. Modelos: NotaCreditoVenta, NotaDebitoVenta
3. Livewire: NotaCreditoForm, NotaDebitoForm
4. Reportes:
   - Libro de ventas
   - Ventas por período
   - Ventas por vendedor
   - Ventas por cliente
   - Productos más vendidos
   - Análisis de rentabilidad
```

### 9.7 Fase 7: Facturación Electrónica SET (🔮 FUTURO)

```
1. Integración con API SET
2. Generación de CDC
3. Firma digital de XML
4. Envío a SET
5. Recepción de respuesta
6. Generación de QR code
7. Impresión con formato SET
```

---

## 10. CHECKLIST DE ÉXITO

### 10.1 Técnico

- [x] Schema `ventas` creado y configurado en search_path
- [x] Migraciones de pedidos ejecutadas correctamente
- [x] Migraciones de facturas ejecutadas correctamente
- [x] Migraciones de caja ejecutadas correctamente
- [x] Migraciones de cuentas por cobrar ejecutadas correctamente
- [x] Migraciones de remisiones ejecutadas correctamente
- [ ] Migraciones de ajustes (notas crédito/débito) ejecutadas
- [x] Modelos con relaciones implementados
- [x] Observers configurados y funcionando
- [x] Componentes Livewire operativos
- [x] Vistas Blade con AdminLTE integrado
- [x] Rutas agrupadas y protegidas
- [ ] Permisos configurados en seeder
- [x] Menú AdminLTE actualizado

### 10.2 Funcional

- [x] Alta de pedidos cliente funcional
- [x] Facturación desde pedidos operativa
- [x] Facturación desde servicios operativa
- [x] Facturación directa (sin pedido) operativa
- [x] Generación automática de movimientos de stock
- [x] Generación automática de movimientos de caja
- [x] Apertura/cierre de caja funcional
- [x] Cuentas por cobrar generándose automáticamente
- [x] Pagos de cuentas por cobrar funcional
- [x] Remisiones generándose correctamente
- [ ] Notas de crédito funcionales
- [ ] Notas de débito funcionales
- [x] Libro de ventas generando información
- [ ] Reportes generando información útil

### 10.3 Operativo

- [x] Usuarios pueden crear pedidos
- [x] Usuarios pueden facturar pedidos
- [x] Usuarios pueden facturar servicios
- [x] Stock se actualiza al emitir factura
- [x] Caja registra movimientos automáticamente
- [x] Cuentas por cobrar se generan automáticamente
- [x] Remisiones se emiten correctamente
- [ ] Alertas de cobranza funcionando
- [ ] Reportes son comprensibles y útiles
- [ ] Sistema responde en tiempos aceptables
- [ ] Data real puede procesarse
- [ ] Backup y restore probados

### 10.4 Integración

- [x] Integración con módulo STOCK funcionando
- [x] Integración con módulo SERVICIOS funcionando
- [x] Integración con módulo EMPRESA funcionando
- [x] Timbrados controlando numeración
- [x] Puntos de expedición identificando cajas
- [ ] Facturación electrónica SET (futuro)

---

## RESUMEN EJECUTIVO

Este modelo conceptual proporciona:

- ✅ Visión completa del módulo Ventas desde arquitectura hasta operación
- ✅ Jerarquía clara de entidades y sus relaciones
- ✅ Estructura detallada de archivos y carpetas
- ✅ Flujos de trabajo principales para cada funcionalidad
- ✅ Consideraciones específicas para el mercado paraguayo
- ✅ Integración completa con módulos existentes (Stock, Servicios, Empresa)
- ✅ Modelo lógico de datos detallado
- ✅ Ruta de implementación faseada y realista

El módulo está diseñado para ser:

- **Robusto**: Manejo completo del ciclo de ventas con trazabilidad
- **Escalable**: Fácil expansión a facturación electrónica SET
- **Integrado**: Conexión natural con stock, servicios y caja
- **Práctico**: Resuelve necesidades reales de ferretería con aguatería
- **Paraguayo**: Considera particularidades locales fiscales y comerciales
- **Completo**: Desde pedido hasta cobro, pasando por facturación y entrega

---

**Fecha de documentación**: Diciembre 2025
**Versión del sistema**: SIGEA v4
**Framework**: Laravel 12.26.4 / PHP 8.4.15 / PostgreSQL
**Módulos relacionados**: Stock, Servicios, Empresa
**Estado**: En producción (fases 1-5 completadas)
