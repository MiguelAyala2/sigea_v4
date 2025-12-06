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

    <x-adminlte-card theme="light" title="Puntos de Expedición" icon="fas fa-shipping-fast">
        <div class="row mb-3">
            <div class="col-md-5">
                <x-adminlte-input name="buscador" wire:model.live="buscador" placeholder="Buscar punto de expedición..."
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
                <a href="{{ route('empresa.puntos-expedicion.create') }}" class="btn btn-success btn-block btn-sm">
                    <i class="fas fa-plus"></i> Nuevo
                </a>
            </div>
        </div>

        @if($puntosExpedicion->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Sucursal</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($puntosExpedicion as $punto)
                            <tr>
                                <td><strong>{{ $punto->codigo }}</strong></td>
                                <td>{{ $punto->nombre }}</td>
                                <td>
                                    <small class="text-muted">{{ $punto->sucursal->codigo_establecimiento }}</small>
                                    {{ $punto->sucursal->nombre }}
                                </td>
                                <td>
                                    @if($punto->tipo === 'caja')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-cash-register"></i> Caja
                                        </span>
                                    @elseif($punto->tipo === 'terminal')
                                        <span class="badge badge-info">
                                            <i class="fas fa-laptop"></i> Terminal
                                        </span>
                                    @else
                                        <span class="badge badge-success">
                                            <i class="fas fa-globe"></i> Web
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($punto->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('empresa.puntos-expedicion.edit', $punto->id) }}"
                                           class="btn btn-warning btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($punto->activo)
                                            <button wire:click="inactivar({{ $punto->id }})"
                                                    class="btn btn-secondary btn-xs"
                                                    onclick="return confirm('¿Inactivar punto de expedición?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $punto->id }})"
                                                    class="btn btn-success btn-xs">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button wire:click="eliminar({{ $punto->id }})"
                                                class="btn btn-danger btn-xs"
                                                onclick="return confirm('¿Eliminar punto de expedición? Esta acción no se puede deshacer.')">
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
                    Mostrando {{ $puntosExpedicion->firstItem() }} a {{ $puntosExpedicion->lastItem() }} de {{ $puntosExpedicion->total() }} registros
                </div>
                <div>
                    {{ $puntosExpedicion->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay puntos de expedición registrados</h5>
                <p class="text-muted">Crea tu primer punto de expedición para comenzar</p>
                <a href="{{ route('empresa.puntos-expedicion.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear Primer Punto de Expedición
                </a>
            </div>
        @endif
    </x-adminlte-card>
</div>
