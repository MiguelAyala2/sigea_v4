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

    <x-adminlte-card theme="light" title="Solicitudes de Servicio" icon="fas fa-clipboard-list">
        <div class="row mb-3">
            <div class="col-md-3">
                <x-adminlte-input name="buscador" wire:model.live="buscador" placeholder="Buscar..."
                    fgroup-class="mb-0" igroup-size="sm">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="col-md-2">
                <x-adminlte-select name="buscarEstado" wire:model.live="buscarEstado" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="completado">Completado</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
            <div class="col-md-2">
                <x-adminlte-select name="buscarPrioridad" wire:model.live="buscarPrioridad" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todas las prioridades</option>
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
            <div class="col-md-2">
                <x-adminlte-select name="buscarTipoServicio" wire:model.live="buscarTipoServicio" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los tipos</option>
                    <option value="mantenimiento">Mantenimiento</option>
                    <option value="reparacion">Reparación</option>
                    <option value="diagnostico">Diagnóstico</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-tools"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
            <div class="col-md-3 text-right">
                <a href="{{ route('servicios.solicitudes.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nueva Solicitud
                </a>
                <button wire:click="pdf" class="btn btn-danger btn-sm" title="Exportar PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <button wire:click="excel" class="btn btn-success btn-sm" title="Exportar Excel">
                    <i class="fas fa-file-excel"></i>
                </button>
            </div>
        </div>

        @if($solicitudes->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>N° Solicitud</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Equipo/Producto</th>
                            <th>Tipo Servicio</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $solicitud)
                            <tr>
                                <td><strong>{{ $solicitud->numero_solicitud }}</strong></td>
                                <td>{{ $solicitud->fecha->format('d/m/Y') }}</td>
                                <td>{{ $solicitud->cliente->nombre }}</td>
                                <td>{{ $solicitud->producto->nombre }}</td>
                                <td>
                                    @if($solicitud->tipo_servicio === 'mantenimiento')
                                        <span class="badge badge-info">
                                            <i class="fas fa-cogs"></i> Mantenimiento
                                        </span>
                                    @elseif($solicitud->tipo_servicio === 'reparacion')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-wrench"></i> Reparación
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-search"></i> Diagnóstico
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($solicitud->prioridad === 'alta')
                                        <span class="badge badge-danger">Alta</span>
                                    @elseif($solicitud->prioridad === 'media')
                                        <span class="badge badge-warning">Media</span>
                                    @else
                                        <span class="badge badge-secondary">Baja</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$solicitud->activo)
                                        <span class="badge badge-danger">Inactivo</span>
                                    @elseif($solicitud->estado === 'completado')
                                        <span class="badge badge-success">Completado</span>
                                    @elseif($solicitud->estado === 'en_proceso')
                                        <span class="badge badge-primary">En Proceso</span>
                                    @else
                                        <span class="badge badge-warning">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button wire:click="verDetalles({{ $solicitud->id }})" class="btn btn-info btn-xs" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('servicios.solicitudes.edit', $solicitud->id) }}"
                                           class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($solicitud->activo)
                                            <button wire:click="inactivar({{ $solicitud->id }})"
                                                    class="btn btn-secondary btn-xs"
                                                    title="Inactivar"
                                                    onclick="return confirm('¿Inactivar solicitud?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $solicitud->id }})"
                                                    class="btn btn-success btn-xs"
                                                    title="Activar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Mostrando {{ $solicitudes->firstItem() }} a {{ $solicitudes->lastItem() }} de {{ $solicitudes->total() }} registros
                </div>
                <div>
                    {{ $solicitudes->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay solicitudes registradas</h5>
                <p class="text-muted">Crea tu primera solicitud para comenzar</p>
                <a href="{{ route('servicios.solicitudes.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear Primera Solicitud
                </a>
            </div>
        @endif
    </x-adminlte-card>

    {{-- Modal de Ver Detalles --}}
    @if($showModal && $solicitudSeleccionada)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title"><i class="fas fa-clipboard-list"></i> Detalles de la Solicitud</h5>
                        <button type="button" class="close" wire:click="cerrarModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>N° Solicitud:</strong><br>{{ $solicitudSeleccionada->numero_solicitud }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Fecha:</strong><br>{{ $solicitudSeleccionada->fecha->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <p><strong>Cliente:</strong><br>{{ $solicitudSeleccionada->cliente->nombre }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Equipo/Producto:</strong><br>{{ $solicitudSeleccionada->producto->nombre }}</p>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <p><strong>Tipo de Servicio:</strong><br>
                                    @if($solicitudSeleccionada->tipo_servicio === 'mantenimiento')
                                        <span class="badge badge-info">Mantenimiento</span>
                                    @elseif($solicitudSeleccionada->tipo_servicio === 'reparacion')
                                        <span class="badge badge-warning">Reparación</span>
                                    @else
                                        <span class="badge badge-secondary">Diagnóstico</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Prioridad:</strong><br>
                                    @if($solicitudSeleccionada->prioridad === 'alta')
                                        <span class="badge badge-danger">Alta</span>
                                    @elseif($solicitudSeleccionada->prioridad === 'media')
                                        <span class="badge badge-warning">Media</span>
                                    @else
                                        <span class="badge badge-secondary">Baja</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Estado:</strong><br>
                                    @if($solicitudSeleccionada->estado === 'completado')
                                        <span class="badge badge-success">Completado</span>
                                    @elseif($solicitudSeleccionada->estado === 'en_proceso')
                                        <span class="badge badge-primary">En Proceso</span>
                                    @else
                                        <span class="badge badge-warning">Pendiente</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($solicitudSeleccionada->observaciones)
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <p><strong>Observaciones:</strong><br>{{ $solicitudSeleccionada->observaciones }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="row mt-3 pt-3 border-top">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> Registrado: {{ $solicitudSeleccionada->created_at->format('d/m/Y H:i') }}<br>
                                    @if($solicitudSeleccionada->updated_at != $solicitudSeleccionada->created_at)
                                        <i class="fas fa-edit"></i> Última actualización: {{ $solicitudSeleccionada->updated_at->format('d/m/Y H:i') }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
