# ÍNDICE GENERAL DE DOCUMENTACIÓN - SIGEA V4

**Sistema Integrado de Gestión Empresarial para Aguatería**
**Versión:** 4.0
**Fecha:** Diciembre 2025

---

## 📚 DOCUMENTACIÓN DISPONIBLE

### 1. Arquitectura General
- [arquitectura-proyecto.md](arquitectura-proyecto.md) - Estructura general del proyecto
- [project-structure.md](project-structure.md) - Organización de carpetas
- [buenas-practicas-desarrollo-modulos.md](buenas-practicas-desarrollo-modulos.md) - Guía de desarrollo
- [seeders-estructura.md](seeders-estructura.md) - Datos iniciales
- [modelos-relaciones.md](modelos-relaciones.md) - Relaciones entre modelos

### 2. Módulo EMPRESA (Schema: empresa)
- ✅ [empresa-modelo-conceptual.md](empresa-modelo-conceptual.md) - Modelo conceptual
- ⏳ empresa-flujos-uso.md - Flujos de uso (PENDIENTE)

**Funcionalidad:** Configuración base del sistema (empresa, sucursales, depósitos, timbrados, puntos de expedición)

### 3. Módulo STOCK (Schema: stock)
- ✅ [stocks-modelo-conceptual.md](stocks-modelo-conceptual.md) - Modelo conceptual completo
- ✅ [stocks-plan-implementacion.md](stocks-plan-implementacion.md) - Plan de implementación
- ⏳ stocks-flujos-uso.md - Flujos de uso (PENDIENTE)

**Funcionalidad:** Gestión de inventario (productos, stock por depósito, movimientos, precios, kardex)

### 4. Módulo COMPRAS (Schema: compras)
- ✅ [compras-modulo-completo.md](compras-modulo-completo.md) - Documentación completa
- ✅ [compras-verificacion-final.md](compras-verificacion-final.md) - Verificación
- ⏳ compras-flujos-uso.md - Flujos de uso (PENDIENTE)

**Funcionalidad:** Gestión de compras (proveedores, pedidos, órdenes, facturas, recepción, cuentas por pagar)

### 5. Módulo VENTAS (Schema: ventas)
- ✅ [ventas-modelo-conceptual.md](ventas-modelo-conceptual.md) - Modelo conceptual completo
- ✅ [ventas-flujos-uso.md](ventas-flujos-uso.md) - 7 flujos detallados

**Funcionalidad:** Gestión de ventas (pedidos cliente, facturación, caja, cobranzas, remisiones, libro de ventas)

### 6. Módulo SERVICIOS (Schema: servicios)
- ✅ [servicios-modelo-conceptual.md](servicios-modelo-conceptual.md) - Modelo conceptual completo
- ✅ servicios-flujos-uso.md - Flujos de uso detallados (en archivo existente)

**Funcionalidad:** Servicio técnico (clientes, solicitudes, recepción, diagnóstico, presupuesto, órdenes, reclamos)

### 7. Schema PUBLIC
- ✅ [public-schema.md](public-schema.md) - Documentación completa de autenticación y autorización

**Funcionalidad:** Autenticación (users, sessions), Autorización (roles, permissions con Spatie), Sistema de módulos

---

## 🗺️ MAPA DE INTEGRACIÓN ENTRE MÓDULOS

```
                     ┌──────────────┐
                     │   EMPRESA    │
                     │  (config)    │
                     └──────┬───────┘
                            │
                ┌───────────┼───────────┐
                │           │           │
                ▼           ▼           ▼
         ┌──────────┐ ┌──────────┐ ┌──────────┐
         │  STOCK   │ │ SERVICIOS│ │  VENTAS  │
         │(productos)│ │(clientes)│ │ (factura)│
         └─────┬────┘ └────┬─────┘ └────┬─────┘
               │           │            │
               │           └────┬───────┘
               │                │
               ▼                ▼
         ┌──────────┐    ┌──────────┐
         │ COMPRAS  │    │  PUBLIC  │
         │(proveed.)│    │ (users)  │
         └──────────┘    └──────────┘
```

### Relaciones Clave

**EMPRESA → Todos los módulos**
- Sucursales, Depósitos, Timbrados, Puntos Expedición

**STOCK ↔ COMPRAS**
- Movimientos de stock (ENTRADA_COMPRA)
- Productos comprados

**STOCK ↔ VENTAS**
- Movimientos de stock (SALIDA_VENTA)
- Productos vendidos

**STOCK ↔ SERVICIOS**
- Repuestos en diagnósticos
- Productos en órdenes de servicio

**SERVICIOS → VENTAS**
- Clientes compartidos
- Facturación de servicios técnicos

**PUBLIC → Todos**
- Usuarios: creado_por, actualizado_por
- Roles y permisos por módulo

---

## 📊 ESTADÍSTICAS DEL SISTEMA

### Schemas PostgreSQL
- `public` - Usuarios, roles, permissions
- `empresa` - Configuración base (14 tablas)
- `stock` - Inventario (11 tablas)
- `compras` - Compras y proveedores (15+ tablas)
- `ventas` - Ventas y facturación (20 tablas)
- `servicios` - Servicio técnico (12 tablas)

### Total Aproximado
- **Tablas:** ~72 tablas
- **Modelos Eloquent:** ~70 modelos
- **Componentes Livewire:** ~50 componentes
- **Migraciones:** ~80 migraciones

---

## 🚀 PRÓXIMOS PASOS

### Documentación Pendiente (por orden de prioridad)

1. **stocks-flujos-uso.md** ⏳
2. **compras-flujos-uso.md** ⏳
3. **empresa-flujos-uso.md** ⏳

### Funcionalidades Futuras

- **Facturación Electrónica SET** (Paraguay)
- **Módulo de Reportes Avanzados**
- **Dashboard Gerencial**
- **Módulo de RRHH** (futuro)
- **Módulo de Contabilidad** (futuro)

---

**Mantenido por:** Equipo de Desarrollo SIGEA
**Última actualización:** Diciembre 2025
