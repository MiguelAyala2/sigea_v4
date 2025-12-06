# SIGEA V4 - Guía Completa de Estructura del Proyecto

## Stack Tecnológico
- **Framework:** Laravel 12.0
- **PHP:** ^8.2
- **Autenticación:** Sistema personalizado con Login
- **Roles y Permisos:** Spatie Permission 6.21
- **Auditoría:** Owen-IT Laravel Auditing 14.0
- **Tema Admin:** JeroenNoten Laravel AdminLTE 3.15
- **Componentes Reactivos:** Livewire 3.6.4
- **CSS Framework:** Bootstrap 5
- **Exportación Excel:** Maatwebsite Excel 3.1
- **Generación PDF:** Barryvdh DomPDF 3.1
- **Traducciones:** Laravel Lang Common 6.7

---

## 1. ESTRUCTURA DE DIRECTORIOS

```
sigea_v4/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                    # Controladores de administración
│   │   │   │   ├── UsuarioController.php
│   │   │   │   └── RoleController.php
│   │   │   └── Auth/                     # Controladores de autenticación
│   │   │       └── LoginController.php
│   │   └── Middleware/
│   ├── Livewire/                         # Componentes Livewire
│   │   └── Admin/
│   │       ├── Usuarios/                 # Componentes CRUD de Usuarios
│   │       │   ├── Index.php
│   │       │   ├── Create.php
│   │       │   ├── Edit.php
│   │       │   ├── AsignarRol.php
│   │       │   └── AsignarRolAUsuarios.php
│   │       └── Roles/                    # Componentes CRUD de Roles
│   │           ├── Index.php
│   │           ├── Create.php
│   │           └── Edit.php
│   ├── Models/                           # Modelos Eloquent
│   │   ├── User.php
│   │   ├── Admin/
│   │   │   ├── Rol.php
│   │   │   └── Permiso.php
│   │   ├── Productos/
│   │   │   └── Producto.php
│   │   ├── Empresa.php
│   │   ├── Sucursal.php
│   │   └── Impuesto.php
│   ├── Exports/                          # Exportadores (Excel/PDF)
│   │   ├── ExcelGenericoExport.php
│   │   ├── PdfGenericoExport.php
│   │   ├── Excel/Admin/Usuarios/
│   │   └── Pdf/Admin/Usuarios/
│   ├── View/Components/
│   │   └── Tabla.php                     # Componente de tabla reutilizable
│   └── Providers/
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php             # Layout principal (AdminLTE)
│       ├── admin/                        # Vistas de administración
│       │   ├── usuarios/
│       │   └── roles/
│       ├── livewire/                     # Vistas de componentes Livewire
│       │   └── admin/
│       │       ├── usuarios/
│       │       └── roles/
│       └── components/
│           └── tabla.blade.php
├── routes/
│   ├── web.php
│   └── admin.php
├── database/migrations/
└── config/
    ├── adminlte.php
    ├── livewire.php
    └── permission.php
```

---

## 2. PATRÓN DE ARQUITECTURA

### Arquitectura Controlador-Livewire

El proyecto sigue una arquitectura donde:
- **Controladores**: Son minimalistas, solo retornan vistas
- **Livewire**: Contiene toda la lógica de negocio (CRUD, validación, búsqueda)
- **Modelos**: Contienen relaciones, scopes de búsqueda y configuración
- **Vistas Blade**: Containers que llaman a componentes Livewire
- **Vistas Livewire**: Interfaz de usuario reactiva con directivas wire:*

```
Usuario interactua con Vista Blade (AdminLTE Container)
    ↓
Vista llama a Componente Livewire (@livewire)
    ↓
Componente Livewire maneja la lógica
    ↓
Modelo ejecuta queries con Scopes
    ↓
Livewire retorna datos a Vista Livewire
    ↓
Vista se actualiza reactivamente sin reload
```

---

## 3. CONTROLADORES

### Ubicación y Convenciones
- **Ubicación**: `app/Http/Controllers/{Modulo}/`
- **Convención de nombres**: `{Entidad}Controller.php` (PascalCase)
- **Namespace**: `App\Http\Controllers\{Modulo}`
- **Responsabilidad**: SOLO retornar vistas, NO lógica de negocio

### Ejemplo: UsuarioController

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('admin.usuarios.index');
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function edit($usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }
}
```

**IMPORTANTE**:
- Los controladores NO tienen métodos `store()`, `update()`, `destroy()`
- Toda la lógica CRUD está en componentes Livewire
- Solo rutas GET en controladores

---

## 4. MODELOS ELOQUENT

### Ubicación y Organización
- **Ubicación**: `app/Models/{Modulo}/`
- **Convención**: PascalCase, singular (ej: `User`, `Producto`)

### Traits Utilizados
- `HasFactory` - Factory para seeders
- `SoftDeletes` - Eliminación lógica
- `HasRoles` (Spatie) - Sistema de roles y permisos
- `Auditable` (Owen-IT) - Auditoría de cambios

### Campos de Auditoría Estándar
- `creadoPor` - ID del usuario que creó
- `actualizadoPor` - ID del usuario que actualizó
- `deleted_at` - Timestamp de eliminación

### Ejemplo Completo: User Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use HasRoles, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'usuario',
        'email',
        'nro_cedula',
        'nro_celular',
        'observacion',
        'password',
        'activo',
        'ultimo_acceso',
        'creadoPor',
        'actualizadoPor',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'activo'            => 'boolean',
            'ultimo_acceso'     => 'datetime',
        ];
    }

    // SCOPES PARA BÚSQUEDA
    #[Scope]
    protected function buscador(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('name', "%{$search}%")
                ->orWhereLike('usuario', "%{$search}%")
                ->orWhereLike('email', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarName(Builder $query, $search = null): void
    {
        $query->when($search, function (Builder $query, string $search) {
            $query->whereLike('name', "%{$search}%");
        });
    }

    #[Scope]
    protected function buscarActivo(Builder $query, $search): void
    {
        $query->when($search !== null && $search !== '', function (Builder $query) use ($search) {
            $query->where('activo', $search);
        });
    }
}
```

### Convenciones de Nombres de Tablas

El proyecto usa diferentes esquemas:
- **Esquema público**: `EMPRESAS`, `SUCURSALES`
- **Esquema productos**: `productos.PRO_PRODUCTOS`
- **Especificar con**: `protected $table = 'schema.TABLE_NAME';`

---

## 5. COMPONENTES LIVEWIRE

### Configuración en config/livewire.php

```php
return [
    'class_namespace' => 'App\\Livewire',
    'view_path' => resource_path('views/livewire'),
    'pagination_theme' => 'bootstrap', // IMPORTANTE
    'legacy_model_binding' => false,
];
```

### Ejemplo: Index Component

```php
<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // PROPIEDADES DE BÚSQUEDA
    public $buscador = '';
    public $buscarName = '';
    public $buscarUsuario = '';
    public $buscarActivo = '';
    public $paginado = 5;

    // RESETEAR PAGINACIÓN AL CAMBIAR FILTROS
    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarName', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.admin.usuarios.index', [
            'usuarios' => User::buscador($this->buscador)
                ->buscarName($this->buscarName)
                ->buscarActivo($this->buscarActivo)
                ->paginate($this->paginado),
        ]);
    }

    // ACCIONES
    public function activar($id)
    {
        User::findOrFail($id)->update([
            'activo' => true,
            'actualizadoPor' => Auth::id(),
        ]);
        session()->flash('success', 'Usuario Activo Correctamente!');
        $this->redirectRoute('admin.usuarios.index');
    }

    // EXPORTACIÓN
    public function excel()
    {
        $datos = User::select('name', 'usuario', 'email')->get();
        $encabezados = ['Nombre', 'Usuario', 'Email'];
        return Excel::download(
            new ExcelListadoUsuarios($datos, $encabezados),
            'Usuarios.xlsx'
        );
    }
}
```

### Ejemplo: Create Component

```php
<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate]
    public $name, $usuario, $email, $password = '12345678';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'usuario' => ['required', 'string', 'max:45', Rule::unique(User::class)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique(User::class)],
            'password' => ['required', 'string', 'min:8', 'max:20'],
        ];
    }

    public function guardar()
    {
        $this->validate();
        User::create([
            'name' => $this->name,
            'usuario' => $this->usuario,
            'email' => $this->email,
            'password' => $this->password,
            'activo' => true,
            'creadoPor' => Auth::id(),
        ]);
        session()->flash('success', 'Usuario Creado Correctamente!');
        $this->redirectRoute('admin.usuarios.index');
    }

    public function render()
    {
        return view('livewire.admin.usuarios.create');
    }
}
```

### Ejemplo: Edit Component

```php
<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public $id;

    #[Validate]
    public $name, $usuario, $email;

    public function mount(User $user)
    {
        $this->id = $user->id;
        $this->name = $user->name;
        $this->usuario = $user->usuario;
        $this->email = $user->email;
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'usuario' => ['required', 'string', 'max:45', Rule::unique(User::class)->ignore($this->id)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique(User::class)->ignore($this->id)],
        ];
    }

    public function guardar()
    {
        $this->validate();
        User::findOrFail($this->id)->update([
            'name' => $this->name,
            'usuario' => $this->usuario,
            'email' => $this->email,
            'actualizadoPor' => Auth::id(),
        ]);
        session()->flash('success', 'Usuario Actualizado Correctamente!');
        $this->redirectRoute('admin.usuarios.index');
    }

    public function render()
    {
        return view('livewire.admin.usuarios.edit');
    }
}
```

---

## 6. VISTAS BLADE

### Layout Principal

**Archivo**: `resources/views/layouts/app.blade.php`

```blade
@extends('adminlte::page')

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@stop

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')
            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@stop

@section('content')
    @yield('content_body')
@stop

@section('footer')
    <div class="float-right">
        Version: {{ config('app.version', '1.0.0') }}
    </div>
@stop
```

### Vista Container

**Archivo**: `resources/views/admin/usuarios/index.blade.php`

```blade
@extends('layouts.app')

@section('subtitle', 'Usuarios')
@section('content_header_title', 'Usuarios')
@section('content_header_subtitle', 'Listar')

@section('content_body')
    @livewire('admin.usuarios.index')
@stop
```

---

## 7. VISTAS LIVEWIRE

### Vista Index con Componente Tabla

**Archivo**: `resources/views/livewire/admin/usuarios/index.blade.php`

```blade
<div>
    <x-tabla titulo="Usuarios" buscador excel pdf>

        <x-slot name="headerBotones">
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-user-plus"></i> Añadir Usuario
            </a>
        </x-slot>

        <x-slot name="cabeceras">
            <th>
                <x-adminlte-input name=""
                    wire:model.live.debounce.200ms="buscarName"
                    oninput="this.value = this.value.toUpperCase()"
                    label="Nombre" igroup-size="sm" />
            </th>
            <th>
                <x-adminlte-input name=""
                    wire:model.live.debounce.200ms="buscarUsuario"
                    label="Usuario" igroup-size="sm" />
            </th>
            <th>
                <x-adminlte-select name=""
                    wire:model.live.debounce.200ms="buscarActivo"
                    label="Activo" igroup-size="sm">
                    <option value="">-- Todos --</option>
                    <option value="true">Si</option>
                    <option value="false">No</option>
                </x-adminlte-select>
            </th>
            <th><x-adminlte-input name="" label="Acciones" igroup-size="sm" disabled /></th>
        </x-slot>

        @forelse ($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->name ?? 'S/D' }}</td>
                <td>{{ $usuario->usuario ?? 'S/D' }}</td>
                <td>{{ $usuario->activo ? 'SI' : 'NO' }}</td>
                <td>
                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                        class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>

                    <x-adminlte-button label="Inactivar" theme="danger"
                        icon="fas fa-ban" class="btn-sm"
                        wire:click="inactivar({{ $usuario->id }})"
                        wire:confirm="¿Está seguro?" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100%" class="text-center text-muted">
                    Sin resultados coincidentes...
                </td>
            </tr>
        @endforelse

        <x-slot name="paginacion">
            {{ $usuarios->links() }}
        </x-slot>
    </x-tabla>
</div>
```

### Vista Create con Formulario

**Archivo**: `resources/views/livewire/admin/usuarios/create.blade.php`

```blade
<div>
    <x-adminlte-card theme="light" title="Añadir Usuario"
        icon="fas fa-plus-circle" header-class="text-muted text-sm">

        <form class="row col-md-12 p-2" wire:submit="guardar">

            <x-adminlte-input name="name" wire:model.blur="name"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="EJ: JUAN PEREZ"
                fgroup-class="col-md-4" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">Nombre *</div>
                </x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="usuario" wire:model.blur="usuario"
                oninput="this.value = this.value.toLowerCase()"
                placeholder="EJ: juan.perez"
                fgroup-class="col-md-4" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">Usuario *</div>
                </x-slot>
            </x-adminlte-input>

            <x-adminlte-input type="email" name="email" wire:model.blur="email"
                placeholder="EJ: ejemplo@ejemplo.com"
                fgroup-class="col-md-4" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">Email</div>
                </x-slot>
            </x-adminlte-input>

            <div class="form-group col-md-3 d-flex align-items-end">
                <a href="{{ route('admin.usuarios.index') }}"
                    class="btn btn-block btn-outline-secondary text-decoration-none btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="form-group col-md-3 d-flex align-items-end">
                <x-adminlte-button type="submit" label="Guardar"
                    theme="outline-success" icon="fas fa-save"
                    class="w-100 btn-sm" />
            </div>
        </form>
    </x-adminlte-card>
</div>
```

---

## 8. COMPONENTE TABLA REUTILIZABLE

### Componente PHP

**Archivo**: `app/View/Components/Tabla.php`

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Tabla extends Component
{
    public string $titulo;
    public ?string $buscador;
    public ?string $excel;
    public ?string $pdf;
    public ?string $paginado;
    public $paginacion;

    public function __construct(
        string $titulo = '',
        $buscador = null,
        $excel = null,
        $pdf = null,
        string $paginado = 'paginado',
        $paginacion = null
    ) {
        $this->titulo = $titulo;
        $this->buscador = $buscador === true ? 'buscador' : $buscador;
        $this->excel = $excel === true ? 'excel' : $excel;
        $this->pdf = $pdf === true ? 'pdf' : $pdf;
        $this->paginado = $paginado;
        $this->paginacion = $paginacion;
    }

    public function render()
    {
        return view('components.tabla');
    }
}
```

### Vista del Componente

**Archivo**: `resources/views/components/tabla.blade.php`

```blade
<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-2 mb-md-0">
            {{ $titulo ?? '' }}

            @if ($excel)
                <button class="btn btn-sm btn-outline-success mr-1" wire:click="{{ $excel }}">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
            @endif

            @if ($pdf)
                <button class="btn btn-sm btn-outline-secondary mr-1" wire:click="{{ $pdf }}">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            @endif

            @isset($headerBotones)
                {{ $headerBotones }}
            @endisset
        </h3>

        @if ($buscador)
            <div class="card-tools">
                <div class="input-group input-group-sm">
                    <input type="text" name="buscador"
                        class="form-control float-right"
                        placeholder="Buscar..."
                        wire:model.live.debounce.150ms="{{ $buscador ?? 'buscador' }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>{{ $cabeceras }}</tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if ($paginacion)
        <div class="d-flex justify-content-between align-items-center m-2">
            <div class="mb-2 mb-md-0">
                <select class="form-control form-control-sm"
                    style="width: 55px; display:inline-block;"
                    wire:model.live="{{ $paginado }}">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
                <small>Registros por página</small>
            </div>
            <div>{{ $paginacion }}</div>
        </div>
    @endif
</div>
```

---

## 9. RUTAS

### Archivo: routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;

include_once __DIR__.'/admin.php';

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home');
```

### Archivo: routes/admin.php

```php
<?php

use App\Http\Controllers\Admin\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware('role:SuperAdmin')
    ->group(function () {

        Route::controller(UsuarioController::class)->group(function () {
            Route::get('/usuarios', 'index')->name('usuarios.index');
            Route::get('/usuarios/create', 'create')->name('usuarios.create');
            Route::get('/usuarios/{usuario}/edit', 'edit')->name('usuarios.edit');
        });
    });
```

---

## 10. CONFIGURACIÓN AdminLTE

**Archivo**: `config/adminlte.php`

```php
return [
    'title' => 'Grupo Servipar',
    'logo' => '<b>Grupo</b>Servipar',

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_nav_accordion' => true,

    'menu' => [
        [
            'text' => 'Admin',
            'icon' => 'fas fa-terminal',
            'submenu' => [
                [
                    'text' => 'Usuarios',
                    'route' => 'admin.usuarios.index',
                    'can' => 'SuperAdmin',
                ],
                [
                    'text' => 'Roles',
                    'route' => 'admin.roles.index',
                    'can' => 'SuperAdmin',
                ],
            ],
        ],
    ],
];
```

---

## 11. DIRECTIVAS LIVEWIRE

### Wire:model (Data Binding)

```blade
{{-- Binding en tiempo real --}}
<input type="text" wire:model.live="nombre">

{{-- Binding con debounce --}}
<input type="text" wire:model.live.debounce.200ms="buscar">

{{-- Binding al perder foco --}}
<input type="text" wire:model.blur="email">
```

### Wire:click (Eventos)

```blade
<button wire:click="guardar">Guardar</button>
<button wire:click="eliminar({{ $id }})">Eliminar</button>
<button wire:click="eliminar({{ $id }})" wire:confirm="¿Está seguro?">Eliminar</button>
```

### Wire:submit (Formularios)

```blade
<form wire:submit="guardar">
    <input type="text" wire:model="nombre">
    <button type="submit">Guardar</button>
</form>
```

---

## 12. COMPONENTES AdminLTE

### Input

```blade
<x-adminlte-input
    name="nombre"
    wire:model.blur="nombre"
    placeholder="Ingrese el nombre"
    label="Nombre"
    fgroup-class="col-md-6"
    igroup-size="sm"
>
    <x-slot name="prependSlot">
        <div class="input-group-text"><i class="fas fa-user"></i></div>
    </x-slot>
</x-adminlte-input>
```

### Select

```blade
<x-adminlte-select name="estado" wire:model.live="estado" label="Estado" igroup-size="sm">
    <option value="">-- Seleccione --</option>
    <option value="1">Activo</option>
    <option value="0">Inactivo</option>
</x-adminlte-select>
```

### Select2 (Múltiple)

```blade
<x-adminlte-select2 name="roles" label="Roles" multiple wire:model="roles">
    @foreach($rolesDisponibles as $rol)
        <option value="{{ $rol->name }}">{{ $rol->name }}</option>
    @endforeach
</x-adminlte-select2>
```

### Button

```blade
<x-adminlte-button label="Guardar" type="submit" theme="success" icon="fas fa-save" class="btn-sm" />
```

---

## 13. COMANDOS ÚTILES

```bash
# Crear componente Livewire
php artisan make:livewire Admin/Usuarios/Index

# Crear modelo con migración
php artisan make:model Producto -m

# Crear controlador
php artisan make:controller Admin/UsuarioController

# Ejecutar migraciones
php artisan migrate

# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Listar rutas
php artisan route:list
```

---

## 14. PATRONES Y CONVENCIONES

### Búsqueda con Scopes

```php
// En el Modelo
#[Scope]
protected function buscador(Builder $query, $search = null): void
{
    $query->when($search, function (Builder $query, string $search) {
        $query->whereLike('campo', "%{$search}%");
    });
}

// En Livewire
public $buscador = '';

public function updating($key): void
{
    if ($key === 'buscador') {
        $this->resetPage();
    }
}
```

### Auditoría

```php
// Create
Model::create([
    'campo' => $valor,
    'creadoPor' => Auth::id(),
]);

// Update
Model::update([
    'campo' => $valor,
    'actualizadoPor' => Auth::id(),
]);
```

### Flash Messages

```php
session()->flash('success', 'Operación exitosa!');
$this->redirectRoute('ruta.nombre');
```

---

## 15. CHECKLIST PARA NUEVO MÓDULO CRUD

- [ ] Crear migración con campos `creadoPor`, `actualizadoPor`, `softDeletes()`
- [ ] Crear modelo con traits `Auditable`, `SoftDeletes`
- [ ] Definir scopes de búsqueda con `#[Scope]`
- [ ] Crear controlador con métodos `index()`, `create()`, `edit()`
- [ ] Crear componentes Livewire: `Index`, `Create`, `Edit`
- [ ] Crear vistas Blade containers
- [ ] Crear vistas Livewire usando componente `<x-tabla>`
- [ ] Agregar rutas en archivo de módulo
- [ ] Agregar entrada en menú AdminLTE
- [ ] Implementar exportación Excel/PDF si es necesario

---

**Última actualización:** 2025-12-05
**Versión:** 4.0
**Documentación generada mediante exploración completa del proyecto**
