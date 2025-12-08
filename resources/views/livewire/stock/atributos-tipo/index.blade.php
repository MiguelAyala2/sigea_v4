<div>
    <x-adminlte-card theme="light" icon="fas fa-list-ul">
        <x-slot name="title">
            Gestión de Atributos Tipo
        </x-slot>

        <x-slot name="toolsSlot">
            <a href="{{ route('stock.atributos-tipo.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Nuevo Atributo Tipo
            </a>
        </x-slot>

        {{-- Filtros --}}
        <div class="row mb-3">
            <div class="col-md-10">
                <x-adminlte-input
                    name="busqueda"
                    placeholder="Buscar por nombre o código..."
                    wire:model.live.debounce.500ms="busqueda"
                >
                    <x-slot name="appendSlot">
                        <div class="input-group-text">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="col-md-2">
                <div class="custom-control custom-checkbox mt-2">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="soloActivos"
                           wire:model.live="soloActivos">
                    <label class="custom-control-label" for="soloActivos">
                        Solo Activos
                    </label>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        @if($atributosTipo->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th width="100">Código</th>
                            <th>Nombre</th>
                            <th width="100">Unidad</th>
                            <th width="80" class="text-center">Orden</th>
                            <th width="100" class="text-center">Filtrable</th>
                            <th width="100" class="text-center">Requerido</th>
                            <th width="80" class="text-center">Estado</th>
                            <th width="120" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atributosTipo as $atributo)
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">{{ $atributo->codigo }}</span>
                                </td>
                                <td>
                                    <strong>{{ $atributo->nombre }}</strong>
                                    @if($atributo->descripcion)
                                        <br><small class="text-muted">{{ Str::limit($atributo->descripcion, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($atributo->unidad)
                                        <span class="badge badge-info">{{ $atributo->unidad }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light">{{ $atributo->orden }}</span>
                                </td>
                                <td class="text-center">
                                    @if($atributo->es_filtrable)
                                        <span class="badge badge-success">Sí</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($atributo->es_requerido)
                                        <span class="badge badge-warning">Sí</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($atributo->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('stock.atributos-tipo.edit', $atributo->id) }}"
                                       class="btn btn-xs btn-warning"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-3">
                {{ $atributosTipo->links() }}
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                No se encontraron atributos tipo con los filtros aplicados.
            </div>
        @endif
    </x-adminlte-card>
</div>
