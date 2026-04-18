# Estructura Jerárquica de Vistas - SIGEA v4

## 📁 resources/views/

### 🏠 Raíz
- welcome.blade.php
- home.blade.php
- pdf-general.blade.php

---

### 🎨 Layouts
```
layouts/
├── app.blade.php
├── appOld.blade.php
└── pdf/
    ├── plantilla.blade.php
    └── partials/
        ├── encabezado.blade.php
        └── firma.blade.php
```

---

### 🔐 Autenticación (auth/)
```
auth/
├── login.blade.php
├── register.blade.php
├── verify.blade.php
└── passwords/
    ├── confirm.blade.php
    ├── email.blade.php
    └── reset.blade.php
```

---

### 👥 Administración (admin/)

#### Roles
```
admin/roles/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── pdf/
    └── pdf-roles.blade.php
```

#### Usuarios
```
admin/usuarios/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── asignar-rol.blade.php
├── asignar-rol-a-usuarios.blade.php
└── pdf/
    └── pdf-usuarios-listado.blade.php
```

---

### 🛒 Compras (compras/)

#### Dashboard
```
compras/dashboard/
├── index.blade.php
├── flujo.blade.php
└── pdf.blade.php
```

#### Configuración
```
compras/configuracion/
├── index.blade.php
├── flujos-aprobacion.blade.php
└── tipos-documento.blade.php
```

#### Proveedores
```
compras/proveedores/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── show.blade.php
```

#### Presupuestos
```
compras/presupuestos/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
├── comparar.blade.php
├── pdf.blade.php
└── pdf-individual.blade.php
```

#### Pedidos
```
compras/pedidos/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
├── pdf.blade.php
└── pdf-individual.blade.php
```

#### Órdenes de Compra
```
compras/ordenes/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
├── pdf.blade.php
└── pdf-individual.blade.php
```

#### Compras
```
compras/compras/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
├── pdf.blade.php
└── pdf-individual.blade.php
```

#### Recepciones
```
compras/recepciones/
├── index.blade.php
├── create.blade.php
└── show.blade.php
```

#### Remisiones
```
compras/remisiones/
├── index.blade.php
├── create.blade.php
└── show.blade.php
```

#### Notas de Crédito
```
compras/notas-credito/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── show.blade.php
```

#### Notas de Débito
```
compras/notas-debito/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── show.blade.php
```

#### Pagos
```
compras/pagos/
├── index.blade.php
└── pdf.blade.php
```

#### Aprobaciones
```
compras/aprobaciones/
├── index.blade.php
├── pendientes.blade.php
└── mis-aprobaciones.blade.php
```

#### Reportes
```
compras/reportes/
├── libro-compras.blade.php
├── libro-compras-pdf.blade.php
├── compras-periodo.blade.php
├── analisis-proveedores.blade.php
├── flujo-aprobaciones.blade.php
└── recepciones-vs-compras.blade.php
```

---

### 💰 Ventas (ventas/)

#### Facturación
```
ventas/facturacion/
└── index.blade.php
```

#### Facturas
```
ventas/facturas/
├── index.blade.php
├── crear.blade.php
├── editar.blade.php
├── show.blade.php
└── pdf.blade.php
```

#### Pedidos
```
ventas/pedidos/
├── registrar.blade.php
├── editar.blade.php
├── show.blade.php
└── historial.blade.php
```

#### Remisiones
```
ventas/remisiones/
├── index.blade.php
├── crear.blade.php
├── show.blade.php
└── pdf.blade.php
```

#### Notas de Crédito
```
ventas/notas-credito/
├── index.blade.php
├── crear.blade.php
├── editar.blade.php
└── show.blade.php
```

#### Notas de Débito
```
ventas/notas-debito/
├── index.blade.php
├── crear.blade.php
├── editar.blade.php
└── show.blade.php
```

#### Cuentas por Cobrar
```
ventas/cuentas-cobrar/
├── index.blade.php
└── cobrar.blade.php
```

#### Cobranzas
```
ventas/cobranzas/
├── registrar.blade.php
├── historial.blade.php
└── forma-pago.blade.php
```

#### Caja
```
ventas/caja/
├── apertura.blade.php
├── movimientos.blade.php
├── arqueo.blade.php
├── cierre.blade.php
└── recaudaciones.blade.php
```

#### Reportes
```
ventas/informes/
└── index.blade.php

ventas/libro-ventas/
└── index.blade.php
```

---

### 📦 Stock (stock/)

#### Productos
```
stock/productos/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
├── kardex.blade.php
└── pdf.blade.php
```

#### Categorías
```
stock/categorias/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Marcas
```
stock/marcas/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Unidades de Medida
```
stock/unidades-medida/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Atributos
```
stock/atributos-tipo/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Gestión de Stock
```
stock/stock/
├── index.blade.php
├── ajuste.blade.php
├── transferencia.blade.php
└── inventario.blade.php
```

#### Reportes
```
stock/reportes/
├── valorizado.blade.php
├── rotacion.blade.php
└── stock-bajo.blade.php
```

---

### 🔧 Servicios (servicios/)

#### Clientes
```
servicios/clientes/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── registrar.blade.php
├── historial.blade.php
└── pdf/
    └── pdf-clientes-listado.blade.php
```

#### Tipos de Servicio
```
servicios/tipos-servicio/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Solicitudes
```
servicios/solicitudes/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── pdf/
    └── pdf-solicitudes-listado.blade.php
```

#### Recepciones
```
servicios/recepciones/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── pdf/
    └── pdf-recepciones-listado.blade.php
```

#### Diagnósticos
```
servicios/diagnosticos/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Presupuestos
```
servicios/presupuestos/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Órdenes de Servicio
```
servicios/ordenes/
├── edit.blade.php
├── pdf-orden-trabajo.blade.php
└── pdf-contrato.blade.php
```

#### Entrega
```
servicios/entrega/
└── index.blade.php
```

#### Reclamos
```
servicios/reclamos/
├── registrar.blade.php
├── edit.blade.php
└── seguimiento.blade.php
```

#### Promociones
```
servicios/promociones/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Descuentos
```
servicios/descuentos/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Gestión (Tableros)
```
servicios/gestion/
├── solicitudes.blade.php
├── recepcion.blade.php
├── diagnostico.blade.php
├── presupuestos.blade.php
├── ordenes.blade.php
└── entrega.blade.php
```

#### Informes
```
servicios/informes/
├── index.blade.php
└── pdf/
    ├── solicitudes.blade.php
    ├── presupuestos.blade.php
    ├── ordenes.blade.php
    └── reclamos.blade.php
```

---

### 🏢 Empresa (empresa/)

#### Datos de Empresa
```
empresa/empresa/
├── index.blade.php
└── edit.blade.php
```

#### Sucursales
```
empresa/sucursales/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Depósitos
```
empresa/depositos/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Puntos de Expedición
```
empresa/puntos-expedicion/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Timbrados
```
empresa/timbrados/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

---

### ⚡ Livewire Components (livewire/)

#### Dashboard
```
livewire/dashboard/
└── home.blade.php
```

#### Admin - Roles
```
livewire/admin/roles/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Admin - Usuarios
```
livewire/admin/usuarios/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── asignar-rol.blade.php
└── asignar-rol-a-usuarios.blade.php
```

#### Compras - Dashboard
```
livewire/compras/dashboard/
├── index.blade.php
└── flujo-compra.blade.php
```

#### Compras - Proveedores
```
livewire/compras/
├── proveedor-form.blade.php
└── proveedor-table.blade.php
```

#### Compras - Presupuestos
```
livewire/compras/
├── presupuesto-form.blade.php
└── presupuesto-lista.blade.php
```

#### Compras - Pedidos
```
livewire/compras/
├── pedido-compra-form.blade.php
└── pedido-compra-lista.blade.php
```

#### Compras - Órdenes
```
livewire/compras/
├── orden-compra-form.blade.php
└── orden-compra-lista.blade.php
```

#### Compras - Compras
```
livewire/compras/
├── compra-form.blade.php
└── compra-lista.blade.php

livewire/compras/compras/
├── index.blade.php
├── show.blade.php
└── tabs/
    ├── detalles.blade.php
    ├── items.blade.php
    ├── aprobaciones.blade.php
    ├── recepciones.blade.php
    ├── trazabilidad.blade.php
    └── auditoria.blade.php
```

#### Compras - Recepciones
```
livewire/compras/recepciones/
├── index.blade.php
└── create.blade.php
```

#### Compras - Pagos
```
livewire/compras/
└── pago-lista.blade.php
```

#### Compras - Aprobaciones
```
livewire/compras/aprobaciones/
├── index.blade.php
├── pendientes.blade.php
└── mis-aprobaciones.blade.php
```

#### Ventas - Facturas
```
livewire/ventas/
├── factura-form.blade.php
└── factura-list.blade.php
```

#### Ventas - Pedidos
```
livewire/ventas/
└── pedido-cliente-form.blade.php
```

#### Ventas - Remisiones
```
livewire/ventas/
└── remision-form.blade.php
```

#### Ventas - Notas
```
livewire/ventas/
├── nota-credito-form.blade.php
├── nota-credito-list.blade.php
├── nota-debito-form.blade.php
└── nota-debito-list.blade.php
```

#### Ventas - Cuentas por Cobrar
```
livewire/ventas/
└── pagar-cuenta.blade.php
```

#### Ventas - Cobranzas
```
livewire/ventas/cobranzas/
├── registrar-cobranza.blade.php
├── historial-cobranzas.blade.php
└── forma-pago.blade.php
```

#### Ventas - Caja
```
livewire/ventas/caja/
├── apertura.blade.php
├── movimientos.blade.php
├── arqueo.blade.php
├── cierre.blade.php
└── recaudaciones.blade.php
```

#### Ventas - Reportes
```
livewire/ventas/
├── informes-ventas.blade.php
└── libro-ventas.blade.php
```

#### Stock - Productos
```
livewire/stock/productos/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
└── kardex.blade.php
```

#### Stock - Categorías
```
livewire/stock/categorias/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Stock - Marcas
```
livewire/stock/marcas/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Stock - Unidades
```
livewire/stock/unidades-medida/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Stock - Atributos
```
livewire/stock/atributos-tipo/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Stock - Gestión
```
livewire/stock/
├── stock-index.blade.php
├── ajuste-stock.blade.php
├── transferencia-stock.blade.php
└── inventario-stock.blade.php
```

#### Stock - Reportes
```
livewire/stock/reportes/
├── valorizado.blade.php
├── rotacion.blade.php
└── stock-bajo.blade.php
```

#### Servicios - Clientes
```
livewire/servicios/clientes/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── historial-servicios.blade.php
```

#### Servicios - Tipos
```
livewire/servicios/tipos-servicio/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Servicios - Solicitudes
```
livewire/servicios/solicitudes/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Servicios - Recepciones
```
livewire/servicios/recepciones/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Servicios - Diagnósticos
```
livewire/servicios/diagnosticos/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Servicios - Presupuestos
```
livewire/servicios/presupuestos/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Servicios - Órdenes
```
livewire/servicios/ordenes-servicio/
└── index.blade.php
```

#### Servicios - Entrega
```
livewire/servicios/entrega/
└── index.blade.php
```

#### Servicios - Reclamos
```
livewire/servicios/
├── reclamos-index.blade.php
└── reclamos-manager.blade.php
```

#### Servicios - Promociones
```
livewire/servicios/promociones/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Servicios - Descuentos
```
livewire/servicios/descuentos/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Empresa - Empresa
```
livewire/empresa/empresa/
├── index.blade.php
└── edit.blade.php
```

#### Empresa - Sucursales
```
livewire/empresa/sucursales/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Empresa - Depósitos
```
livewire/empresa/depositos/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Empresa - Puntos de Expedición
```
livewire/empresa/puntos-expedicion/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Empresa - Timbrados
```
livewire/empresa/timbrados/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

---

### 🧩 Componentes
```
components/
└── tabla.blade.php
```

---

## 📊 Resumen Estadístico

### Total de Vistas por Módulo:
- **Compras**: 79 vistas (tradicionales + Livewire)
- **Ventas**: 54 vistas (tradicionales + Livewire)
- **Servicios**: 60 vistas (tradicionales + Livewire)
- **Stock**: 32 vistas (tradicionales + Livewire)
- **Empresa**: 20 vistas (tradicionales + Livewire)
- **Admin**: 17 vistas (tradicionales + Livewire)
- **Auth**: 6 vistas
- **Layouts**: 5 vistas
- **Dashboard**: 2 vistas
- **Otros**: 4 vistas

### **TOTAL: ~279 archivos .blade.php**

---

## 🏗️ Arquitectura

### Patrón de Organización:
1. **Vistas Tradicionales** (`resources/views/[modulo]/`)
   - Vistas estáticas o con scripts inline
   - PDFs generados

2. **Componentes Livewire** (`resources/views/livewire/[modulo]/`)
   - Componentes reactivos
   - Formularios dinámicos
   - Tablas interactivas
   - Gestión de estados

3. **Layouts y Plantillas**
   - Layout principal (AdminLTE)
   - Plantillas PDF
   - Componentes reutilizables

### Módulos Principales:
1. **Compras** - Gestión completa del ciclo de compras
2. **Ventas** - Facturación, cobranzas y punto de venta
3. **Stock** - Inventario y gestión de productos
4. **Servicios** - Órdenes de servicio y garantías
5. **Empresa** - Configuración de la organización
6. **Admin** - Usuarios, roles y permisos
