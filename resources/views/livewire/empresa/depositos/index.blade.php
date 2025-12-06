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

    <x-adminlte-card theme="light" title="Depósitos" icon="fas fa-warehouse">
        <div class="row mb-3">
            <div class="col-md-5">
                <x-adminlte-input name="buscador" wire:model.live="buscador" placeholder="Buscar depósito..."
                    fgroup-class="mb-0" igroup-size="sm">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="col-md-3">
                <x-adminlte-select name="buscarSucursal" wire:model.live="buscarSucursal" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todas las sucursales</option>
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">{{ $sucursal->codigo_establecimiento }} - {{ $sucursal->nombre }}</option>
                    @endforeach
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-building"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
            <div class="col-md-2">
                <x-adminlte-select name="buscarActivo" wire:model.live="buscarActivo" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-filter"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('empresa.depositos.create') }}" class="btn btn-success btn-block btn-sm">
                    <i class="fas fa-plus"></i> Nuevo
                </a>
            </div>
        </div>

        @if($depositos->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Sucursal</th>
                            <th>Principal</th>
                            <th>Permite Venta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($depositos as $deposito)
                            <tr>
                                <td><strong>{{ $deposito->codigo }}</strong></td>
                                <td>{{ $deposito->nombre }}</td>
                                <td>
                                    <small class="text-muted">{{ $deposito->sucursal->codigo_establecimiento }}</small>
                                    {{ $deposito->sucursal->nombre }}
                                </td>
                                <td>
                                    @if($deposito->es_principal)
                                        <span class="badge badge-primary">
                                            <i class="fas fa-star"></i> Sí
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($deposito->permite_venta)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Sí
                                        </span>
                                    @else
                                        <span class="badge badge-warning">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($deposito->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('empresa.depositos.edit', $deposito->id) }}"
                                           class="btn btn-warning btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($deposito->activo)
                                            <button wire:click="inactivar({{ $deposito->id }})"
                                                    class="btn btn-secondary btn-xs"
                                                    onclick="return confirm('¿Inactivar depósito?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $deposito->id }})"
                                                    class="btn btn-success btn-xs">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button wire:click="eliminar({{ $deposito->id }})"
                                                class="btn btn-danger btn-xs"
                                                onclick="return confirm('¿Eliminar depósito? Esta acción no se puede deshacer.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between">
                <div>
                    Mostrando {{ $depositos->firstItem() }} a {{ $depositos->lastItem() }} de {{ $depositos->total() }} registros
                </div>
                <div>
                    {{ $depositos->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay depósitos registrados</h5>
                <p class="text-muted">Crea tu primer depósito para comenzar</p>
                <a href="{{ route('empresa.depositos.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear Primer Depósito
                </a>
            </div>
        @endif
    </x-adminlte-card>
</div>
