<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Reclamos</h3>
            <div class="card-tools">
                <a href="{{ route('servicios.reclamos.registrar') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Reclamo
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" wire:model.live="buscador" class="form-control" placeholder="Buscar por código o cliente...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="buscarEstado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_revision">En Revisión</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="resuelto">Resuelto</option>
                        <option value="cerrado">Cerrado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="buscarPrioridad" class="form-control">
                        <option value="">Todas las prioridades</option>
                        <option value="urgente">Urgente</option>
                        <option value="alta">Alta</option>
                        <option value="media">Media</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="paginado" class="form-control">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>N° Reclamo</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Prioridad</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reclamos as $reclamo)
                        <tr>
                            <td>{{ $reclamo->codigo }}</td>
                            <td>{{ $reclamo->fecha_reclamo->format('d/m/Y') }}</td>
                            <td>{{ $reclamo->cliente_nombre }}</td>
                            <td>{{ $reclamo->tipo_reclamo_text }}</td>
                            <td>{!! $reclamo->prioridad_badge !!}</td>
                            <td>{{ $reclamo->responsable_nombre }}</td>
                            <td>{!! $reclamo->estado_badge !!}</td>
                            <td>
                                <button wire:click="verReclamo({{ $reclamo->id }})" class="btn btn-sm btn-info" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('servicios.reclamos.edit', $reclamo->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay reclamos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $reclamos->links() }}
            </div>
        </div>
    </div>

    <!-- Modal para ver detalle -->
    @if($mostrarModal && $reclamoSeleccionado)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Detalle del Reclamo: {{ $reclamoSeleccionado->codigo }}</h5>
                    <button type="button" class="close text-white" wire:click="cerrarModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> {{ $reclamoSeleccionado->cliente->nombre }}</p>
                            <p><strong>Fecha Reclamo:</strong> {{ $reclamoSeleccionado->fecha_reclamo->format('d/m/Y') }}</p>
                            <p><strong>Tipo:</strong> {{ $reclamoSeleccionado->tipo_reclamo_text }}</p>
                            <p><strong>Prioridad:</strong> {!! $reclamoSeleccionado->prioridad_badge !!}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Estado:</strong> {!! $reclamoSeleccionado->estado_badge !!}</p>
                            <p><strong>Canal:</strong> {{ $reclamoSeleccionado->canal_recepcion_text }}</p>
                            <p><strong>Responsable:</strong> {{ $reclamoSeleccionado->responsable_nombre }}</p>
                            @if($reclamoSeleccionado->ordenServicio)
                            <p><strong>Orden Relacionada:</strong> {{ $reclamoSeleccionado->ordenServicio->codigo }}</p>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Descripción del Reclamo:</strong></p>
                            <p class="text-justify">{{ $reclamoSeleccionado->descripcion }}</p>
                        </div>
                    </div>
                    @if($reclamoSeleccionado->solucion)
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Solución:</strong></p>
                            <p class="text-justify">{{ $reclamoSeleccionado->solucion }}</p>
                            @if($reclamoSeleccionado->fecha_resolucion)
                            <p><small><strong>Fecha Resolución:</strong> {{ $reclamoSeleccionado->fecha_resolucion->format('d/m/Y H:i') }}</small></p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if($reclamoSeleccionado->estado === 'pendiente')
                    <button wire:click="cambiarEstado({{ $reclamoSeleccionado->id }}, 'en_revision')" class="btn btn-info">
                        <i class="fas fa-search"></i> Marcar En Revisión
                    </button>
                    @endif
                    @if($reclamoSeleccionado->estado === 'en_revision' || $reclamoSeleccionado->estado === 'pendiente')
                    <button wire:click="cambiarEstado({{ $reclamoSeleccionado->id }}, 'en_proceso')" class="btn btn-warning">
                        <i class="fas fa-cog"></i> Marcar En Proceso
                    </button>
                    @endif
                    @if($reclamoSeleccionado->estado === 'en_proceso')
                    <button wire:click="cambiarEstado({{ $reclamoSeleccionado->id }}, 'resuelto')" class="btn btn-success">
                        <i class="fas fa-check"></i> Marcar Resuelto
                    </button>
                    @endif
                    <button type="button" class="btn btn-secondary" wire:click="cerrarModal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
