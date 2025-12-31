# MODELO CONCEPTUAL Y JERÁRQUICO COMPLETO - MÓDULO SERVICIOS
## Sistema Integrado de Gestión de Servicios Técnicos para Ferretería Especializada en Aguatería

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

- **Schema independiente**: `servicios` separado de `empresa`, `stock`, `ventas` pero relacionado
- **Relación clave**: Servicios vinculados a Stock (repuestos), Clientes y Facturación
- **Preparado para expansión**: Diseñado para integrarse con sistema de garantías
- **Configuración unificada**: Mismo AdminLTE, mismos estilos, mismos patrones

---

## 2. JERARQUÍA CONCEPTUAL

### 2.1 Nivel 0: Schema y Configuración

```
servicios (SCHEMA POSTGRESQL)
├── Configuración en search_path: 'servicios,stock,ventas,empresa,public'
├── Relación con stock.PRODUCTOS (foreign keys para repuestos y equipos)
├── Relación con ventas.FACTURAS (facturación de servicios)
├── Relación con empresa.SUCURSALES (ubicación de servicio)
└── Relación con public.users (técnicos y auditores)
```

### 2.2 Nivel 1: Gestión de Clientes

#### CLIENTES (Base del módulo)
```
CLIENTES
├── Tipos: FISICA o JURIDICA
├── Documento único (CI o RUC)
├── Datos de contacto (teléfono, celular, email)
├── Historial completo de servicios
└── Compartido con módulo Ventas (mismo cliente para compras y servicios)
```

**Características**:
- Tabla: `servicios.CLIENTES`
- Tipos: `fisica` (personas), `juridica` (empresas)
- Documento único: CI para físicas, RUC para jurídicas
- Relaciones: usado por SOLICITUDES_SERVICIO, RECEPCIONES, DIAGNOSTICOS
- Auditoría: `creadoPor`, `actualizadoPor`

### 2.3 Nivel 2: Solicitud y Recepción

#### SOLICITUDES_SERVICIO (Inicio del flujo)
```
SOLICITUDES_SERVICIO
├── Origen del flujo de servicios
├── Cliente solicita servicio técnico
├── Estados: PENDIENTE → EN_PROCESO → COMPLETADO
├── Tipos: MANTENIMIENTO, REPARACION, DIAGNOSTICO
├── Prioridad: BAJA, MEDIA, ALTA
└── Genera RECEPCION del equipo
```

**Características**:
- Tabla: `servicios.SOLICITUDES_SERVICIO`
- Número automático: `SOL-000001`, `SOL-000002`...
- Relaciones: `cliente_id`, `producto_id` (equipo a reparar)
- Tipos de servicio: `mantenimiento`, `reparacion`, `diagnostico`
- Prioridades: `baja`, `media`, `alta`
- Estados: `pendiente`, `en_proceso`, `completado`

#### RECEPCIONES (Formalización de ingreso)
```
RECEPCIONES
├── Recepción física del equipo
├── Número único: REC-000001
├── Estado del equipo: BUENO, REGULAR, MALO
├── Datos técnicos: marca, modelo, serie
├── Problema reportado por cliente
├── Accesorios recibidos
└── Base para DIAGNOSTICO
```

**Características**:
- Tabla: `servicios.RECEPCIONES`
- Número automático: `REC-000001`, `REC-000002`...
- Foreign keys: `solicitud_id`, `cliente_id`, `producto_id`
- Estados recepción: `bueno`, `regular`, `malo`
- Campos técnicos: `tipo_equipo`, `marca`, `modelo`, `numero_serie`
- Control: `descripcion_problema`, `accesorios_recibidos`
- Estado adicional agregado en migración posterior

### 2.4 Nivel 3: Diagnóstico Técnico

#### DIAGNOSTICOS (Evaluación del problema)
```
DIAGNOSTICOS
├── Número único: DIAG-000001
├── Problema detectado por técnico
├── Solución propuesta
├── Estado diagnóstico: REPARABLE, NO_REPARABLE, REQUIERE_REPUESTOS
├── Mano de obra requerida
├── Lista de repuestos necesarios
├── Lista de servicios a realizar
└── Genera PRESUPUESTO
```

**Características**:
- Tabla: `servicios.DIAGNOSTICOS`
- Número automático: `DIAG-000001`, `DIAG-000002`...
- Foreign keys: `solicitud_id`, `recepcion_id`, `cliente_id`, `producto_id`
- Estados diagnóstico: `reparable`, `no_reparable`, `requiere_repuestos`
- Estados proceso: `pendiente`, `en_proceso`, `completado`
- Costos: `mano_obra_descripcion`, `mano_obra_costo`
- Auditoría: `creadoPor`, `actualizadoPor`

#### DIAGNOSTICO_REPUESTOS (Repuestos necesarios)
```
DIAGNOSTICO_REPUESTOS
├── Tabla intermedia diagnóstico → productos
├── Cantidad requerida de cada repuesto
├── Costo unitario del repuesto
└── Vincula con stock.PRODUCTOS
```

**Características**:
- Tabla: `servicios.DIAGNOSTICO_REPUESTOS`
- Foreign keys: `diagnostico_id`, `producto_id` → stock.PRODUCTOS
- Campos: `cantidad`, `costo`
- Cascade delete: se elimina con el diagnóstico

#### DIAGNOSTICO_TIPOS_SERVICIO (Servicios a realizar)
```
DIAGNOSTICO_TIPOS_SERVICIO
├── Tabla intermedia diagnóstico → tipos de servicio
├── Cantidad de cada tipo de servicio
├── Costo unitario y subtotal
└── Vincula con TIPOS_SERVICIO
```

**Características**:
- Tabla: `servicios.DIAGNOSTICO_TIPOS_SERVICIO`
- Foreign keys: `diagnostico_id`, `tipo_servicio_id`
- Campos: `cantidad`, `costo_unitario`, `subtotal`
- Cascade delete: se elimina con el diagnóstico

#### TIPOS_SERVICIO (Catálogo de servicios)
```
TIPOS_SERVICIO
├── Código único por tipo de servicio
├── Descripción del servicio
├── Costo estándar
├── Ejemplos: DESMONTAJE, REPARACION, LIMPIEZA, INSTALACION
└── Catálogo maestro
```

**Características**:
- Tabla: `servicios.TIPOS_SERVICIO`
- Campos: `codigo` (único), `descripcion`, `costo`
- Estado: `activo`
- Auditoría: `creadoPor`, `actualizadoPor`

### 2.5 Nivel 4: Presupuestación

#### PRESUPUESTOS (Cotización para el cliente)
```
PRESUPUESTOS
├── Código único: PRE-000001
├── Basado en DIAGNOSTICO
├── Subtotal de servicios (mano de obra)
├── Subtotal de repuestos
├── Descuentos por promoción
├── Descuentos adicionales
├── Monto total
├── Estados: PENDIENTE_APROBACION → APROBADO / RECHAZADO
└── Si aprobado → genera ORDEN_SERVICIO
```

**Características**:
- Tabla: `servicios.PRESUPUESTOS`
- Código automático: `PRE-000001`, `PRE-000002`...
- Foreign keys: `diagnostico_id`, `promocion_id`, `descuento_id`
- Totales: `subtotal_servicios`, `total_servicios`
- Totales: `subtotal_repuestos`, `total_repuestos`
- Descuentos: `descuento_promocion`, `descuento_descuento`
- Total: `monto_total`
- Estados: `pendiente_aprobacion`, `aprobado`, `rechazado`

#### PROMOCIONES (Descuentos por temporada)
```
PROMOCIONES
├── Código único
├── Tipos: GENERAL, SERVICIO, PRODUCTO
├── Porcentaje de descuento
├── Vigencia: fecha_inicio → fecha_fin
└── Aplicable en presupuestos
```

**Características**:
- Tabla: `servicios.promociones`
- Campos: `codigo`, `nombre`, `tipo`, `descuento`
- Vigencia: `fecha_inicio`, `fecha_fin`
- Tipos: `general`, `servicio`, `producto`
- Estado: `activo`

#### DESCUENTOS (Descuentos adicionales)
```
DESCUENTOS
├── Descuentos especiales
├── Cliente frecuente
├── Volumen
└── Excepcionales (autorizados)
```

**Características**:
- Tabla: `servicios.descuentos`
- Estructura similar a promociones
- Aplicación manual en presupuesto

### 2.6 Nivel 5: Ejecución del Servicio

#### ORDENES_SERVICIO (Trabajo asignado)
```
ORDENES_SERVICIO
├── Código único: ORD-SRV-000001
├── Generada desde PRESUPUESTO aprobado
├── Técnico asignado
├── Estados: PENDIENTE → EN_PROCESO → PAUSADA → FINALIZADA → ENTREGADA
├── Fechas: inicio, finalización, entrega
├── Progreso: 0-100%
└── Al finalizar → FACTURACIÓN (módulo Ventas)
```

**Características**:
- Tabla: `servicios.ORDENES_SERVICIO`
- Código automático: `ORD-SRV-000001`, `ORD-SRV-000002`...
- Foreign keys: `presupuesto_id`, `tecnico_id` → users
- Estados: `pendiente`, `en_proceso`, `pausada`, `finalizada`, `entregada`, `cancelada`
- Fechas: `fecha_inicio`, `fecha_finalizacion`, `fecha_entrega`
- Progreso: integer 0-100
- Auditoría: `creadoPor`, `actualizadoPor`

### 2.7 Nivel 6: Post-Servicio

#### RECLAMOS (Gestión de quejas)
```
RECLAMOS
├── Código único: RCL-000001
├── Cliente presenta reclamo post-servicio
├── Tipos: CALIDAD_SERVICIO, DEMORA_ENTREGA, FALLA_POST_SERVICIO, etc.
├── Prioridad: BAJA, MEDIA, ALTA, URGENTE
├── Estados: PENDIENTE → EN_REVISION → EN_PROCESO → RESUELTO → CERRADO
├── Canal: PRESENCIAL, TELEFONO, EMAIL, WHATSAPP, WEB
├── Responsable asignado
└── Solución y cierre
```

**Características**:
- Tabla: `servicios.RECLAMOS`
- Código automático: `RCL-000001`, `RCL-000002`...
- Foreign keys: `cliente_id`, `orden_servicio_id`, `responsable_id`
- Tipos: `calidad_servicio`, `demora_entrega`, `falla_post_servicio`, `atencion_cliente`, `costo_facturacion`, `otro`
- Prioridades: `baja`, `media`, `alta`, `urgente`
- Estados: `pendiente`, `en_revision`, `en_proceso`, `resuelto`, `cerrado`, `rechazado`
- Canales: `presencial`, `telefono`, `email`, `whatsapp`, `web`
- Solución: `solucion`, `fecha_resolucion`, `fecha_cierre`

---

## 3. RELACIONES CRUCIALES CON OTROS MÓDULOS

### 3.1 Relación con STOCK

```
servicios.RECEPCIONES.producto_id → stock.PRODUCTOS.id
servicios.DIAGNOSTICOS.producto_id → stock.PRODUCTOS.id
servicios.DIAGNOSTICO_REPUESTOS.producto_id → stock.PRODUCTOS.id
```

- **Propósito**: Identificar equipos recibidos y repuestos necesarios
- **Implicación**: Control de inventario de repuestos
- **Cascade**: `onDelete('restrict')` - No permitir eliminar producto con servicios
- **Nota**: Los repuestos generan movimiento de stock al facturar (módulo Ventas)

### 3.2 Relación con VENTAS

```
servicios.CLIENTES ← ventas.FACTURAS.cliente_id
servicios.ORDENES_SERVICIO → ventas.FACTURAS (tipo SERVICIOS)
```

- **Propósito**: Facturar servicios técnicos + repuestos
- **Flujo**: Diagnóstico → Presupuesto → Orden → Factura
- **Implicación**:
  - Factura incluye servicios (FACTURAS_SERVICIOS)
  - Factura incluye repuestos (FACTURAS_DETALLE)
  - Los repuestos generan movimiento de stock
  - Cliente unificado en ambos módulos

### 3.3 Relación con EMPRESA

```
servicios.CLIENTES (compartido con ventas) → sucursales, depósitos
```

- **Propósito**: Ubicación física del servicio
- **Implicación**: Trazabilidad de dónde se realizó el servicio

### 3.4 Relación con USERS (Autenticación)

```
servicios.ORDENES_SERVICIO.tecnico_id → public.users.id
servicios.RECLAMOS.responsable_id → public.users.id
```

- **Propósito**: Asignación de técnicos y responsables
- **Implicación**: Control de quién realiza cada servicio

---

## 4. JERARQUÍA DE CARPETAS Y ARCHIVOS

### 4.1 Estructura Livewire (Lógica de Negocio)

```
app/Livewire/Servicios/
├── ReclamosIndex.php              # Listado de reclamos
├── ReclamosManager.php            # Gestión de reclamos (alta/edición)
└── (Otros componentes pendientes de implementación)
```

### 4.2 Estructura de Modelos (Datos y Relaciones)

```
app/Models/Servicios/
├── Cliente.php                    # Modelo principal de clientes
├── SolicitudServicio.php          # Solicitudes de servicio
├── Recepcion.php                  # Recepciones de equipos
├── Diagnostico.php                # Diagnósticos técnicos
├── DiagnosticoRepuesto.php        # Repuestos del diagnóstico
├── DiagnosticoTipoServicio.php    # Servicios del diagnóstico
├── TipoServicio.php               # Catálogo de tipos de servicio
├── Presupuesto.php                # Presupuestos
├── Promocion.php                  # Promociones
├── Descuento.php                  # Descuentos
├── OrdenServicio.php              # Órdenes de servicio
└── Reclamo.php                    # Reclamos
```

### 4.3 Estructura de Vistas (Interfaz de Usuario)

#### 4.3.1 Vistas Contenedoras (AdminLTE Layout)

```
resources/views/servicios/
├── clientes/
│   ├── form.blade.php             # Formulario de cliente
│   └── index.blade.php            # Listado de clientes
│
├── solicitudes/
│   ├── form.blade.php             # Formulario de solicitud
│   └── index.blade.php            # Listado de solicitudes
│
├── recepciones/
│   ├── form.blade.php             # Formulario de recepción
│   └── index.blade.php            # Listado de recepciones
│
├── diagnosticos/
│   ├── form.blade.php             # Formulario de diagnóstico
│   └── index.blade.php            # Listado de diagnósticos
│
├── presupuestos/
│   ├── form.blade.php             # Formulario de presupuesto
│   └── index.blade.php            # Listado de presupuestos
│
├── ordenes/
│   ├── form.blade.php             # Formulario de orden
│   ├── index.blade.php            # Listado de órdenes
│   └── show.blade.php             # Vista detalle de orden
│
└── reclamos/
    ├── form.blade.php             # Formulario de reclamo
    └── index.blade.php            # Listado de reclamos
```

#### 4.3.2 Vistas de Componentes Livewire (Reactivas)

```
resources/views/livewire/servicios/
├── reclamos-index.blade.php       # Tabla reactiva de reclamos
├── reclamos-manager.blade.php     # Form reactivo de reclamos
└── (Otros componentes pendientes)
```

### 4.4 Estructura de Migraciones

```
database/migrations/
├── 2025_12_20_204153_create_servicios_schema.php
├── 2025_12_20_205157_create_clientes_table.php
├── 2025_12_20_214635_create_solicitudes_servicio_table.php
├── 2025_12_22_110439_create_recepciones_table.php
├── 2025_12_22_113704_add_estado_to_recepciones_table.php
├── 2025_12_22_130017_create_diagnosticos_table.php
├── 2025_12_22_140000_add_mano_obra_to_diagnosticos.php
├── 2025_12_22_150000_create_tipos_servicio_table.php
├── 2025_12_22_170644_update_tipos_servicio_codigos.php
├── 2025_12_22_180000_create_diagnostico_tipos_servicio_table.php
├── 2025_12_23_011808_create_promocions_table.php
├── 2025_12_23_013448_create_descuentos_table.php
├── 2025_12_23_101210_create_presupuestos_table.php
└── servicios/
    ├── 2025_12_26_210751_create_ordenes_servicio_table.php
    └── 2025_12_27_114352_create_reclamos_table.php
```

---

## 5. FLUJOS DE TRABAJO PRINCIPALES

### 5.1 Flujo Completo: Servicio de Reparación

```
1. SOLICITUD
   ├── Cliente solicita reparación de bomba
   ├── Se registra SOLICITUD_SERVICIO
   ├── Tipo: REPARACION
   ├── Prioridad: MEDIA o ALTA (según urgencia)
   └── Estado: PENDIENTE

2. RECEPCIÓN
   ├── Cliente trae equipo físicamente
   ├── Se registra RECEPCION
   ├── Datos técnicos: marca, modelo, serie
   ├── Estado del equipo: REGULAR o MALO
   ├── Problema reportado
   ├── Accesorios recibidos
   └── Se entrega comprobante al cliente

3. DIAGNÓSTICO
   ├── Técnico evalúa el equipo
   ├── Crea DIAGNOSTICO
   ├── Problema detectado (técnico)
   ├── Solución propuesta
   ├── Agrega TIPOS_SERVICIO necesarios (desmontaje, reparación, limpieza)
   ├── Agrega REPUESTOS necesarios (rodete, sello, o-rings)
   ├── Define mano de obra
   └── Estado: REQUIERE_REPUESTOS o REPARABLE

4. PRESUPUESTO
   ├── Sistema genera PRESUPUESTO automáticamente
   ├── Calcula: servicios + repuestos + mano de obra
   ├── Aplica promociones si hay vigentes
   ├── Aplica descuentos si corresponde
   ├── Monto total calculado
   ├── Se envía al cliente (email, WhatsApp)
   └── Estado: PENDIENTE_APROBACION

5. APROBACIÓN
   ├── Cliente aprueba presupuesto
   ├── Presupuesto → estado APROBADO
   ├── Sistema genera ORDEN_SERVICIO automáticamente
   └── Orden → estado PENDIENTE

6. EJECUCIÓN
   ├── Supervisor asigna técnico
   ├── Técnico inicia trabajo
   ├── Orden → estado EN_PROCESO
   ├── Actualiza progreso: 25%, 50%, 75%
   ├── Técnico finaliza trabajo
   ├── Orden → estado FINALIZADA
   └── Fecha finalización registrada

7. FACTURACIÓN
   ├── Se carga Orden en FACTURA (módulo Ventas)
   ├── Tipo facturación: SERVICIOS
   ├── Se cargan automáticamente:
   │   ├── FACTURAS_SERVICIOS (tipos de servicio)
   │   └── FACTURAS_DETALLE (repuestos)
   ├── Observer: Genera MOVIMIENTOS_STOCK (solo repuestos)
   ├── Observer: Genera MOVIMIENTOS_CAJA o CUENTA_POR_COBRAR
   └── Cliente paga

8. ENTREGA
   ├── Cliente retira equipo reparado
   ├── Firma recepción conforme
   ├── Orden → estado ENTREGADA
   ├── Fecha entrega registrada
   └── Se entrega garantía del servicio

9. POST-SERVICIO (Opcional)
   ├── Si cliente tiene problema posterior
   ├── Registra RECLAMO
   ├── Tipo: FALLA_POST_SERVICIO
   ├── Se asigna responsable
   ├── Se evalúa reclamo
   └── Se resuelve (nuevo servicio o devolución)
```

### 5.2 Flujo Alternativo: Servicio de Mantenimiento Preventivo

```
1. SOLICITUD
   ├── Cliente agenda mantenimiento
   ├── Tipo: MANTENIMIENTO
   ├── Prioridad: BAJA (no es urgente)

2. RECEPCIÓN
   ├── Cliente trae equipo en fecha acordada
   ├── Estado: BUENO (solo mantenimiento)

3. DIAGNÓSTICO
   ├── Revisión general
   ├── NO requiere repuestos (solo limpieza/ajuste)
   ├── Estado: REPARABLE

4. PRESUPUESTO
   ├── Solo mano de obra + servicios (limpieza, lubricación)
   ├── Sin repuestos
   ├── Cliente aprueba

5-8. Ejecución, Facturación, Entrega
   (Similar al flujo completo)
```

### 5.3 Flujo de Rechazo: Equipo No Reparable

```
1-2. SOLICITUD y RECEPCIÓN
   (Normal)

3. DIAGNÓSTICO
   ├── Técnico evalúa
   ├── Problema: motor quemado, costo de reparación > costo equipo nuevo
   ├── Estado: NO_REPARABLE
   └── Recomendación: comprar equipo nuevo

4. PRESUPUESTO
   ├── Se cobra solo diagnóstico
   ├── Monto mínimo
   ├── Cliente aprueba o rechaza

5. FACTURACIÓN
   ├── Se factura solo el diagnóstico
   └── Cliente paga

6. DEVOLUCIÓN
   ├── Se devuelve equipo sin reparar
   ├── Cliente firma recepción
   └── Se ofrece venta de equipo nuevo (módulo Ventas)
```

---

## 6. CONSIDERACIONES ESPECIALES PARA PARAGUAY

### 6.1 Aspectos Comerciales

- **Garantías**: Servicios técnicos incluyen garantía de 30-90 días
- **Responsabilidad**: Comprobante de recepción protege legalmente
- **Precios**: En Guaraníes (PYG), posible cotización en USD
- **Facturación**: Integrada con módulo Ventas (timbrados SET)
- **Plazos**: Reparaciones típicas 3-7 días hábiles

### 6.2 Prácticas del Rubro Aguatería

- **Servicios comunes**:
  - Reparación de bombas de agua
  - Instalación de tanques
  - Mantenimiento de sistemas de riego
  - Reparación de hidroneumáticos
  - Limpieza de filtros

- **Repuestos críticos**:
  - Rodetes (impulsores)
  - Sellos mecánicos
  - O-rings y empaquetaduras
  - Motores eléctricos
  - Válvulas check

- **Tipos de clientes**:
  - Residenciales (casas, departamentos)
  - Comerciales (edificios, locales)
  - Agrícolas (sistemas de riego)
  - Industriales (fábricas)

### 6.3 Particularidades Locales

- **Urgencias**: Común tener servicios urgentes (falta de agua)
- **Garantía de trabajo**: 30 días mínimo por ley
- **Horarios extendidos**: Algunos servicios 24/7
- **Delivery de repuestos**: Si falta repuesto, se busca y entrega
- **Presupuestos**: Cliente puede rechazar y buscar segunda opinión

---

## 7. INTEGRACIÓN CON MÓDULOS EXISTENTES

### 7.1 Integración con STOCK

#### Puntos de Conexión
```
1. DIAGNOSTICO_REPUESTOS → stock.PRODUCTOS
2. Validación de disponibilidad de repuestos
3. Al facturar: genera MOVIMIENTOS_STOCK (SALIDA_VENTA)
```

#### Flujo Integrado
```
Diagnóstico → Agregar repuestos →
  ├── Verificar stock disponible
  ├── Si hay stock → agregar a presupuesto
  ├── Si NO hay stock → marcar como "pendiente pedido"
  ├── Presupuesto aprobado → reservar repuestos
  └── Facturación → genera movimiento de stock automático
```

### 7.2 Integración con VENTAS

#### Puntos de Conexión
```
1. servicios.CLIENTES ← ventas.FACTURAS.cliente_id (mismo cliente)
2. servicios.ORDENES_SERVICIO → ventas.FACTURAS (facturación de servicios)
3. Historial unificado por cliente
```

#### Flujo Integrado
```
Orden Servicio → Finalizada →
  ├── Crear FACTURA en módulo Ventas
  ├── Tipo facturación: SERVICIOS
  ├── Cargar automáticamente:
  │   ├── Servicios → FACTURAS_SERVICIOS
  │   └── Repuestos → FACTURAS_DETALLE
  ├── Cliente: desde servicios.CLIENTES
  ├── Emitir factura con timbrado SET
  ├── Generar movimientos de stock (repuestos)
  └── Generar movimientos de caja o cuenta por cobrar
```

### 7.3 Integración con EMPRESA

#### Puntos de Conexión
```
1. Sucursales: ubicación donde se realizó el servicio
2. Depósitos: origen de repuestos
3. Usuarios: técnicos y responsables
```

---

## 8. MODELO LÓGICO DE DATOS

### 8.1 Diagrama Entidad-Relación (Texto)

```
[CLIENTES] 1----N [SOLICITUDES_SERVICIO] 1----1 [RECEPCIONES]
    |                      |                           |
    |                      |                           1
    |                      |                           |
    |                      └───────────┬───────────────┘
    |                                  N
    |                            [DIAGNOSTICOS]
    |                                  |
    |                    ┌─────────────┼─────────────┐
    |                    │             │             │
    |                    N             N             │
    |          [DIAGNOSTICO_        [DIAGNOSTICO_    │
    |           REPUESTOS]           TIPOS_SERVICIO] │
    |                    |                   |        │
    |                    |                   N        │
    |                    N                   |        │
    |          [stock.PRODUCTOS]    [TIPOS_SERVICIO] │
    |                                                 1
    |                                                 |
    |                                          [PRESUPUESTOS]
    |                                                 |
    |                                    ┌────────────┼────────────┐
    |                                    N            N            1
    |                              [PROMOCIONES] [DESCUENTOS]      |
    |                                                               N
    |                                                    [ORDENES_SERVICIO]
    |                                                               |
    └──────────────────────────────────────┐                       1
                                           |                       |
                                           N                       N
                                        [RECLAMOS]         [ventas.FACTURAS]
                                                                   |
                                                      ┌────────────┴────────────┐
                                                      |                         |
                                                      N                         N
                                              [FACTURAS_SERVICIOS]    [FACTURAS_DETALLE]
```

### 8.2 Tablas del Schema `servicios`

#### Tabla: CLIENTES
```sql
id                          BIGSERIAL PRIMARY KEY
tipo_cliente                ENUM('fisica', 'juridica')
documento                   VARCHAR(20) UNIQUE
nombre                      VARCHAR(200)
telefono                    VARCHAR(20)
celular                     VARCHAR(20)
email                       VARCHAR(100)
direccion                   TEXT
observaciones               TEXT
activo                      BOOLEAN DEFAULT true
creadoPor                   BIGINT → users
actualizadoPor              BIGINT → users
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: SOLICITUDES_SERVICIO
```sql
id                          BIGSERIAL PRIMARY KEY
numero_solicitud            VARCHAR(20) UNIQUE           # SOL-000001
fecha                       DATE
cliente_id                  BIGINT → CLIENTES
producto_id                 BIGINT → stock.PRODUCTOS
tipo_servicio               ENUM('mantenimiento', 'reparacion', 'diagnostico')
prioridad                   ENUM('baja', 'media', 'alta')
estado                      ENUM('pendiente', 'en_proceso', 'completado')
observaciones               TEXT
activo                      BOOLEAN DEFAULT true
creadoPor                   BIGINT → users
actualizadoPor              BIGINT → users
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: RECEPCIONES
```sql
id                          BIGSERIAL PRIMARY KEY
numero_recepcion            VARCHAR(20) UNIQUE           # REC-000001
fecha_recepcion             DATE
solicitud_id                BIGINT → SOLICITUDES_SERVICIO
cliente_id                  BIGINT → CLIENTES
producto_id                 BIGINT → stock.PRODUCTOS
contacto_cliente            VARCHAR
tipo_equipo                 VARCHAR
marca                       VARCHAR
modelo                      VARCHAR
numero_serie                VARCHAR
estado_recepcion            ENUM('bueno', 'regular', 'malo')
descripcion_problema        TEXT
accesorios_recibidos        TEXT
estado                      VARCHAR (agregado posteriormente)
activo                      BOOLEAN DEFAULT true
creadoPor                   BIGINT → users
actualizadoPor              BIGINT → users
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: DIAGNOSTICOS
```sql
id                          BIGSERIAL PRIMARY KEY
numero_diagnostico          VARCHAR(20) UNIQUE           # DIAG-000001
fecha_diagnostico           DATE
solicitud_id                BIGINT → SOLICITUDES_SERVICIO
recepcion_id                BIGINT → RECEPCIONES
cliente_id                  BIGINT → CLIENTES
producto_id                 BIGINT → stock.PRODUCTOS
problema_detectado          TEXT
solucion_propuesta          TEXT
mano_obra_descripcion       TEXT
mano_obra_costo             DECIMAL(15,2)
estado_diagnostico          ENUM('reparable', 'no_reparable', 'requiere_repuestos')
estado                      ENUM('pendiente', 'en_proceso', 'completado')
observaciones               TEXT
activo                      BOOLEAN DEFAULT true
creadoPor                   BIGINT
actualizadoPor              BIGINT
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: DIAGNOSTICO_REPUESTOS
```sql
id                          BIGSERIAL PRIMARY KEY
diagnostico_id              BIGINT → DIAGNOSTICOS (CASCADE)
producto_id                 BIGINT → stock.PRODUCTOS
cantidad                    INTEGER
costo                       DECIMAL(10,2)
created_at, updated_at      TIMESTAMP
```

#### Tabla: TIPOS_SERVICIO
```sql
id                          BIGSERIAL PRIMARY KEY
codigo                      VARCHAR(20) UNIQUE
descripcion                 VARCHAR(255)
costo                       DECIMAL(15,2)
activo                      BOOLEAN DEFAULT true
creadoPor                   BIGINT
actualizadoPor              BIGINT
created_at, updated_at, deleted_at TIMESTAMP
```

#### Tabla: DIAGNOSTICO_TIPOS_SERVICIO
```sql
id                          BIGSERIAL PRIMARY KEY
diagnostico_id              BIGINT → DIAGNOSTICOS (CASCADE)
tipo_servicio_id            BIGINT → TIPOS_SERVICIO
cantidad                    INTEGER DEFAULT 1
costo_unitario              DECIMAL(15,2)
subtotal                    DECIMAL(15,2)
created_at, updated_at      TIMESTAMP
```

#### Tabla: PROMOCIONES
```sql
id                          BIGSERIAL PRIMARY KEY
codigo                      VARCHAR(20) UNIQUE
nombre                      VARCHAR
tipo                        ENUM('general', 'servicio', 'producto')
descuento                   DECIMAL(5,2)
fecha_inicio                DATE
fecha_fin                   DATE
descripcion                 TEXT
activo                      BOOLEAN DEFAULT true
creadoPor                   BIGINT → users
actualizadoPor              BIGINT → users
created_at, updated_at      TIMESTAMP
```

#### Tabla: descuentos
```sql
(Estructura similar a PROMOCIONES)
```

#### Tabla: PRESUPUESTOS
```sql
id                          BIGSERIAL PRIMARY KEY
codigo                      VARCHAR(20) UNIQUE           # PRE-000001
fecha_presupuesto           DATE
diagnostico_id              BIGINT → DIAGNOSTICOS (CASCADE)
promocion_id                BIGINT → promociones
descuento_id                BIGINT → descuentos
subtotal_servicios          DECIMAL(15,2) DEFAULT 0
descuento_promocion         DECIMAL(15,2) DEFAULT 0
total_servicios             DECIMAL(15,2) DEFAULT 0
subtotal_repuestos          DECIMAL(15,2) DEFAULT 0
descuento_descuento         DECIMAL(15,2) DEFAULT 0
total_repuestos             DECIMAL(15,2) DEFAULT 0
monto_total                 DECIMAL(15,2) DEFAULT 0
estado                      ENUM('pendiente_aprobacion', 'aprobado', 'rechazado')
observaciones               TEXT
activo                      BOOLEAN DEFAULT true
creadoPor                   BIGINT → users
actualizadoPor              BIGINT → users
created_at, updated_at      TIMESTAMP
```

#### Tabla: ORDENES_SERVICIO
```sql
id                          BIGSERIAL PRIMARY KEY
codigo                      VARCHAR(20) UNIQUE           # ORD-SRV-000001
fecha_orden                 DATE
presupuesto_id              BIGINT → PRESUPUESTOS (CASCADE)
tecnico_id                  BIGINT → users
estado                      ENUM('pendiente', 'en_proceso', 'pausada',
                                 'finalizada', 'entregada', 'cancelada')
fecha_inicio                TIMESTAMP
fecha_finalizacion          TIMESTAMP
fecha_entrega               TIMESTAMP
progreso                    INTEGER DEFAULT 0            # 0-100
observaciones               TEXT
activo                      BOOLEAN DEFAULT true
creadoPor                   BIGINT → users
actualizadoPor              BIGINT → users
created_at, updated_at      TIMESTAMP
```

#### Tabla: RECLAMOS
```sql
id                          BIGSERIAL PRIMARY KEY
codigo                      VARCHAR(20) UNIQUE           # RCL-000001
fecha_reclamo               DATE
cliente_id                  BIGINT → CLIENTES (CASCADE)
orden_servicio_id           BIGINT → ORDENES_SERVICIO
tipo_reclamo                ENUM('calidad_servicio', 'demora_entrega',
                                 'falla_post_servicio', 'atencion_cliente',
                                 'costo_facturacion', 'otro')
prioridad                   ENUM('baja', 'media', 'alta', 'urgente')
estado                      ENUM('pendiente', 'en_revision', 'en_proceso',
                                 'resuelto', 'cerrado', 'rechazado')
canal_recepcion             ENUM('presencial', 'telefono', 'email',
                                 'whatsapp', 'web')
descripcion                 TEXT
responsable_id              BIGINT → users
solucion                    TEXT
fecha_resolucion            TIMESTAMP
fecha_cierre                TIMESTAMP
creadoPor                   BIGINT → users
actualizadoPor              BIGINT → users
created_at, updated_at      TIMESTAMP
```

---

## 9. ESTRATEGIA DE IMPLEMENTACIÓN

### 9.1 Fase 1: Estructura Base (COMPLETADO)

```
1. Schema servicios creado
2. Migraciones de clientes, solicitudes, recepciones
3. Modelos base con relaciones
4. Componentes Livewire de reclamos (parcial)
```

### 9.2 Fase 2: Diagnóstico y Catálogos (COMPLETADO)

```
1. Migraciones de diagnósticos y tablas relacionadas
2. Tabla TIPOS_SERVICIO (catálogo)
3. Tablas intermedias (repuestos, servicios)
4. Modelos con relaciones HasMany/BelongsTo
5. Agregado de mano de obra en diagnósticos
```

### 9.3 Fase 3: Presupuestación (COMPLETADO)

```
1. Migraciones de presupuestos
2. Tablas de promociones y descuentos
3. Modelo Presupuesto con cálculos automáticos
4. Relaciones con diagnóstico
```

### 9.4 Fase 4: Órdenes de Servicio (COMPLETADO)

```
1. Migración de ordenes_servicio
2. Modelo OrdenServicio con relaciones
3. Estados y progreso
4. Asignación de técnicos
```

### 9.5 Fase 5: Reclamos (COMPLETADO)

```
1. Migración de reclamos
2. Modelo Reclamo completo
3. Livewire de gestión de reclamos
4. Tipos, prioridades y canales
```

### 9.6 Fase 6: Interfaces Livewire (PENDIENTE)

```
1. Componentes de clientes (CRUD)
2. Componentes de solicitudes (CRUD)
3. Componentes de recepciones (CRUD + comprobante)
4. Componentes de diagnósticos (CRUD + cálculos)
5. Componentes de presupuestos (visualización + aprobación)
6. Componentes de órdenes (asignación + seguimiento)
7. Mejora de componentes de reclamos
```

### 9.7 Fase 7: Reportes (FUTURO)

```
1. Reporte de servicios por período
2. Reporte de servicios por técnico
3. Reporte de servicios por cliente
4. Reporte de reclamos
5. Estadísticas de tiempos de servicio
6. Análisis de rentabilidad por tipo de servicio
```

### 9.8 Fase 8: Integraciones Avanzadas (FUTURO)

```
1. Generación de PDF de órdenes de trabajo
2. Generación de PDF de contratos de servicio
3. Notificaciones por email/WhatsApp
4. Sistema de garantías
5. Portal de cliente (consulta de estado)
```

---

## 10. CHECKLIST DE ÉXITO

### 10.1 Técnico

- [x] Schema `servicios` creado y configurado
- [x] Migraciones de clientes ejecutadas
- [x] Migraciones de solicitudes y recepciones ejecutadas
- [x] Migraciones de diagnósticos ejecutadas
- [x] Migraciones de tipos de servicio ejecutadas
- [x] Migraciones de presupuestos ejecutadas
- [x] Migraciones de promociones y descuentos ejecutadas
- [x] Migraciones de órdenes de servicio ejecutadas
- [x] Migraciones de reclamos ejecutadas
- [x] Modelos con relaciones implementados
- [ ] Observers configurados (para movimientos de stock)
- [x] Componentes Livewire de reclamos operativos (parcial)
- [ ] Componentes Livewire completos (solicitudes, recepciones, diagnósticos, etc.)
- [ ] Vistas Blade con AdminLTE integrado
- [ ] Rutas agrupadas y protegidas
- [ ] Permisos configurados en seeder
- [ ] Menú AdminLTE actualizado

### 10.2 Funcional

- [ ] Alta de clientes funcional
- [ ] Alta de solicitudes de servicio funcional
- [ ] Recepción de equipos operativa
- [ ] Diagnóstico técnico funcional
- [ ] Cálculo de presupuestos automático
- [ ] Aprobación de presupuestos operativa
- [ ] Generación de órdenes de servicio automática
- [ ] Asignación de técnicos funcional
- [ ] Seguimiento de progreso operativo
- [ ] Facturación de servicios integrada con Ventas
- [x] Gestión de reclamos funcional (básica)
- [ ] Reportes generando información útil

### 10.3 Operativo

- [ ] Usuarios pueden crear solicitudes
- [ ] Usuarios pueden recepcionar equipos
- [ ] Técnicos pueden crear diagnósticos
- [ ] Sistema calcula presupuestos automáticamente
- [ ] Clientes pueden aprobar presupuestos
- [ ] Sistema genera órdenes automáticamente
- [ ] Técnicos pueden actualizar progreso
- [ ] Facturación de servicios genera movimientos de stock
- [ ] Reclamos se gestionan correctamente
- [ ] Sistema responde en tiempos aceptables
- [ ] Data real puede procesarse

### 10.4 Integración

- [x] Integración con módulo STOCK definida (repuestos)
- [x] Integración con módulo VENTAS definida (facturación)
- [x] Integración con módulo EMPRESA definida (sucursales, usuarios)
- [ ] Facturación de servicios genera movimientos de stock
- [ ] Cliente unificado entre Servicios y Ventas
- [ ] Reportes integrados con otros módulos

---

## RESUMEN EJECUTIVO

Este modelo conceptual proporciona:

- Visión completa del módulo Servicios desde arquitectura hasta operación
- Jerarquía clara de entidades y sus relaciones (7 niveles)
- Estructura detallada de archivos y carpetas
- Flujos de trabajo completos para servicios técnicos
- Consideraciones específicas para el mercado paraguayo de aguatería
- Integración completa con módulos existentes (Stock, Ventas, Empresa)
- Modelo lógico de datos detallado (12 tablas principales)
- Ruta de implementación faseada y realista

El módulo está diseñado para ser:

- **Completo**: Cubre todo el ciclo de servicio técnico
- **Integrado**: Conexión natural con stock y facturación
- **Escalable**: Fácil expansión a garantías y portal de cliente
- **Práctico**: Resuelve necesidades reales de servicios de aguatería
- **Paraguayo**: Considera particularidades locales
- **Trazable**: Control completo desde solicitud hasta post-venta

---

**Fecha de documentación**: Diciembre 2025
**Versión del sistema**: SIGEA v4
**Framework**: Laravel 12.26.4 / PHP 8.4.15 / PostgreSQL
**Módulos relacionados**: Stock, Ventas, Empresa
**Estado**: En desarrollo (fases 1-5 completadas, faltan interfaces Livewire)
