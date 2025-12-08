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

    <x-tabla titulo="Marcas" buscador="buscador">
        <x-slot name="headerBotones">
            <a href="{{ route('stock.marcas.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Nueva Marca
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

        @forelse($marcas as $marca)
            <tr class="{{ $marca->activo ? '' : 'table-secondary' }}">
                <td>
                    <span class="badge badge-secondary">{{ $marca->codigo }}</span>
                </td>
                <td>{{ $marca->nombre }}</td>
                <td>
                    @if($marca->activo)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('stock.marcas.edit', $marca->id) }}"
                           class="btn btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>

                        @if($marca->activo)
                            <button type="button" class="btn btn-secondary"
                                    wire:click="inactivar({{ $marca->id }})"
                                    wire:confirm="¿Inactivar marca {{ $marca->nombre }}?"
                                    title="Inactivar">
                                <i class="fas fa-ban"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-success"
                                    wire:click="activar({{ $marca->id }})"
                                    wire:confirm="¿Activar marca {{ $marca->nombre }}?"
                                    title="Activar">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif

                        <button type="button" class="btn btn-danger"
                                wire:click="eliminar({{ $marca->id }})"
                                wire:confirm="¿Está seguro de eliminar la marca {{ $marca->nombre }}? Esta acción no se puede deshacer."
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted">
                    No se encontraron marcas
                </td>
            </tr>
        @endforelse

        <x-slot name="paginacion">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $marcas->firstItem() }} a {{ $marcas->lastItem() }}
                        de {{ $marcas->total() }} registros
                    </small>
                </div>
                <div>
                    {{ $marcas->links() }}
                </div>
            </div>
        </x-slot>
    </x-tabla>
</div>