<div>
    {{-- PROPIEDAD BUSCAR ACTIVO: {{ $buscarActivo ?? 'S/D'}} --}}
    <x-tabla titulo="Usuarios" buscador excel pdf>

        <x-slot name="headerBotones">
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-sm btn-success"><i
                    class="fas fa-user-plus"></i>Añadir Usuario</a>

            <a href="{{ route('admin.usuarios.asignar-rol-a-usuarios') }}" class="btn btn-sm btn-outline-secondary"><i
                    class="fas fa-user-tag"></i>Asig. Rol a Usuarios</a>
        </x-slot>
        <x-slot name="cabeceras">
            {{-- Name --}}
            <th>
                <x-adminlte-input name="" wire:model.live.debounce.200ms="buscarName"
                    oninput="this.value = this.value.toUpperCase()" label="Nombre" igroup-size="sm" />
            </th>

            {{-- Usuario --}}
            <th>
                <x-adminlte-input name="" wire:model.live.debounce.200ms="buscarUsuario"
                    oninput="this.value = this.value.toLowerCase()" label="Usuario" igroup-size="sm" />
            </th>

            {{-- Email --}}
            <th>
                <x-adminlte-input name="" wire:model.live.debounce.200ms="buscarEmail"
                    oninput="this.value = this.value.toLowerCase()" label="Email" igroup-size="sm" />
            </th>

            {{-- Activo --}}
            <th>
                <x-adminlte-select name="" wire:model.live.debounce.200ms="buscarActivo" label="Activo"
                    igroup-size="sm">
                    <option value="">-- Todos --</option>
                    <option value="true">Si</option>
                    <option value="false">No</option>
                </x-adminlte-select>
            </th>

            {{-- Ultimo Acceso --}}
            <th>
                <x-adminlte-input name="" label="Ultimo Acceso" igroup-size="sm" disabled />
            </th>

            {{-- Acciones --}}
            <th>
                <x-adminlte-input name="" label="Acciones" igroup-size="sm" disabled />
            </th>

        </x-slot>

        @forelse ($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->name ?? 'S/D' }}</td>
                <td>{{ $usuario->usuario ?? 'S/D' }}</td>
                <td>{{ $usuario->email ?? 'S/D' }}</td>
                <td>{{ $usuario->activo ? 'SI' : 'NO' }}</td>
                <td>{{ optional($usuario->ultimo_acceso)->format('d/m/Y H:i:s') ?? 'S/D' }}</td>
                <td>
                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-warning"><i
                            class="fas fa-edit mr-1"></i>Editar</a>
                    @if ($usuario->activo === true)
                        <x-adminlte-button label="Inactivar" theme="danger" icon="fas fa-ban" class="btn-sm"
                            wire:click="inactivar({{ $usuario->id }})"
                            wire:confirm="Estas Seguro que desear Inactivar este usuario?" />
                    @else
                        <x-adminlte-button label="Activar" theme="success" icon="fas fa-thumbs-up" class="btn-sm"
                            wire:click="activar({{ $usuario->id }})"
                            wire:confirm="Estas Seguro que desear Activar este usuario?" />
                    @endif

                    <x-adminlte-button label="Reset. Contraseña" theme="outline-warning" icon="fas fa-key"
                        class="btn-sm" wire:click="resetearContrasena({{ $usuario->id }})"
                        wire:confirm="Estas Seguro que desear Restablecer la contraseña por defecto de este usuario?" />

                    @can('admin.usuarios.asignar_rol')
                        <a href="{{ route('admin.usuarios.asignar-rol', $usuario->id) }}"
                            class="btn btn-sm btn-outline-secondary"><i class="fas fa-user-tag"></i> Asig. Rol</a>
                    @endcan
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100%" class="text-center text-muted">Sin resultados coincidentes...</td>
            </tr>
        @endforelse

        <x-slot name="paginacion">
            {{ $usuarios->links() }}
        </x-slot>
    </x-tabla>
</div>
