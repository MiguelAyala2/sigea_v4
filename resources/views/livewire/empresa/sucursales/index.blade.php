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

    <x-adminlte-card theme="light" title="Sucursales" icon="fas fa-building">
        <div class="row mb-3">
            <div class="col-md-9">
                <x-adminlte-input name="buscador" wire:model.live="buscador" placeholder="Buscar..."
                    fgroup-class="mb-0" igroup-size="sm">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="col-md-3">
                <a href="{{ route('empresa.sucursales.create') }}" class="btn btn-success btn-block btn-sm">
                    <i class="fas fa-plus"></i> Nueva Sucursal
                </a>
            </div>
        </div>

        @if($sucursales->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Ciudad</th>
                            <th>Casa Central</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sucursales as $sucursal)
                            <tr>
                                <td>{{ $sucursal->codigo_establecimiento }}</td>
                                <td>{{ $sucursal->nombre }}</td>
                                <td>{{ $sucursal->direccion }}</td>
                                <td>{{ $sucursal->ciudad }}</td>
                                <td>
                                    @if($sucursal->es_casa_central)
                                        <span class="badge badge-primary">Sí</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sucursal->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('empresa.sucursales.edit', $sucursal->id) }}" 
                                           class="btn btn-warning btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($sucursal->activo)
                                            <button wire:click="inactivar({{ $sucursal->id }})" 
                                                    class="btn btn-secondary btn-xs"
                                                    onclick="return confirm('¿Inactivar sucursal?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $sucursal->id }})" 
                                                    class="btn btn-success btn-xs">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button wire:click="eliminar({{ $sucursal->id }})" 
                                                class="btn btn-danger btn-xs"
                                                onclick="return confirm('¿Eliminar sucursal? Esta acción no se puede deshacer.')">
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
                    Mostrando {{ $sucursales->firstItem() }} a {{ $sucursales->lastItem() }} de {{ $sucursales->total() }} registros
                </div>
                <div>
                    {{ $sucursales->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay sucursales registradas</h5>
                <p class="text-muted">Crea tu primera sucursal para comenzar</p>
                <a href="{{ route('empresa.sucursales.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear Primera Sucursal
                </a>
            </div>
        @endif
    </x-adminlte-card>
</div>
