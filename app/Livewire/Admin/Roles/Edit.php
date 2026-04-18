<?php

namespace App\Livewire\Admin\Roles;

use App\Models\Admin\Permiso;
use App\Models\Admin\Rol;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public $id;

    #[Validate]
    public $name, $permisos; // PROPIEDADES DEL FORMULARIO

    public $permisosAgrupados = []; // Permisos organizados por módulo y acción

    public function mount()
    {
        $rol = Rol::findOrFail($this->id);
        $this->name = $rol->name;
        $this->permisos = $rol->permissions->pluck('name')->toArray();

        // Agrupar permisos por módulo y acción
        $this->permisosAgrupados = $this->agruparPermisos();
    }

    /**
     * Agrupar permisos por módulo y acción
     */
    protected function agruparPermisos()
    {
        $permisos = Permiso::where('name', 'NOT LIKE', 'SuperAdmin')
            ->orderBy('name')
            ->get();

        $agrupados = [];
        $acciones = ['ver', 'crear', 'editar', 'eliminar', 'aprobar', 'rechazar', 'anular', 'imprimir', 'exportar',
                     'activar', 'inactivar', 'asignar_rol', 'reset_password', 'gestionar', 'ejecutar',
                     'finalizar', 'cobrar', 'pagar', 'transferir', 'ajustar', 'convertir', 'reportes'];

        foreach ($permisos as $permiso) {
            $nombre = $permiso->name;

            // Permisos con formato nuevo: modulo.entidad.accion
            if (str_contains($nombre, '.')) {
                $partes = explode('.', $nombre);

                // Wildcard (ej: compras.*, admin.usuarios.*)
                if (count($partes) == 2 && $partes[1] == '*') {
                    $modulo = ucfirst($partes[0]);
                    $agrupados[$modulo]['_wildcards'][] = $nombre;
                    continue;
                }

                if (count($partes) >= 3) {
                    $modulo = ucfirst($partes[0]);
                    $entidad = ucfirst(str_replace('_', ' ', $partes[1]));
                    $accion = $partes[2];

                    // Agrupar por módulo y entidad
                    if (!isset($agrupados[$modulo][$entidad])) {
                        $agrupados[$modulo][$entidad] = [];
                    }

                    $agrupados[$modulo][$entidad][$accion] = $nombre;
                }
            }
            // Permisos antiguos (mantener compatibilidad)
            else {
                $agrupados['Otros']['Permisos Legados']['legacy_' . md5($nombre)] = $nombre;
            }
        }

        // Ordenar acciones en orden estándar
        $ordenAcciones = array_flip($acciones);
        foreach ($agrupados as $modulo => &$entidades) {
            foreach ($entidades as $entidad => &$permisos) {
                if ($entidad !== '_wildcards') {
                    uksort($permisos, function($a, $b) use ($ordenAcciones) {
                        $posA = $ordenAcciones[$a] ?? 999;
                        $posB = $ordenAcciones[$b] ?? 999;
                        return $posA - $posB;
                    });
                }
            }
        }

        return $agrupados;
    }

    /**
     * Seleccionar/Deseleccionar todos los permisos de un módulo
     */
    public function toggleModulo($modulo)
    {
        if (!isset($this->permisosAgrupados[$modulo])) {
            return;
        }

        $todosPermisos = [];
        foreach ($this->permisosAgrupados[$modulo] as $entidad => $permisos) {
            if ($entidad === '_wildcards') {
                $todosPermisos = array_merge($todosPermisos, $permisos);
            } else {
                $todosPermisos = array_merge($todosPermisos, array_values($permisos));
            }
        }

        // Verificar si todos están seleccionados
        $todosSeleccionados = !empty($todosPermisos) && count(array_intersect($todosPermisos, $this->permisos)) === count($todosPermisos);

        if ($todosSeleccionados) {
            // Deseleccionar todos
            $this->permisos = array_diff($this->permisos, $todosPermisos);
        } else {
            // Seleccionar todos
            $this->permisos = array_unique(array_merge($this->permisos, $todosPermisos));
        }
    }

    /**
     * Seleccionar/Deseleccionar todos los permisos de una entidad
     */
    public function toggleEntidad($modulo, $entidad)
    {
        if (!isset($this->permisosAgrupados[$modulo][$entidad])) {
            return;
        }

        $permisosEntidad = array_values($this->permisosAgrupados[$modulo][$entidad]);

        // Verificar si todos están seleccionados
        $todosSeleccionados = !empty($permisosEntidad) && count(array_intersect($permisosEntidad, $this->permisos)) === count($permisosEntidad);

        if ($todosSeleccionados) {
            // Deseleccionar todos
            $this->permisos = array_diff($this->permisos, $permisosEntidad);
        } else {
            // Seleccionar todos
            $this->permisos = array_unique(array_merge($this->permisos, $permisosEntidad));
        }
    }

    protected function rules()
    {
        return [
            'name'        => ['required', 'string', 'max:45', Rule::unique(Rol::class)->ignore($this->id)],
            'permisos'    => ['required', 'array']
        ];
    }

    public function guardar()
    {
        $this->validate();
        $rol = Rol::findOrFail($this->id);
        $rol->update([
            'name' => $this->name,
            'actualizadoPor' => Auth::id(),
        ]);
        // ASIGNAR LOS PERMISOS SELECCIONADOS AL ROL
        $rol->syncPermissions($this->permisos);

        session()->flash('success', 'Rol Actualizado Correctamente!');
        $this->redirectRoute('admin.roles.index');
    }

    public function render()
    {
        return view('livewire.admin.roles.edit');
    }
}
