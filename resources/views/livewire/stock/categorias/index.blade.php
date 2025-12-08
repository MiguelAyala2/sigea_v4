<div>
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="¡Éxito!" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    <x-tabla titulo="Categorías" buscador="buscador">
        <x-slot name="headerBotones">
            <a href="{{ route('stock.categorias.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Nueva Categoría
            </a>
        </x-slot>

        <x-slot name="cabeceras">
            <th>Código</th>
            <th>
                <x-adminlte-input name="buscarNombre"
                    wire:model.live.debounce.300ms="buscarNombre"
                    placeholder="Nombre"
                    igroup-size="sm" />
            </th>
            <th>Categoría Padre</th>
            <th>Nivel</th>
            <th>
                <x-adminlte-select name="buscarActivo"
                    wire:model.live.debounce.300ms="buscarActivo"
                    igroup-size="sm">
                    <option value="">-- Todos --</option>
                    <option value="true">Activo</option>
                    <option value="false">Inactivo</option>
                </x-adminlte-select>
            </th>
            <th>Acciones</th>
        </x-slot>

        @forelse($categorias as $categoria)
            <tr class="{{ $categoria->activo ? '' : 'table-secondary' }}">
                <td>
                    <span class="badge badge-secondary">{{ $categoria->codigo }}</span>
                </td>
                <td>
                    <span style="padding-left: {{ ($categoria->nivel - 1) * 20 }}px;">
                        @if($categoria->nivel > 1)
                            <i class="fas fa-level-up-alt fa-rotate-90 text-muted"></i>
                        @endif
                        {{ $categoria->nombre }}
                    </span>
                </td>
                <td>
                    @if($categoria->parent)
                        <small class="text-muted">{{ $categoria->parent->nombre }}</small>
                    @else
                        <span class="badge badge-primary">Raíz</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($categoria->nivel == 1)
                        <span class="badge badge-primary">Grupo</span>
                    @elseif($categoria->nivel == 2)
                        <span class="badge badge-info">Subgrupo</span>
                    @else
                        <span class="badge badge-secondary">Especialización</span>
                    @endif
                </td>
                <td>
                    @if($categoria->activo)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('stock.categorias.edit', $categoria->id) }}"
                           class="btn btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>

                        @if($categoria->activo)
                            <button type="button" class="btn btn-secondary"
                                    wire:click="inactivar({{ $categoria->id }})"
                                    wire:confirm="¿Inactivar categoría {{ $categoria->nombre }}?"
                                    title="Inactivar">
                                <i class="fas fa-ban"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-success"
                                    wire:click="activar({{ $categoria->id }})"
                                    wire:confirm="¿Activar categoría {{ $categoria->nombre }}?"
                                    title="Activar">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif

                        <button type="button" class="btn btn-danger"
                                wire:click="eliminar({{ $categoria->id }})"
                                wire:confirm="¿Está seguro de eliminar la categoría {{ $categoria->nombre }}? Esta acción no se puede deshacer."
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">
                    No se encontraron categorías
                </td>
            </tr>
        @endforelse

        <x-slot name="paginacion">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $categorias->firstItem() }} a {{ $categorias->lastItem() }}
                        de {{ $categorias->total() }} registros
                    </small>
                </div>
                <div>
                    {{ $categorias->links() }}
                </div>
            </div>
        </x-slot>
    </x-tabla>
</div>
