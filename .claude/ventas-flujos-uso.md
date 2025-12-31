# FLUJOS DE USO DETALLADOS - MÓDULO VENTAS
## Sistema Integrado de Gestión de Ventas - SIGEA v4

---

## ÍNDICE
1. [Flujo 1: Venta Completa (Pedido → Factura → Entrega → Cobro)](#flujo-1-venta-completa)
2. [Flujo 2: Venta Directa al Contado](#flujo-2-venta-directa-al-contado)
3. [Flujo 3: Facturación de Servicios Técnicos](#flujo-3-facturación-de-servicios-técnicos)
4. [Flujo 4: Gestión de Caja Diaria](#flujo-4-gestión-de-caja-diaria)
5. [Flujo 5: Cobranza de Facturas a Crédito](#flujo-5-cobranza-de-facturas-a-crédito)
6. [Flujo 6: Devolución de Productos (Nota de Crédito)](#flujo-6-devolución-de-productos)
7. [Flujo 7: Consulta de Libro de Ventas](#flujo-7-consulta-de-libro-de-ventas)

---

## FLUJO 1: Venta Completa (Pedido → Factura → Entrega → Cobro)

### Contexto
Cliente solicita productos para una instalación de sistema de agua. Requiere varios productos, algunos en stock y otros a pedido. La venta es a crédito 30 días.

### Diagrama de Flujo

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    INICIO: Cliente solicita productos                   │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 1: CREACIÓN DE PEDIDO                                              │
│ Ubicación: Ventas → Pedidos de Clientes → Nuevo Pedido                 │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.1 Buscar Cliente   │
                    │ - Escribir nombre    │
                    │ - Seleccionar        │
                    │ - Auto-completa      │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.2 Datos del Pedido │
                    │ - Fecha pedido       │
                    │ - Tipo entrega       │
                    │ - Dirección (si DELIVERY)│
                    │ - Observaciones      │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.3 Agregar Productos│
                    │ - Buscar producto    │
                    │ - Ver stock actual   │
                    │ - Agregar cantidad   │
                    │ - Ver precio         │
                    │ - Repetir por c/prod.│
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.4 Revisar Totales  │
                    │ - Subtotal           │
                    │ - IVA 10% / 5%       │
                    │ - Total              │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.5 Guardar Pedido   │
                    │ Estado: BORRADOR     │
                    │ Número: PC-000123    │
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 2: CONFIRMACIÓN DE PEDIDO                                          │
│ Ubicación: Ventas → Pedidos → Ver Pedido PC-000123                     │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 2.1 Verificar Stock  │
                    │ - ¿Hay stock?        │
                    └──────────┬───────────┘
                               │
                    ┌──────────┴──────────┐
                    │                     │
              ✅ SÍ hay stock      ❌ NO hay stock
                    │                     │
                    ▼                     ▼
         ┌──────────────────┐  ┌──────────────────────┐
         │ Estado:          │  │ Contactar proveedor  │
         │ CONFIRMADO       │  │ Esperar ingreso      │
         │ (puede facturar) │  │ Luego: CONFIRMADO    │
         └─────┬────────────┘  └──────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 3: PREPARACIÓN DE PEDIDO                                           │
│ Ubicación: Depósito (acción física)                                    │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 3.1 Recoger Productos│
                    │ - Según lista pedido │
                    │ - Verificar cantidad │
                    │ - Verificar estado   │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 3.2 Marcar LISTO     │
                    │ Estado: LISTO_ENTREGAR│
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 4: FACTURACIÓN                                                     │
│ Ubicación: Ventas → Facturas → Nueva Factura                           │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 4.1 Abrir Caja       │
                    │ (si no está abierta) │
                    │ - Saldo inicial      │
                    │ - Punto expedición   │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 4.2 Buscar Pedido    │
                    │ - Escribir PC-000123 │
                    │ - O buscar por cliente│
                    │ - Seleccionar pedido │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 4.3 Carga Automática │
                    │ - Cliente            │
                    │ - Productos          │
                    │ - Cantidades         │
                    │ - Precios            │
                    │ - Totales            │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 4.4 Condición Pago   │
                    │ Seleccionar: CREDITO │
                    │ Cuotas: 1 (30 días)  │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 4.5 Guardar Borrador │
                    │ Estado: BORRADOR     │
                    │ (revisar antes emit.)│
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 4.6 EMITIR FACTURA   │
                    │ [BOTÓN EMITIR]       │
                    └──────────┬───────────┘
                               │
                               ▼
            ┌───────────────────────────────────────┐
            │ ACCIONES AUTOMÁTICAS AL EMITIR:       │
            ├───────────────────────────────────────┤
            │ ✅ Genera número: 001-001-0000123     │
            │ ✅ Estado → EMITIDA                   │
            │ ✅ Crea MOVIMIENTOS_STOCK por c/prod  │
            │    (SALIDA_VENTA, actualiza stock)    │
            │ ✅ Crea CUENTA_POR_COBRAR             │
            │    (monto, vencimiento 30 días)       │
            │ ✅ Crea FACTURA_CUOTA                 │
            │    (1 cuota, vence en 30 días)        │
            │ ✅ Pedido → estado FACTURADO          │
            └────────────────┬──────────────────────┘
                             │
                             ▼
                  ┌──────────────────────┐
                  │ 4.7 Imprimir Factura │
                  │ [BOTÓN IMPRIMIR]     │
                  │ PDF con formato legal│
                  └──────────┬───────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 5: ENTREGA (Opcional: Generar Remisión)                           │
│ Ubicación: Ventas → Remisiones → Nueva Remisión                        │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 5.1 Desde Factura    │
                    │ - Seleccionar factura│
                    │ - Carga automática   │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 5.2 Datos Entrega    │
                    │ - Dirección          │
                    │ - Fecha entrega      │
                    │ - Responsable        │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 5.3 Emitir Remisión  │
                    │ Número: REM-000089   │
                    │ Estado: EMITIDA      │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 5.4 Entrega Física   │
                    │ - Chofer lleva rem.  │
                    │ - Cliente firma      │
                    │ - Registrar receptor │
                    │ Estado: ENTREGADA    │
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 6: COBRO (30 días después)                                        │
│ Ubicación: Ventas → Cuentas por Cobrar                                 │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 6.1 Listar Cuentas   │
                    │ - Filtrar: PENDIENTE │
                    │ - Buscar cliente     │
                    │ - Ver fact. 001-001..│
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 6.2 Registrar Pago   │
                    │ [BOTÓN PAGAR]        │
                    │ - Monto pagado       │
                    │ - Forma pago: EFECTIVO│
                    │ - Referencia (opcional)│
                    └──────────┬───────────┘
                               │
                               ▼
            ┌───────────────────────────────────────┐
            │ ACCIONES AUTOMÁTICAS AL PAGAR:        │
            ├───────────────────────────────────────┤
            │ ✅ Actualiza CUENTA_POR_COBRAR        │
            │    (monto_pagado, saldo_pendiente)    │
            │ ✅ Actualiza FACTURA_CUOTA            │
            │    (estado → PAGADA)                  │
            │ ✅ Crea MOVIMIENTO_CAJA               │
            │    (INGRESO_COBRO)                    │
            │ ✅ Factura → estado PAGADA            │
            └────────────────┬──────────────────────┘
                             │
                             ▼
                  ┌──────────────────────┐
                  │ 6.3 Imprimir Recibo  │
                  │ Comprobante de pago  │
                  └──────────┬───────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │   FIN FLUJO    │
                    │  ✅ Venta      │
                    │     Completada │
                    └────────────────┘
```

### Pantallas Involucradas

1. **Pedido Cliente Form** ([pedido-cliente-form.blade.php](app/Livewire/Ventas/PedidoClienteForm.php))
   - Búsqueda de cliente
   - Búsqueda de productos
   - Cálculo de totales
   - Validación de stock

2. **Factura Form** ([factura-form.blade.php](app/Livewire/Ventas/FacturaForm.php))
   - Carga desde pedido
   - Selección de condición de pago
   - Cálculo de cuotas
   - Emisión de factura

3. **Remisión Form** ([remision-form.blade.php](app/Livewire/Ventas/RemisionForm.php))
   - Carga desde factura
   - Datos de entrega
   - Registro de receptor

4. **Pagar Cuenta** ([pagar-cuenta.blade.php](app/Livewire/Ventas/PagarCuenta.php))
   - Listado de cuentas pendientes
   - Registro de pago
   - Selección de forma de pago

### Entidades Afectadas

- ✅ PEDIDOS_CLIENTES
- ✅ PEDIDOS_CLIENTES_DETALLE
- ✅ FACTURAS
- ✅ FACTURAS_DETALLE
- ✅ FACTURAS_CUOTAS
- ✅ CUENTAS_POR_COBRAR
- ✅ PAGOS_CUENTAS_POR_COBRAR
- ✅ REMISIONES
- ✅ REMISIONES_DETALLE
- ✅ MOVIMIENTOS_STOCK (schema: stock)
- ✅ MOVIMIENTOS_CAJA

---

## FLUJO 2: Venta Directa al Contado

### Contexto
Cliente entra al local, selecciona productos y paga al contado. No requiere pedido previo.

### Diagrama de Flujo

```
┌─────────────────────────────────────────────────────────────────────────┐
│                INICIO: Cliente en mostrador con productos               │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 1: VERIFICAR CAJA ABIERTA                                          │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                    ┌──────────┴──────────┐
                    │                     │
              ✅ Caja ABIERTA      ❌ Caja CERRADA
                    │                     │
                    │                     ▼
                    │          ┌──────────────────────┐
                    │          │ 1.1 Abrir Caja       │
                    │          │ - Saldo inicial      │
                    │          │ - Punto expedición   │
                    │          │ - Usuario            │
                    │          └──────────┬───────────┘
                    │                     │
                    └──────────┬──────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 2: CREAR FACTURA DIRECTA                                           │
│ Ubicación: Ventas → Facturas → Nueva Factura                           │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 2.1 Buscar Cliente   │
                    │ - Escribir nombre/doc│
                    │ - Seleccionar        │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 2.2 Agregar Productos│
                    │ (Búsqueda en tiempo  │
                    │  real)               │
                    └──────────┬───────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ POR CADA PRODUCTO:                     │
        ├────────────────────────────────────────┤
        │ ▸ Buscar por código o nombre          │
        │ ▸ Sistema muestra:                     │
        │   - Código                             │
        │   - Descripción                        │
        │   - Stock actual                       │
        │   - Precio de venta                    │
        │ ▸ Ingresar cantidad                    │
        │ ▸ Sistema calcula:                     │
        │   - Subtotal                           │
        │   - IVA (10% o 5%)                     │
        │   - Total línea                        │
        │ ▸ [BOTÓN AGREGAR]                      │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
                    ┌──────────────────────┐
                    │ 2.3 Revisar Totales  │
                    │ - Subtotal productos │
                    │ - IVA 10%            │
                    │ - IVA 5%             │
                    │ - TOTAL A PAGAR      │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 2.4 Condición Pago   │
                    │ Fijo: CONTADO        │
                    │ (no permite CREDITO) │
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 3: REGISTRAR FORMAS DE PAGO                                        │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ 3.1 Agregar Forma de Pago              │
        ├────────────────────────────────────────┤
        │ Seleccionar:                           │
        │ ○ EFECTIVO                             │
        │ ○ TARJETA_DEBITO                       │
        │ ○ TARJETA_CREDITO                      │
        │ ○ TRANSFERENCIA                        │
        │ ○ CHEQUE                               │
        │ ○ GIROS                                │
        │                                        │
        │ Ingresar:                              │
        │ - Monto: ________________              │
        │ - Referencia: ___________              │
        │   (núm. transacción, cheque, etc.)     │
        │                                        │
        │ [BOTÓN AGREGAR FORMA PAGO]             │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
                ┌──────────────────────┐
                │ 3.2 Validar Total    │
                │ ¿Pagado = Total?     │
                └──────────┬───────────┘
                           │
                ┌──────────┴──────────┐
                │                     │
          ✅ Sí igualan        ❌ No igualan
                │                     │
                │                     ▼
                │          ┌──────────────────────┐
                │          │ ERROR: Debe pagar    │
                │          │ el total exacto      │
                │          │ (volver a 3.1)       │
                │          └──────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 4: EMITIR FACTURA                                                  │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 4.1 [BOTÓN EMITIR]   │
                    │ Sistema procesa...   │
                    └──────────┬───────────┘
                               │
                               ▼
            ┌───────────────────────────────────────┐
            │ ACCIONES AUTOMÁTICAS:                 │
            ├───────────────────────────────────────┤
            │ ✅ Genera número: 001-001-0000124     │
            │ ✅ Estado → EMITIDA                   │
            │ ✅ Por cada producto:                 │
            │    - Crea MOVIMIENTO_STOCK            │
            │      (SALIDA_VENTA)                   │
            │    - Actualiza stock_actual           │
            │ ✅ Por cada forma de pago:            │
            │    - Crea MOVIMIENTO_CAJA             │
            │      (INGRESO_VENTA)                  │
            │ ✅ Factura → estado PAGADA            │
            │    (ya que es CONTADO)                │
            └────────────────┬──────────────────────┘
                             │
                             ▼
                  ┌──────────────────────┐
                  │ 4.2 Imprimir Factura │
                  │ PDF automático       │
                  │ - Original (cliente) │
                  │ - Copia (local)      │
                  └──────────┬───────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 5: ENTREGA AL CLIENTE                                              │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 5.1 Cliente recibe:  │
                    │ ✓ Productos          │
                    │ ✓ Factura impresa    │
                    │ ✓ Garantía (si aplica)│
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌────────────────┐
                    │   FIN FLUJO    │
                    │ ✅ Venta al    │
                    │    Contado     │
                    │    Completada  │
                    └────────────────┘
```

### Tiempo Estimado
⏱️ **3-5 minutos** (desde cliente en mostrador hasta entrega)

### Pantallas Involucradas
- **Factura Form** (modo directo, sin pedido previo)
- **Apertura Caja** (si caja no está abierta)

### Entidades Afectadas
- ✅ FACTURAS
- ✅ FACTURAS_DETALLE
- ✅ FACTURAS_FORMAS_PAGO
- ✅ MOVIMIENTOS_STOCK
- ✅ MOVIMIENTOS_CAJA

---

## FLUJO 3: Facturación de Servicios Técnicos

### Contexto
Cliente trajo bomba de agua para reparación. Ya se diagnosticó, aprobó presupuesto, se realizó el servicio. Ahora debe facturarse.

### Diagrama de Flujo

```
┌─────────────────────────────────────────────────────────────────────────┐
│          INICIO: Servicio finalizado, listo para facturar              │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PREREQUISITOS (Módulo Servicios)                                        │
│ ✅ Diagnóstico creado (con tipos de servicio)                           │
│ ✅ Presupuesto aprobado                                                 │
│ ✅ Orden de Servicio finalizada (estado: FINALIZADA)                    │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 1: CREAR FACTURA DE SERVICIO                                      │
│ Ubicación: Ventas → Facturas → Nueva Factura                           │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.1 Abrir Caja       │
                    │ (si no está abierta) │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.2 Tipo Facturación │
                    │ Seleccionar:         │
                    │ ⦿ SERVICIOS          │
                    │ ○ PEDIDOS            │
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 2: BUSCAR ORDEN DE SERVICIO                                        │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 2.1 Buscar por:      │
                    │ - Código orden (OS-) │
                    │ - Cliente            │
                    │ - Documento          │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 2.2 Seleccionar OS   │
                    │ Ejemplo: OS-000456   │
                    │ Cliente: Juan Pérez  │
                    │ Monto: Gs 850.000    │
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 3: CARGA AUTOMÁTICA DE SERVICIOS Y REPUESTOS                      │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ 3.1 SERVICIOS (de Diagnóstico)         │
        ├────────────────────────────────────────┤
        │ Sistema carga automáticamente:         │
        │                                        │
        │ TABLA DE SERVICIOS:                    │
        │ ┌──────┬───────────┬────┬──────┬──────┐│
        │ │Código│ Servicio  │Cant│Precio│Total ││
        │ ├──────┼───────────┼────┼──────┼──────┤│
        │ │SRV-01│Desmontaje │ 1  │50.000│50.000││
        │ │SRV-02│Reparación │ 1  │200000│200000││
        │ │SRV-03│Limpieza   │ 1  │30.000│30.000││
        │ └──────┴───────────┴────┴──────┴──────┘│
        │                 Subtotal: Gs 280.000   │
        │                 IVA 10%:  Gs  28.000   │
        │                 Total:    Gs 308.000   │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
        ┌────────────────────────────────────────┐
        │ 3.2 REPUESTOS (de Diagnóstico)         │
        ├────────────────────────────────────────┤
        │ Sistema carga automáticamente:         │
        │                                        │
        │ TABLA DE REPUESTOS:                    │
        │ ┌──────┬───────────┬────┬──────┬──────┐│
        │ │Código│ Repuesto  │Cant│Precio│Total ││
        │ ├──────┼───────────┼────┼──────┼──────┤│
        │ │P-123 │Rodete     │ 1  │180000│180000││
        │ │P-456 │Sello mecán│ 2  │ 45000│ 90000││
        │ │P-789 │O-ring     │ 4  │  5000│ 20000││
        │ └──────┴───────────┴────┴──────┴──────┘│
        │                 Subtotal: Gs 290.000   │
        │                 IVA 10%:  Gs  29.000   │
        │                 Total:    Gs 319.000   │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
        ┌────────────────────────────────────────┐
        │ 3.3 TOTALES GENERALES                  │
        ├────────────────────────────────────────┤
        │ Subtotal Servicios:   Gs 280.000       │
        │ Subtotal Repuestos:   Gs 290.000       │
        │ ─────────────────────────────────      │
        │ Subtotal Total:       Gs 570.000       │
        │ IVA 10%:              Gs  57.000       │
        │ ═════════════════════════════════      │
        │ TOTAL A PAGAR:        Gs 627.000       │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 4: CONDICIÓN DE PAGO                                               │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 4.1 Seleccionar      │
                    │ ○ CONTADO            │
                    │ ○ CREDITO            │
                    └──────────┬───────────┘
                               │
                    ┌──────────┴──────────┐
                    │                     │
              CONTADO                CREDITO
                    │                     │
                    ▼                     ▼
         ┌──────────────────┐  ┌──────────────────────┐
         │ Registrar formas │  │ Cantidad de cuotas   │
         │ de pago          │  │ - Calcular cuotas    │
         │ (paso 5)         │  │ - Fechas vencimiento │
         └─────┬────────────┘  └──────────┬───────────┘
               │                          │
               └──────────┬───────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 5: REGISTRAR FORMAS DE PAGO (Solo si CONTADO)                     │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 5.1 Agregar Pagos    │
                    │ - Forma: EFECTIVO    │
                    │ - Monto: 627.000     │
                    │ [AGREGAR]            │
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 6: EMITIR FACTURA                                                  │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 6.1 [BOTÓN EMITIR]   │
                    └──────────┬───────────┘
                               │
                               ▼
            ┌───────────────────────────────────────┐
            │ ACCIONES AUTOMÁTICAS:                 │
            ├───────────────────────────────────────┤
            │ ✅ Genera número factura              │
            │ ✅ Guarda FACTURAS_SERVICIOS          │
            │    (tipos de servicio realizados)     │
            │ ✅ Guarda FACTURAS_DETALLE            │
            │    (repuestos utilizados)             │
            │ ✅ Por cada repuesto:                 │
            │    - Crea MOVIMIENTO_STOCK            │
            │      (SALIDA_VENTA)                   │
            │    - Actualiza stock_actual           │
            │ ✅ Si CONTADO:                        │
            │    - Crea MOVIMIENTO_CAJA             │
            │      (INGRESO_VENTA)                  │
            │ ✅ Si CREDITO:                        │
            │    - Crea CUENTA_POR_COBRAR           │
            │    - Crea FACTURA_CUOTA               │
            │ ✅ Orden Servicio → FACTURADA         │
            └────────────────┬──────────────────────┘
                             │
                             ▼
                  ┌──────────────────────┐
                  │ 6.2 Imprimir Factura │
                  │ Con detalle de:      │
                  │ - Servicios          │
                  │ - Repuestos          │
                  │ - Totales            │
                  └──────────┬───────────┘
                             │
                             ▼
                  ┌──────────────────────┐
                  │ 6.3 Entregar equipo  │
                  │ Cliente firma recibo │
                  │ Orden → ENTREGADA    │
                  └──────────┬───────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │   FIN FLUJO    │
                    │ ✅ Servicio    │
                    │    Facturado   │
                    └────────────────┘
```

### Particularidades

1. **NO genera movimiento de stock** para servicios (solo para repuestos)
2. **Tabla dual**: FACTURAS_SERVICIOS + FACTURAS_DETALLE
3. **IVA siempre 10%** en servicios (Paraguay)
4. **Garantía del servicio** se registra en Orden de Servicio, no en factura

### Pantallas Involucradas
- **Factura Form** (modo SERVICIOS)
- Búsqueda de Órdenes de Servicio
- Visualización de servicios y repuestos

### Entidades Afectadas
- ✅ FACTURAS
- ✅ FACTURAS_SERVICIOS
- ✅ FACTURAS_DETALLE (solo repuestos)
- ✅ MOVIMIENTOS_STOCK (solo repuestos)
- ✅ MOVIMIENTOS_CAJA o CUENTAS_POR_COBRAR
- ✅ ORDENES_SERVICIO (schema: servicios) - actualiza estado

---

## FLUJO 4: Gestión de Caja Diaria

### Contexto
Cajero inicia su jornada laboral, realiza ventas durante el día y cierra caja al final.

### Diagrama de Flujo

```
┌─────────────────────────────────────────────────────────────────────────┐
│              INICIO: Cajero llega a trabajar (7:00 AM)                  │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 1: APERTURA DE CAJA                                                │
│ Ubicación: Ventas → Caja → Abrir Caja                                  │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.1 Datos Apertura   │
                    │ - Usuario: Auto      │
                    │ - Punto Exp: CAJA 01 │
                    │ - Fecha: Hoy         │
                    │ - Hora: Auto         │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.2 Contar Efectivo  │
                    │ Saldo inicial:       │
                    │ Gs 500.000           │
                    │ (fondo fijo)         │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.3 [ABRIR CAJA]     │
                    │ Estado: ABIERTA      │
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 2: OPERACIONES DEL DÍA (7:00 AM - 6:00 PM)                        │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ 2.1 INGRESOS (Automáticos)             │
        ├────────────────────────────────────────┤
        │ Por cada venta al contado:             │
        │ ✅ FACTURA emitida →                   │
        │    ↳ MOVIMIENTO_CAJA automático        │
        │      tipo: INGRESO_VENTA               │
        │      monto: total factura              │
        │      forma_pago: según registro        │
        │                                        │
        │ Por cada cobro de cuenta por cobrar:   │
        │ ✅ PAGO registrado →                   │
        │    ↳ MOVIMIENTO_CAJA automático        │
        │      tipo: INGRESO_COBRO               │
        │      monto: monto pagado               │
        │      forma_pago: según registro        │
        │                                        │
        │ Ejemplo acumulado al mediodía:         │
        │ - 15 facturas: Gs 4.250.000            │
        │ - 3 cobros: Gs 1.800.000               │
        │ Total ingresos: Gs 6.050.000           │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
        ┌────────────────────────────────────────┐
        │ 2.2 EGRESOS (Manuales)                 │
        ├────────────────────────────────────────┤
        │ Ubicación: Ventas → Caja → Movimientos│
        │                                        │
        │ Gastos operativos:                     │
        │ - Compra de insumos (limpieza)         │
        │   Tipo: EGRESO_GASTO                   │
        │   Monto: Gs 50.000                     │
        │                                        │
        │ - Pago de delivery                     │
        │   Tipo: EGRESO_GASTO                   │
        │   Monto: Gs 100.000                    │
        │                                        │
        │ Retiros de caja:                       │
        │ - Depósito bancario (seguridad)        │
        │   Tipo: EGRESO_RETIRO                  │
        │   Monto: Gs 2.000.000                  │
        │                                        │
        │ Devoluciones:                          │
        │ - Nota de crédito (automático)         │
        │   Tipo: EGRESO_DEVOLUCION              │
        │   Monto: según nota                    │
        │                                        │
        │ Total egresos: Gs 2.150.000            │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
        ┌────────────────────────────────────────┐
        │ 2.3 ARQUEO INTERMEDIO (Opcional)       │
        │ Ubicación: Ventas → Caja → Arqueo      │
        ├────────────────────────────────────────┤
        │ A las 12:00 (mediodía)                 │
        │                                        │
        │ Saldo sistema: Gs 4.400.000            │
        │ (inicial + ingresos - egresos)         │
        │                                        │
        │ Contar efectivo físico: Gs 4.380.000   │
        │                                        │
        │ Diferencia: Gs 20.000 (faltante)       │
        │ ⚠️ Registrar y justificar              │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 3: CIERRE DE CAJA (6:00 PM)                                        │
│ Ubicación: Ventas → Caja → Cerrar Caja                                 │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ 3.1 CÁLCULOS AUTOMÁTICOS               │
        │ Sistema calcula:                       │
        ├────────────────────────────────────────┤
        │ Saldo Inicial:        Gs   500.000     │
        │                                        │
        │ INGRESOS:                              │
        │ - Efectivo ventas:    Gs 5.200.000     │
        │ - Tarjetas ventas:    Gs 2.050.000     │
        │ - Efectivo cobros:    Gs 1.800.000     │
        │ - Otros ingresos:     Gs   100.000     │
        │ Subtotal ingresos:    Gs 9.150.000     │
        │                                        │
        │ EGRESOS:                               │
        │ - Gastos:             Gs   150.000     │
        │ - Retiros:            Gs 2.000.000     │
        │ - Devoluciones:       Gs    80.000     │
        │ Subtotal egresos:     Gs 2.230.000     │
        │                                        │
        │ ═════════════════════════════════      │
        │ SALDO TEÓRICO:        Gs 7.420.000     │
        │ ═════════════════════════════════      │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
                    ┌──────────────────────┐
                    │ 3.2 CONTEO FÍSICO    │
                    │ Cajero cuenta:       │
                    └──────────┬───────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ Efectivo en caja:                      │
        │ ┌──────────────┬──────────┬───────────┐│
        │ │ Denominación │ Cantidad │   Total   ││
        │ ├──────────────┼──────────┼───────────┤│
        │ │ 100.000      │    30    │ 3.000.000 ││
        │ │  50.000      │    45    │ 2.250.000 ││
        │ │  20.000      │    10    │   200.000 ││
        │ │  10.000      │     8    │    80.000 ││
        │ │   5.000      │    12    │    60.000 ││
        │ │   2.000      │    15    │    30.000 ││
        │ │   1.000      │    20    │    20.000 ││
        │ │ Monedas      │     -    │       500 ││
        │ └──────────────┴──────────┴───────────┘│
        │                                        │
        │ TOTAL EFECTIVO:       Gs 5.640.500     │
        │                                        │
        │ Tarjetas (vouchers):  Gs 2.050.000     │
        │ (ya depositadas)                       │
        │                                        │
        │ ═════════════════════════════════      │
        │ TOTAL FÍSICO:         Gs 7.690.500     │
        │ ═════════════════════════════════      │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
                    ┌──────────────────────┐
                    │ 3.3 COMPARACIÓN      │
                    │ Sistema vs Físico    │
                    └──────────┬───────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ Saldo Teórico:    Gs 7.420.000         │
        │ Saldo Físico:     Gs 7.690.500         │
        │ ─────────────────────────────────      │
        │ Diferencia:       Gs   270.500         │
        │                   (SOBRANTE)           │
        │                                        │
        │ Estado: ⚠️ DIFERENCIA                  │
        │ (requiere justificación)               │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
                    ┌──────────────────────┐
                    │ 3.4 Observaciones    │
                    │ "Posible error en    │
                    │  registro de forma   │
                    │  de pago en venta X" │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 3.5 [CERRAR CAJA]    │
                    │ - Guarda CIERRE_CAJA │
                    │ - Apertura → CERRADA │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 3.6 Imprimir Reporte │
                    │ - Detalle movimientos│
                    │ - Totales por forma  │
                    │ - Diferencia         │
                    │ - Firmas             │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 3.7 Depositar        │
                    │ - Efectivo a bóveda  │
                    │ - Dejar fondo fijo   │
                    │   (Gs 500.000)       │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌────────────────┐
                    │   FIN FLUJO    │
                    │ ✅ Caja cerrada│
                    │    y cuadrada  │
                    └────────────────┘
```

### Casos Especiales

#### Si hay diferencia FALTANTE:
```
- Cajero debe justificar
- Puede descontar de sueldo (según política)
- Se registra en observaciones
```

#### Si hay diferencia SOBRANTE:
```
- Se investiga origen
- Puede ser error de vuelto
- Se registra en observaciones
- Sobrante va a caja general
```

### Reportes Generados

1. **Reporte de Cierre**
   - Movimientos del día
   - Totales por forma de pago
   - Diferencias
   - Firmas de cajero y supervisor

2. **Reporte para Contabilidad**
   - Ingresos por venta
   - Ingresos por cobros
   - Egresos del día
   - Saldo final

### Pantallas Involucradas
- Apertura Caja Form
- Movimientos Caja (manual)
- Arqueo Caja Form
- Cierre Caja Form

### Entidades Afectadas
- ✅ APERTURAS_CAJA
- ✅ MOVIMIENTOS_CAJA
- ✅ ARQUEOS_CAJA
- ✅ CIERRES_CAJA

---

## FLUJO 5: Cobranza de Facturas a Crédito

### Contexto
Cliente compró a crédito hace 30 días. Hoy vence la factura y viene a pagar.

### Diagrama de Flujo

```
┌─────────────────────────────────────────────────────────────────────────┐
│             INICIO: Cliente viene a pagar factura vencida              │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 1: BUSCAR CUENTA POR COBRAR                                        │
│ Ubicación: Ventas → Cuentas por Cobrar                                 │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.1 Filtrar          │
                    │ - Estado: PENDIENTE  │
                    │ - Cliente: buscar    │
                    │ - Vencidas: Sí       │
                    └──────────┬───────────┘
                               │
                               ▼
        ┌────────────────────────────────────────────────┐
        │ LISTADO DE CUENTAS POR COBRAR:                 │
        ├────────────────────────────────────────────────┤
        │ ┌──────┬─────────┬─────┬────────┬────────┬───┐│
        │ │Fecha │ Factura │Venc.│  Total │ Saldo  │ - ││
        │ ├──────┼─────────┼─────┼────────┼────────┼───┤│
        │ │30/11 │001-0123 │30/12│850.000 │850.000 │🔍││
        │ │15/12 │001-0145 │14/01│450.000 │450.000 │🔍││
        │ │20/12 │001-0156 │19/01│1.200000│1200000 │🔍││
        │ └──────┴─────────┴─────┴────────┴────────┴───┘│
        │                                                │
        │ ⚠️ Primera factura VENCIDA (hoy 31/12)         │
        └───────────────────┬────────────────────────────┘
                            │
                            ▼
                    ┌──────────────────────┐
                    │ 1.2 [VER DETALLE]    │
                    │ Factura 001-0123     │
                    └──────────┬───────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ DETALLE DE CUENTA:                     │
        ├────────────────────────────────────────┤
        │ Cliente: JUAN PÉREZ                    │
        │ CI: 12345678                           │
        │ Factura: 001-001-0000123               │
        │ Fecha emisión: 30/11/2025              │
        │ Vencimiento: 30/12/2025 ⚠️ VENCIDA     │
        │                                        │
        │ Monto total:      Gs 850.000           │
        │ Monto pagado:     Gs       0           │
        │ ═══════════════════════════════        │
        │ SALDO PENDIENTE:  Gs 850.000           │
        │ ═══════════════════════════════        │
        │                                        │
        │ Cuotas:                                │
        │ ┌────┬──────────┬────────┬───────┬───┐│
        │ │Núm │   Venc.  │  Monto │ Estado│ - ││
        │ ├────┼──────────┼────────┼───────┼───┤│
        │ │ 1  │ 30/12/25 │850.000 │VENCIDA│💰││
        │ └────┴──────────┴────────┴───────┴───┘│
        │                                        │
        │ [REGISTRAR PAGO]                       │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 2: REGISTRAR PAGO                                                  │
│ Ubicación: Modal "Registrar Pago"                                      │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ 2.1 DATOS DEL PAGO                     │
        ├────────────────────────────────────────┤
        │ Cuenta: 001-001-0000123                │
        │ Saldo pendiente: Gs 850.000            │
        │                                        │
        │ ┌──────────────────────────────────┐  │
        │ │ Monto a pagar:                   │  │
        │ │ Gs [____________]                │  │
        │ │                                  │  │
        │ │ ○ Pago total (Gs 850.000)        │  │
        │ │ ○ Pago parcial                   │  │
        │ └───────────��──────────────────────┘  │
        │                                        │
        │ ┌──────────────────────────────────┐  │
        │ │ Forma de pago:                   │  │
        │ │ ▼ EFECTIVO                       │  │
        │ │   TARJETA_DEBITO                 │  │
        │ │   TARJETA_CREDITO                │  │
        │ │   TRANSFERENCIA                  │  │
        │ │   CHEQUE                         │  │
        │ └──────────────────────────────────┘  │
        │                                        │
        │ Referencia (opcional):                 │
        │ [_____________________________]        │
        │                                        │
        │ Observaciones:                         │
        │ [_____________________________]        │
        │                                        │
        │ [CANCELAR]  [REGISTRAR PAGO]           │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
                 Ejemplo: Usuario ingresa
                    Monto: Gs 850.000
                    Forma: EFECTIVO
                          │
                          ▼
                    ┌──────────────────────┐
                    │ 2.2 [REGISTRAR PAGO] │
                    │ Sistema procesa...   │
                    └──────────┬───────────┘
                               │
                               ▼
            ┌───────────────────────────────────────┐
            │ ACCIONES AUTOMÁTICAS:                 │
            ├───────────────────────────────────────┤
            │ ✅ Crea PAGO_CUENTA_POR_COBRAR        │
            │    - fecha_pago: hoy                  │
            │    - monto_pagado: 850.000            │
            │    - forma_pago: EFECTIVO             │
            │                                       │
            │ ✅ Actualiza CUENTA_POR_COBRAR        │
            │    - monto_pagado: 850.000            │
            │    - saldo_pendiente: 0               │
            │    - estado: PAGADA                   │
            │                                       │
            │ ✅ Actualiza FACTURA_CUOTA            │
            │    - monto_pagado: 850.000            │
            │    - saldo_pendiente: 0               │
            │    - estado: PAGADA                   │
            │                                       │
            │ ✅ Actualiza FACTURA                  │
            │    - estado: PAGADA                   │
            │                                       │
            │ ✅ Crea MOVIMIENTO_CAJA               │
            │    - tipo: INGRESO_COBRO              │
            │    - monto: 850.000                   │
            │    - forma_pago: EFECTIVO             │
            │    - vinculado a: pago_cuenta_id      │
            └────────────────┬──────────────────────┘
                             │
                             ▼
                  ┌──────────────────────┐
                  │ 2.3 Mensaje de Éxito │
                  │ ✅ Pago registrado   │
                  │    correctamente     │
                  └──────────┬───────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 3: EMITIR COMPROBANTE                                              │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 3.1 Imprimir Recibo  │
                    │ [BOTÓN IMPRIMIR]     │
                    └──────────┬───────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ COMPROBANTE DE PAGO                    │
        │ ════════════════════════════════       │
        │                                        │
        │ Empresa: Mi Ferretería                 │
        │ Fecha: 31/12/2025                      │
        │ Hora: 14:30                            │
        │                                        │
        │ ════════════════════════════════       │
        │ DATOS DEL PAGO                         │
        │ ════════════════════════════════       │
        │                                        │
        │ Cliente: JUAN PÉREZ                    │
        │ CI: 12345678                           │
        │                                        │
        │ Factura: 001-001-0000123               │
        │ Fecha factura: 30/11/2025              │
        │ Total factura: Gs 850.000              │
        │                                        │
        │ Monto pagado: Gs 850.000               │
        │ Forma de pago: EFECTIVO                │
        │                                        │
        │ Saldo pendiente: Gs 0                  │
        │ Estado: PAGADA ✓                       │
        │                                        │
        │ ════════════════════════════════       │
        │                                        │
        │ Cajero: María González                 │
        │ Firma: _______________                 │
        │                                        │
        │ ════════════════════════════════       │
        │      ¡GRACIAS POR SU PAGO!             │
        │ ════════════════════════════════       │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
                    ┌──────────────────────┐
                    │ 3.2 Entregar recibo  │
                    │ al cliente           │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌────────────────┐
                    │   FIN FLUJO    │
                    │ ✅ Pago        │
                    │    Completado  │
                    └────────────────┘
```

### Caso: Pago Parcial

Si el cliente paga solo una parte:

```
Monto a pagar: Gs 500.000 (de 850.000)
                    │
                    ▼
ACCIONES AUTOMÁTICAS:
✅ Crea PAGO_CUENTA_POR_COBRAR (500.000)
✅ Actualiza CUENTA_POR_COBRAR:
   - monto_pagado: 500.000
   - saldo_pendiente: 350.000
   - estado: PARCIALMENTE_PAGADA
✅ Actualiza FACTURA_CUOTA:
   - monto_pagado: 500.000
   - saldo_pendiente: 350.000
   - estado: PARCIALMENTE_PAGADA
✅ Actualiza FACTURA:
   - estado: PARCIALMENTE_PAGADA
✅ Crea MOVIMIENTO_CAJA (500.000)
```

### Alertas de Cobranza

El sistema puede generar alertas automáticas:

```
📧 Email automático:
   - 5 días antes del vencimiento (recordatorio)
   - El día del vencimiento (aviso)
   - 7 días después del vencimiento (mora)
   - 15 días después (mora urgente)
   - 30 días después (derivar a legales)

📱 WhatsApp (si está integrado):
   - Recordatorio cortés
   - Estado de cuenta
   - Link de pago (futuro)
```

### Pantallas Involucradas
- Listado Cuentas por Cobrar
- Detalle de Cuenta
- Modal Registrar Pago
- Comprobante de Pago (PDF)

### Entidades Afectadas
- ✅ CUENTAS_POR_COBRAR
- ✅ PAGOS_CUENTAS_POR_COBRAR
- ✅ FACTURAS_CUOTAS
- ✅ FACTURAS (actualiza estado)
- ✅ MOVIMIENTOS_CAJA

---

## FLUJO 6: Devolución de Productos (Nota de Crédito)

### Contexto
Cliente devuelve producto defectuoso comprado hace 5 días.

### Diagrama de Flujo

```
┌─────────────────────────────────────────────────────────────────────────┐
│          INICIO: Cliente trae producto para devolución                  │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 1: VERIFICACIÓN INICIAL                                            │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.1 Cliente presenta:│
                    │ ✓ Producto           │
                    │ ✓ Factura original   │
                    │ ✓ Embalaje (si aplica)│
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 1.2 Verificar estado │
                    │ - ¿Defecto real?     │
                    │ - ¿Mal uso?          │
                    │ - ¿Garantía vigente? │
                    └──────────┬───────────┘
                               │
                    ┌──────────┴──────────┐
                    │                     │
          ✅ Devolución      ❌ Devolución
             APROBADA            RECHAZADA
                    │                     │
                    │                     ▼
                    │          ┌──────────────────────┐
                    │          │ Explicar motivo al   │
                    │          │ cliente. Ofrecer     │
                    │          │ reparación si aplica │
                    │          │ [FIN]                │
                    │          └──────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 2: BUSCAR FACTURA ORIGINAL                                         │
│ Ubicación: Ventas → Facturas → Buscar                                  │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 2.1 Buscar por:      │
                    │ - Número factura     │
                    │ - Cliente            │
                    │ - Fecha              │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 2.2 Ver detalles     │
                    │ Factura 001-001-0125 │
                    │ Cliente: Pedro Gómez │
                    │ Fecha: 26/12/2025    │
                    │ Total: Gs 450.000    │
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 3: CREAR NOTA DE CRÉDITO                                           │
│ Ubicación: Ventas → Notas de Crédito → Nueva                           │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ 3.1 DATOS DE LA NOTA                   │
        ├────────────────────────────────────────┤
        │ Factura origen: 001-001-0000125        │
        │ Cliente: Pedro Gómez                   │
        │ Fecha emisión: 31/12/2025              │
        │                                        │
        │ Motivo:                                │
        │ ▼ DEVOLUCION                           │
        │   ERROR_FACTURACION                    │
        │   DESCUENTO_POSTERIOR                  │
        │                                        │
        │ Timbrado:                              │
        │ ▼ TIM-NC-001 (vigente)                 │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
        ┌────────────────────────────────────────┐
        │ 3.2 PRODUCTOS A DEVOLVER               │
        ├────────────────────────────────────────┤
        │ Productos de la factura:               │
        │ ┌────┬───────────┬────┬───────┬──────┐│
        │ │Cód │ Producto  │Cant│Precio │ Sel. ││
        │ ├────┼───────────┼────┼───────┼──────┤│
        │ │P123│Motobomba  │ 1  │350000 │  ☑   ││
        │ │P456│Manguera   │ 2  │ 50000 │  ☐   ││
        │ └────┴───────────┴────┴───────┴──────┘│
        │                                        │
        │ ┌────────────────────────────────────┐│
        │ │ Producto: Motobomba 1HP            ││
        │ │ Cantidad facturada: 1              ││
        │ │ Cantidad a devolver: [1]           ││
        │ │ Precio unitario: Gs 350.000        ││
        │ │ Total línea: Gs 350.000            ││
        │ └────────────────────────────────────┘│
        └─────────────────┬──────────────────────┘
                          │
                          ▼
                    ┌──────────────────────┐
                    │ 3.3 Calcular Totales │
                    │ Subtotal: 350.000    │
                    │ IVA 10%:   35.000    │
                    │ Total NC:  385.000   │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 3.4 Guardar BORRADOR │
                    │ [GUARDAR]            │
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 4: EMITIR NOTA DE CRÉDITO                                          │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 4.1 Revisar datos    │
                    │ [BOTÓN EMITIR]       │
                    └──────────┬───────────┘
                               │
                               ▼
            ┌───────────────────────────────────────┐
            │ ACCIONES AUTOMÁTICAS:                 │
            ├───────────────────────────────────────┤
            │ ✅ Genera número: NC-001-001-0000015  │
            │ ✅ Estado → EMITIDA                   │
            │                                       │
            │ ✅ Por cada producto devuelto:        │
            │    - Crea MOVIMIENTO_STOCK            │
            │      (ENTRADA_DEVOLUCION)             │
            │    - Actualiza stock_actual (+1)      │
            │    - Ubicación: DEVOLUCIONES          │
            │      (para inspección)                │
            │                                       │
            │ ✅ Si factura era CONTADO:            │
            │    - Crea MOVIMIENTO_CAJA             │
            │      (EGRESO_DEVOLUCION)              │
            │    - Monto: 385.000                   │
            │    - O genera vale para nueva compra  │
            │                                       │
            │ ✅ Si factura era CREDITO:            │
            │    - Actualiza CUENTA_POR_COBRAR      │
            │      (reduce saldo pendiente)         │
            │    - Si saldo = 0 → estado PAGADA     │
            │                                       │
            │ ✅ Vincula NC con factura original    │
            └────────────────┬──────────────────────┘
                             │
                             ▼
                  ┌──────────────────────┐
                  │ 4.2 Imprimir NC      │
                  │ PDF con formato legal│
                  └──────────┬───────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 5: COMPENSACIÓN AL CLIENTE                                         │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ 5.1 Opciones:        │
                    └──────────┬───────────┘
                               │
                    ┌──────────┴──────────┬──────────────────┐
                    │                     │                  │
              Devolución            Vale para         Cambio por
              de Efectivo         nueva compra      otro producto
                    │                     │                  │
                    ▼                     ▼                  ▼
         ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
         │ Si pago CONTADO: │  │ - Genera vale    │  │ - Genera factura │
         │ - Devolver       │  │ - Válido 90 días │  │   por diferencia │
         │   Gs 385.000     │  │ - Puede aplicar  │  │ - NC compensa    │
         │ - Registrar      │  │   en próxima     │  │   factura vieja  │
         │   egreso caja    │  │   compra         │  │ - Cliente paga   │
         │ - Recibo firmado │  └──────────────────┘  │   diferencia     │
         └─────┬────────────┘                        └──────────┬─────────┘
               │                                                │
               └────────────────┬───────────────────────────────┘
                                │
                                ▼
                     ┌──────────────────────┐
                     │ 5.2 Producto devuelto│
                     │ va a:                │
                     │ - Depósito DEVOL.    │
                     │ - Inspección técnica │
                     │ - Decisión:          │
                     │   ∙ Reparar          │
                     │   ∙ Descartar        │
                     │   ∙ Devolver a prov. │
                     └──────────┬───────────┘
                                │
                                ▼
                     ┌────────────────┐
                     │   FIN FLUJO    │
                     │ ✅ Devolución  │
                     │    Procesada   │
                     └────────────────┘
```

### Validaciones Importantes

```
⚠️ VALIDAR ANTES DE EMITIR NC:

1. Plazo de devolución (ej: 30 días)
2. Estado del producto (no maltratado)
3. Embalaje original (si aplica)
4. Accesorios completos
5. Garantía vigente
6. Factura original válida
7. Producto no discontinuado
8. Cliente identificado
```

### Políticas Comunes

```
✅ ACEPTAR DEVOLUCIÓN:
- Defecto de fábrica
- Error en entrega (producto equivocado)
- Error de facturación
- Dentro del plazo (7-30 días)

❌ NO ACEPTAR DEVOLUCIÓN:
- Mal uso evidente
- Producto dañado por cliente
- Fuera de plazo
- Sin embalaje original (si es requisito)
- Productos de higiene personal
- Productos cortados a medida
```

### Pantallas Involucradas
- Buscar Factura
- Formulario Nota de Crédito
- Detalle de Nota de Crédito
- Comprobante NC (PDF)

### Entidades Afectadas
- ✅ NOTAS_CREDITO_VENTAS
- ✅ NOTAS_CREDITO_DETALLE (tabla relacionada)
- ✅ MOVIMIENTOS_STOCK (ENTRADA_DEVOLUCION)
- ✅ MOVIMIENTOS_CAJA (si contado) o CUENTAS_POR_COBRAR (si crédito)
- ✅ FACTURAS (vinculación)

---

## FLUJO 7: Consulta de Libro de Ventas

### Contexto
Contador o gerente necesita consultar las ventas del mes para presentar a tributación o análisis.

### Diagrama de Flujo

```
┌─────────────────────────────────────────────────────────────────────────┐
│           INICIO: Usuario necesita reporte de ventas                   │
└──────────────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 1: ACCEDER AL LIBRO DE VENTAS                                      │
│ Ubicación: Ventas → Reportes → Libro de Ventas                         │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ 1.1 FILTROS DE BÚSQUEDA                │
        ├────────────────────────────────────────┤
        │                                        │
        │ Rango de fechas:                       │
        │ Desde: [01/12/2025] Hasta: [31/12/2025]│
        │                                        │
        │ Sucursal:                              │
        │ ▼ Todas las sucursales                 │
        │   Sucursal Central                     │
        │   Sucursal Zona Norte                  │
        │                                        │
        │ Punto de Expedición:                   │
        │ ▼ Todos los puntos                     │
        │   CAJA 01                              │
        │   CAJA 02                              │
        │                                        │
        │ Estado:                                │
        │ ☑ Emitidas                             │
        │ ☐ Borradores                           │
        │ ☐ Anuladas                             │
        │                                        │
        │ Condición de pago:                     │
        │ ▼ Todas                                │
        │   Solo CONTADO                         │
        │   Solo CREDITO                         │
        │                                        │
        │ Timbrado:                              │
        │ ▼ Todos                                │
        │   TIM-001 (vigente)                    │
        │                                        │
        │ [LIMPIAR]  [BUSCAR]                    │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 2: VISUALIZAR RESULTADOS                                           │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
        ┌────────────────────────────────────────────────────────────────┐
        │ LIBRO DE VENTAS - DICIEMBRE 2025                               │
        │ Período: 01/12/2025 - 31/12/2025                               │
        │ ════════════════════════════════════════════════════════════   │
        │                                                                │
        │ RESUMEN:                                                       │
        │ ┌──────────────────────┬─────────────────────────────────────┐│
        │ │ Total Facturas:      │ 342                                 ││
        │ │ Facturas Contado:    │ 215 (Gs 87.500.000)                 ││
        │ │ Facturas Crédito:    │ 127 (Gs 145.200.000)                ││
        │ ├──────────────────────┼─────────────────────────────────────┤│
        │ │ Subtotal (10%):      │ Gs 185.000.000                      ││
        │ │ IVA 10%:             │ Gs  18.500.000                      ││
        │ │ Subtotal (5%):       │ Gs  12.500.000                      ││
        │ │ IVA 5%:              │ Gs     625.000                      ││
        │ │ Exentas:             │ Gs   1.200.000                      ││
        │ ├──────────────────────┼─────────────────────────────────────┤│
        │ │ TOTAL VENTAS:        │ Gs 232.700.000                      ││
        │ │ TOTAL IVA:           │ Gs  19.125.000                      ││
        │ └──────────────────────┴─────────────────────────────────────┘│
        │                                                                │
        │ [EXPORTAR EXCEL]  [EXPORTAR PDF]  [IMPRIMIR]                  │
        └───────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
        ┌────────────────────────────────────────────────────────────────┐
        │ DETALLE DE FACTURAS (Tabla paginada)                           │
        │ ════════════════════════════════════════════════════════════   │
        │                                                                │
        │ Mostrando 1-50 de 342  [Anterior] [1][2][3]...[7] [Siguiente] │
        │                                                                │
        │ ┌─────┬────────┬──────────┬─────────┬────────┬──────┬────────┐│
        │ │Fecha│Timbrado│  Número  │ Cliente │Subtotal│ IVA  │ Total  ││
        │ ├─────┼────────┼──────────┼─────────┼────────┼──────┼────────┤│
        │ │01/12│TIM-001 │001-0001  │Juan P.  │450.000 │45.000│495.000 ││
        │ │01/12│TIM-001 │001-0002  │María G. │850.000 │85.000│935.000 ││
        │ │02/12│TIM-001 │001-0003  │Pedro L. │320.000 │32.000│352.000 ││
        │ │02/12│TIM-001 │001-0004  │Ana S.   │1200000│120000│1320000 ││
        │ │...  │...     │...       │...      │...     │...   │...     ││
        │ │31/12│TIM-001 │001-0342  │Luis R.  │680.000 │68.000│748.000 ││
        │ └─────┴────────┴──────────┴─────────┴────────┴──────┴────────┘│
        │                                                                │
        │ [VER DETALLE] por cada fila                                    │
        └───────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 3: EXPORTAR O IMPRIMIR                                             │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
            ┌──────────────────┴────────────────────┐
            │                                       │
       [EXCEL]                                  [PDF]
            │                                       │
            ▼                                       ▼
┌───────────────────────────┐     ┌────────────────────────────┐
│ ARCHIVO EXCEL             │     │ ARCHIVO PDF                │
├───────────────────────────┤     ├────────────────────────────┤
│ Hoja 1: Resumen           │     │ LIBRO DE VENTAS            │
│ - Totales por fecha       │     │ DICIEMBRE 2025             │
│ - Totales por timbrado    │     │                            │
│ - Totales IVA             │     │ Detalle de todas las       │
│                           │     │ facturas emitidas:         │
│ Hoja 2: Detalle Facturas  │     │                            │
│ - Todas las columnas      │     │ [Tabla formateada con      │
│ - Formato para filtrar    │     │  logo, cabecera, totales,  │
│                           │     │  firmas]                   │
│ Hoja 3: Detalle por Prod. │     │                            │
│ - Productos más vendidos  │     │ Página 1 de 15             │
│ - Cantidades              │     │                            │
│                           │     │                            │
│ [DESCARGAR]               │     │ [DESCARGAR] [IMPRIMIR]     │
└───────────────────────────┘     └────────────────────────────┘
            │                                       │
            └──────────────────┬────────────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │ Archivo guardado en: │
                    │ Descargas/           │
                    │ libro_ventas_dic.xlsx│
                    └──────────┬───────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ PASO 4: ANÁLISIS ADICIONAL (Opcional)                                   │
└──────────────────────────────┬──────────────────────────────────────────┘
                               │
                               ▼
        ┌────────────────────────────────────────┐
        │ 4.1 GRÁFICOS Y ESTADÍSTICAS            │
        ├────────────────────────────────────────┤
        │                                        │
        │ [TAB: Resumen] [TAB: Gráficos]         │
        │                                        │
        │ Ventas por día (gráfico de líneas):    │
        │      Gs                                │
        │   15M │       ╱╲                        │
        │   10M │      ╱  ╲    ╱╲                 │
        │    5M │   ╱╲╱    ╲╱╲╱  ╲               │
        │       └─────────────────────> Días     │
        │         1   5   10  15  20  25  30     │
        │                                        │
        │ Ventas por vendedor (gráfico barras):  │
        │ María G.  ████████████ Gs 45M          │
        │ Juan P.   ██████████   Gs 38M          │
        │ Luis R.   ████████     Gs 32M          │
        │ Ana S.    ██████       Gs 25M          │
        │                                        │
        │ Top 10 productos vendidos:             │
        │ 1. Motobomba 1HP       85 unidades     │
        │ 2. Tanque 500L         62 unidades     │
        │ 3. Manguera 1/2"       450 metros      │
        │ ...                                    │
        │                                        │
        │ Forma de pago (pie chart):             │
        │ ○ Efectivo      62%                    │
        │ ○ Tarjetas      28%                    │
        │ ○ Transferencia  7%                    │
        │ ○ Cheques        3%                    │
        └─────────────────┬──────────────────────┘
                          │
                          ▼
                    ┌──────────────────────┐
                    │   FIN FLUJO          │
                    │ ✅ Reporte generado  │
                    │    y exportado       │
                    └──────────────────────┘
```

### Uso Típico: Tributación Paraguay

```
OBLIGACIONES MENSUALES SET:

1. Libro de Ventas
   ├── Todas las facturas emitidas
   ├── Notas de crédito/débito
   ├── Total IVA 10% y 5%
   └── Presentar hasta día 15 del mes siguiente

2. Libro de Compras (módulo Compras)
   ├── Todas las facturas recibidas
   ├── IVA crédito fiscal
   └── Presentar junto con libro ventas

3. Declaración Jurada (Marangatu SET)
   ├── Cargar datos de libros
   ├── Calcular IVA a pagar
   │   (IVA ventas - IVA compras)
   └── Pagar hasta día 20

EXPORTACIÓN PARA SET:
✅ Excel formato específico SET
✅ Campos requeridos: RUC, Timbrado, Fecha, Monto, IVA
✅ Validación de sumas
```

### Pantallas Involucradas
- Libro de Ventas (tabla reactiva con filtros)
- Exportación Excel/PDF
- Gráficos y estadísticas

### Entidades Consultadas
- ✅ FACTURAS
- ✅ FACTURAS_DETALLE
- ✅ TIMBRADOS
- ✅ CLIENTES
- ✅ PRODUCTOS
- ✅ NOTAS_CREDITO_VENTAS
- ✅ NOTAS_DEBITO_VENTAS

---

## RESUMEN DE TIEMPOS PROMEDIO

| Flujo | Tiempo Estimado |
|-------|----------------|
| 1. Venta Completa (Pedido → Factura → Cobro) | 30-45 min (total) |
| 2. Venta Directa al Contado | 3-5 min |
| 3. Facturación de Servicios | 5-10 min |
| 4. Gestión de Caja Diaria | 15-20 min (cierre) |
| 5. Cobranza de Facturas | 2-3 min |
| 6. Devolución de Productos | 10-15 min |
| 7. Consulta Libro de Ventas | 2-5 min |

---

**Documento actualizado**: Diciembre 2025
**Sistema**: SIGEA v4 - Módulo Ventas
**Objetivo**: Guía práctica para operadores del sistema
