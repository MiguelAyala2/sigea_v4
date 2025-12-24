<div>
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="Éxito" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    {{-- Filtros de búsqueda --}}
    <x-adminlte-card theme="primary" title="Filtros de Búsqueda" icon="fas fa-filter" collapsible>
        <div class="row">
            <div class="col-md-4">
                <x-adminlte-input name="buscar" label="Buscar" placeholder="Código o descripción..." wire:model.live="buscar">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-3">
                <x-adminlte-select name="filtroActivo" label="Estado" wire:model.live="filtroActivo">
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-toggle-on"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>

            <div class="col-md-3">
                <x-adminlte-select name="porPagina" label="Registros por página" wire:model.live="porPagina">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-list"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('servicios.tipos-servicio.create') }}" class="btn btn-success btn-block mb-3">
                    <i class="fas fa-plus"></i> Nuevo
                </a>
            </div>
        </div>
    </x-adminlte-card>

    {{-- Tabla de Tipos de Servicio --}}
    <x-adminlte-card theme="light" title="Listado de Tipos de Servicio" icon="fas fa-list">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="100">Código</th>
                        <th>Descripción</th>
                        <th width="150" class="text-right">Costo (₲)</th>
                        <th width="100" class="text-center">Estado</th>
                        <th width="150" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tiposServicio as $tipo)
                        <tr>
                            <td><strong>{{ $tipo->codigo }}</strong></td>
                            <td>{{ $tipo->descripcion }}</td>
                            <td class="text-right">₲ {{ number_format($tipo->costo, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <button wire:click="toggleActivo({{ $tipo->id }})"
                                        class="btn btn-sm {{ $tipo->activo ? 'btn-success' : 'btn-secondary' }}">
                                    @if($tipo->activo)
                                        <i class="fas fa-check"></i> Activo
                                    @else
                                        <i class="fas fa-times"></i> Inactivo
                                    @endif
                                </button>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('servicios.tipos-servicio.edit', $tipo->id) }}"
                                   class="btn btn-info btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="confirmarEliminacion({{ $tipo->id }})"
                                        class="btn btn-danger btn-sm" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle"></i> No se encontraron tipos de servicio.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tiposServicio->hasPages())
            <div class="mt-3">
                {{ $tiposServicio->links() }}
            </div>
        @endif
    </x-adminlte-card>

    {{-- Modal de confirmación de eliminación --}}
    @if($confirmandoEliminacion)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                        </h5>
                        <button type="button" class="close text-white" wire:click="cancelarEliminacion">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar este tipo de servicio?</p>
                        <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelarEliminacion">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="eliminar">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
