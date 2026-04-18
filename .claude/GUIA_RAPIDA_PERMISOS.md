# Guía Rápida: Sistema de Permisos Granulares

## ✅ Sistema Implementado Exitosamente

Se ha implementado el **Sistema Híbrido de Permisos Granulares + Wildcards** en tu aplicación SIGEA v4.

---

## 📊 Resumen de lo Implementado

### Estadísticas del Sistema

- **Total de Permisos:** 359 permisos
- **Permisos Granulares Nuevos:** 269 permisos con formato `modulo.entidad.accion`
- **Permisos Legados:** 90 permisos antiguos (mantenidos para compatibilidad)
- **Total de Roles:** 15 roles jerárquicos
- **Usuarios de Prueba:** 8 usuarios configurados
- **Wildcards:** Habilitados y funcionales

### Permisos por Módulo

| Módulo | Cantidad | Formato |
|--------|----------|---------|
| **Admin** | 17 permisos | `admin.usuarios.*`, `admin.roles.*` |
| **Compras** | 64 permisos | `compras.compras.*`, `compras.proveedores.*`, etc. |
| **Ventas** | 79 permisos | `ventas.facturas.*`, `ventas.clientes.*`, etc. |
| **Servicios** | 51 permisos | `servicios.ordenes.*`, `servicios.instalaciones.*`, etc. |
| **Stock** | 43 permisos | `stock.productos.*`, `stock.inventario.*`, etc. |
| **Empresa** | 9 permisos | `empresa.configuracion.*`, `empresa.sucursales.*` |
| **Reportes** | 6 permisos | `reportes.financieros.*`, `reportes.ejecutivos.*` |

---

## 🚀 Cómo Usar el Sistema

### 1. Comando de Prueba

Hemos creado un comando Artisan para probar permisos:

```bash
# Ver resumen general del sistema
php artisan permisos:probar

# Probar un usuario específico
php artisan permisos:probar encargado.compras
php artisan permisos:probar superadmin
php artisan permisos:probar cajero
```

**Salida del comando:**
- Roles asignados al usuario
- Permisos directos
- Permisos heredados por módulo
- Prueba de permisos clave
- Verificación de wildcards

---

### 2. Usuarios de Prueba

| Usuario | Contraseña | Rol | Permisos Wildcards |
|---------|------------|-----|-------------------|
| `superadmin` | superadmin123 | SuperAdmin | Todos |
| `administrador` | Rann2006 | Administrador del Sistema | `admin.*`, `compras.*`, `ventas.*`, `servicios.*`, `stock.*` |
| `gerente.general` | 12345678 | Gerente General | Ver, aprobar/rechazar, reportes completos |
| `encargado.compras` | 12345678 | Encargado de Compras | `compras.*` |
| `encargado.servicios` | 12345678 | Encargado de Servicios | `servicios.*` |
| `encargado.ventas` | 12345678 | Encargado de Ventas | `ventas.*` |
| `cajero` | 12345678 | Cajero | `ventas.caja.*` |
| `encargado.almacen` | 12345678 | Encargado de Almacén | `stock.*` |

---

### 3. Ejemplos de Uso en Código

#### En Rutas (Middleware)

```php
// Permiso específico
Route::get('/compras', [CompraController::class, 'index'])
    ->middleware('can:compras.compras.ver');

// Wildcard
Route::prefix('compras')->middleware('can:compras.*')->group(function () {
    // Todas las rutas de compras
});
```

#### En Controladores

```php
public function aprobar(Compra $compra)
{
    // Verificar permiso
    $this->authorize('compras.compras.aprobar');

    // O con Gate
    if (Gate::denies('compras.compras.aprobar')) {
        abort(403);
    }

    // Lógica de aprobación...
}
```

#### En Vistas Blade

```blade
{{-- Permiso específico --}}
@can('compras.compras.crear')
    <a href="{{ route('compras.compras.create') }}" class="btn btn-primary">
        Nueva Compra
    </a>
@endcan

{{-- Wildcard --}}
@can('compras.*')
    <div class="admin-tools">
        <!-- Panel de administrador de compras -->
    </div>
@endcan

{{-- Múltiples permisos --}}
@canany(['compras.compras.editar', 'compras.compras.eliminar'])
    <div class="acciones">
        @can('compras.compras.editar')
            <button>Editar</button>
        @endcan

        @can('compras.compras.eliminar')
            <button>Eliminar</button>
        @endcan
    </div>
@endcanany
```

#### En Livewire

```php
class ComprasIndex extends Component
{
    use AuthorizesRequests;

    public function aprobar($id)
    {
        $this->authorize('compras.compras.aprobar');

        // Lógica...
    }

    public function render()
    {
        return view('livewire.compras.index', [
            'puedeCrear' => auth()->user()->can('compras.compras.crear'),
            'puedeAprobar' => auth()->user()->can('compras.compras.aprobar'),
        ]);
    }
}
```

#### En PHP

```php
// Verificar permiso
if (auth()->user()->can('compras.compras.crear')) {
    // Puede crear
}

// Verificar wildcard
if (auth()->user()->can('compras.*')) {
    // Acceso completo a compras
}

// Verificar múltiples (ANY)
if (auth()->user()->canAny(['compras.compras.ver', 'compras.compras.editar'])) {
    // Tiene al menos uno
}

// Verificar permiso directo
if (auth()->user()->hasPermissionTo('compras.compras.aprobar')) {
    // Tiene específicamente este permiso
}

// Verificar rol
if (auth()->user()->hasRole('Encargado de Compras')) {
    // Tiene este rol
}
```

---

## 🔧 Mantenimiento del Sistema

### Limpiar Caché de Permisos

Después de cualquier cambio en permisos o roles:

```bash
php artisan permission:cache-reset
```

### Re-ejecutar Seeder

Si necesitas recrear todos los permisos y roles:

```bash
# Solo permisos (sin borrar datos)
php artisan db:seed --class=RolYPermisoSeeder

# Refrescar completamente (BORRA DATOS)
php artisan migrate:fresh --seed
```

---

## 📝 Archivos Actualizados

### Archivos Modificados

1. ✅ [config/permission.php](config/permission.php) - Wildcards habilitados
2. ✅ [database/seeders/RolYPermisoSeeder.php](database/seeders/RolYPermisoSeeder.php) - 359 permisos granulares
3. ✅ [routes/compras.php](routes/compras.php) - Middleware actualizado con nuevos permisos
4. ✅ [resources/views/compras/proveedores/*.blade.php](resources/views/compras/proveedores/) - Directivas @can actualizadas

### Archivos Creados

1. ✅ [PERMISOS_SISTEMA.md](PERMISOS_SISTEMA.md) - Documentación completa del sistema
2. ✅ [app/Console/Commands/ProbarPermisosCommand.php](app/Console/Commands/ProbarPermisosCommand.php) - Comando de prueba
3. ✅ [GUIA_RAPIDA_PERMISOS.md](GUIA_RAPIDA_PERMISOS.md) - Esta guía

---

## 🎯 Estructura de Permisos

### Formato Estándar

```
{modulo}.{entidad}.{accion}
```

### Módulos Disponibles

- `admin` - Administración de usuarios y roles
- `compras` - Módulo de compras y proveedores
- `ventas` - Módulo de ventas y facturación
- `servicios` - Módulo de servicios técnicos
- `stock` - Inventario y productos
- `empresa` - Configuración de empresa
- `reportes` - Reportes generales

### Acciones Estándar

| Acción | Uso |
|--------|-----|
| `ver` | Listar y visualizar |
| `crear` | Crear nuevos registros |
| `editar` | Modificar existentes |
| `eliminar` | Eliminar registros |
| `aprobar` | Aprobar documentos |
| `rechazar` | Rechazar solicitudes |
| `anular` | Anular documentos procesados |
| `imprimir` | Generar PDFs |
| `exportar` | Exportar datos |
| `activar/inactivar` | Cambiar estado |
| `gestionar` | Acciones administrativas |
| `ejecutar` | Realizar tareas operativas |
| `finalizar` | Completar procesos |
| `cobrar/pagar` | Transacciones |

---

## 🔐 Jerarquía de Roles

### Nivel 0: SuperAdmin
- **Acceso:** TOTAL (todos los permisos)
- **Uso:** Desarrolladores, Dueño de la empresa

### Nivel 1: Administrador del Sistema
- **Acceso:** Wildcards de todos los módulos
- **Uso:** Gerente de IT, Administrador general

### Nivel 2: Gerencia
- **Gerente General:** Ver, aprobar/rechazar, reportes completos
- **Supervisor General:** Similar sin reportes financieros

### Nivel 3: Encargados
- **Encargado de Compras:** `compras.*`
- **Encargado de Ventas:** `ventas.*`
- **Encargado de Servicios:** `servicios.*`
- **Encargado de Almacén:** `stock.*`

### Nivel 4: Supervisores
- Ver, crear, editar (sin eliminar/aprobar)

### Nivel 5: Operativos
- **Asistentes:** Solo ver y crear
- **Cajero:** Solo caja (`ventas.caja.*`)

---

## 🛠️ Tareas Pendientes (Opcionales)

### Migración Gradual

Aún hay algunas rutas y vistas que usan permisos antiguos. Para completar la migración:

1. Buscar permisos antiguos:
   ```bash
   grep -r "Proveedores Ver" resources/views/
   grep -r "Compras Crear" app/Http/
   ```

2. Reemplazar por nuevos permisos:
   - `Proveedores Ver` → `compras.proveedores.ver`
   - `Compras Crear` → `compras.compras.crear`

3. Actualizar componentes Livewire que usen `$this->authorize()`

4. Actualizar políticas si existen

---

## 📚 Documentación Adicional

Para información detallada, consulta:

- [PERMISOS_SISTEMA.md](PERMISOS_SISTEMA.md) - Documentación completa con matriz de permisos
- Documentación de Spatie: https://spatie.be/docs/laravel-permission

---

## ✨ Beneficios del Nuevo Sistema

### Control Granular
✅ Asigna permisos específicos por acción (ver, crear, editar, aprobar, etc.)
✅ Evita dar más permisos de los necesarios
✅ Cumple con el principio de privilegio mínimo

### Wildcards para Eficiencia
✅ Roles altos usan `compras.*` en lugar de 50+ permisos individuales
✅ Reduce complejidad en asignaciones
✅ Fácil de entender y mantener

### Escalable
✅ Fácil agregar nuevos módulos o acciones
✅ Estructura consistente en todo el sistema
✅ Compatible con crecimiento futuro

### Seguro
✅ Permisos claros y específicos
✅ Verificación en múltiples capas (rutas, controladores, vistas)
✅ Auditoría completa de cambios

### Auditable
✅ Campos `creadoPor` y `actualizadoPor` en roles y permisos
✅ Trazabilidad completa de cambios
✅ Histórico de asignaciones

---

## 🆘 Solución de Problemas

### Error: "User does not have the right permissions"

```bash
# Limpiar caché de permisos
php artisan permission:cache-reset

# Verificar permisos del usuario
php artisan permisos:probar {usuario}
```

### Los wildcards no funcionan

```php
// Verificar configuración
php artisan config:cache

// Verificar que esté habilitado en config/permission.php
'enable_wildcard_permission' => true,
```

### Usuario no tiene acceso después de asignar rol

```bash
# Limpiar todas las cachés
php artisan cache:clear
php artisan config:clear
php artisan permission:cache-reset
```

---

## 📞 Comandos Útiles

```bash
# Ver resumen del sistema de permisos
php artisan permisos:probar

# Probar permisos de un usuario
php artisan permisos:probar encargado.compras

# Limpiar caché de permisos
php artisan permission:cache-reset

# Re-ejecutar seeder de permisos
php artisan db:seed --class=RolYPermisoSeeder

# Ver todos los usuarios y roles
php artisan tinker
>>> User::with('roles')->get()
```

---

**Última actualización:** {{ now()->format('Y-m-d H:i:s') }}

**Sistema:** SIGEA v4 - Sistema Integrado de Gestión Empresarial

**Desarrollado por:** Claude AI Assistant
