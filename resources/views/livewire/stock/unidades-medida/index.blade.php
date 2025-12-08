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

    <x-tabla titulo="Unidades de Medida" buscador="buscador">
        <x-slot name="headerBotones">
            <a href="{{ route('stock.unidades-medida.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Nueva Unidad
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
            <th>Símbolo</th>
            <th>Decimales</th>
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

        @forelse($unidadesMedida as $unidad)
            <tr class="{{ $unidad->activo ? '' : 'table-secondary' }}">
                <td>
                    <span class="badge badge-secondary">{{ $unidad->codigo }}</span>
                </td>
                <td>{{ $unidad->nombre }}</td>
                <td>
                    <span class="badge badge-info">{{ $unidad->simbolo }}</span>
                </td>
                <td class="text-center">
                    @if($unidad->permite_decimales)
                        <span class="badge badge-success">Sí</span>
                    @else
                        <span class="badge badge-secondary">No</span>
                    @endif
                </td>
                <td>
                    @if($unidad->activo)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('stock.unidades-medida.edit', $unidad->id) }}"
                           class="btn btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>

                        @if($unidad->activo)
                            <button type="button" class="btn btn-secondary"
                                    wire:click="inactivar({{ $unidad->id }})"
                                    wire:confirm="¿Inactivar unidad {{ $unidad->nombre }}?"
                                    title="Inactivar">
                                <i class="fas fa-ban"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-success"
                                    wire:click="activar({{ $unidad->id }})"
                                    wire:confirm="¿Activar unidad {{ $unidad->nombre }}?"
                                    title="Activar">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif

                        <button type="button" class="btn btn-danger"
                                wire:click="eliminar({{ $unidad->id }})"
                                wire:confirm="¿Está seguro de eliminar la unidad {{ $unidad->nombre }}? Esta acción no se puede deshacer."
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">
                    No se encontraron unidades de medida
                </td>
            </tr>
        @endforelse

        <x-slot name="paginacion">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $unidadesMedida->firstItem() }} a {{ $unidadesMedida->lastItem() }}
                        de {{ $unidadesMedida->total() }} registros
                    </small>
                </div>
                <div>
                    {{ $unidadesMedida->links() }}
                </div>
            </div>
        </x-slot>
    </x-tabla>
</div>
