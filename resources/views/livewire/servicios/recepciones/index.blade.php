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

    <x-adminlte-card theme="light" title="Recepciones de Equipos / Productos" icon="fas fa-clipboard-check">
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
                <x-adminlte-select name="buscarEstadoRecepcion" wire:model.live="buscarEstadoRecepcion" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Estado Recepción</option>
                    <option value="bueno">Bueno</option>
                    <option value="regular">Regular</option>
                    <option value="malo">Malo</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
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
            <div class="col-md-3 text-right">
                <div class="btn-group" role="group">
                    <button wire:click="exportarExcel" class="btn btn-success btn-sm" title="Exportar a Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>
                    <button wire:click="exportarPdf" class="btn btn-danger btn-sm" title="Exportar a PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <a href="{{ route('servicios.recepciones.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Recepción
                    </a>
                </div>
            </div>
        </div>

        @if($recepciones->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>N° Recepción</th>
                            <th>Fecha</th>
                            <th>N° Solicitud</th>
                            <th>Cliente</th>
                            <th>Equipo/Producto</th>
                            <th>Estado Recepción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recepciones as $recepcion)
                            <tr>
                                <td><strong>{{ $recepcion->numero_recepcion }}</strong></td>
                                <td>{{ $recepcion->fecha_recepcion->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $recepcion->solicitud->numero_solicitud }}
                                    </span>
                                </td>
                                <td>{{ $recepcion->cliente->nombre }}</td>
                                <td>{{ $recepcion->producto->nombre }}</td>
                                <td>
                                    @if(!$recepcion->activo)
                                        <span class="badge badge-danger">Inactivo</span>
                                    @elseif($recepcion->estado_recepcion === 'bueno')
                                        <span class="badge badge-success">Bueno</span>
                                    @elseif($recepcion->estado_recepcion === 'regular')
                                        <span class="badge badge-warning">Regular</span>
                                    @else
                                        <span class="badge badge-danger">Malo</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$recepcion->activo)
                                        <span class="badge badge-danger">Inactivo</span>
                                    @elseif($recepcion->estado === 'pendiente')
                                        <span class="badge badge-secondary">Pendiente</span>
                                    @elseif($recepcion->estado === 'en_proceso')
                                        <span class="badge badge-warning">En Proceso</span>
                                    @else
                                        <span class="badge badge-success">Completado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button wire:click="verDetalles({{ $recepcion->id }})" class="btn btn-info btn-xs" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('servicios.recepciones.edit', $recepcion->id) }}"
                                           class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($recepcion->activo)
                                            <button wire:click="inactivar({{ $recepcion->id }})"
                                                    class="btn btn-secondary btn-xs"
                                                    title="Inactivar"
                                                    onclick="return confirm('¿Inactivar recepción?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $recepcion->id }})"
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
                    Mostrando {{ $recepciones->firstItem() }} a {{ $recepciones->lastItem() }} de {{ $recepciones->total() }} registros
                </div>
                <div>
                    {{ $recepciones->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay recepciones registradas</h5>
                <p class="text-muted">Crea tu primera recepción para comenzar</p>
                <a href="{{ route('servicios.recepciones.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear Primera Recepción
                </a>
            </div>
        @endif
    </x-adminlte-card>

    {{-- Modal de Ver Detalles --}}
    @if($showModal && $recepcionSeleccionada)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title"><i class="fas fa-clipboard-check"></i> Detalles de la Recepción</h5>
                        <button type="button" class="close" wire:click="cerrarModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>N° Recepción:</strong><br>{{ $recepcionSeleccionada->numero_recepcion }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Fecha:</strong><br>{{ $recepcionSeleccionada->fecha_recepcion->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <p><strong>N° Solicitud:</strong><br>
                                    <span class="badge badge-info">{{ $recepcionSeleccionada->solicitud->numero_solicitud }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Cliente:</strong><br>{{ $recepcionSeleccionada->cliente->nombre }}</p>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <p><strong>Equipo/Producto:</strong><br>{{ $recepcionSeleccionada->producto->nombre }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Contacto Cliente:</strong><br>{{ $recepcionSeleccionada->contacto_cliente ?? 'No registrado' }}</p>
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-primary"><i class="fas fa-tools"></i> Datos del Equipo</h6>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <p><strong>Tipo de Equipo:</strong><br>{{ $recepcionSeleccionada->tipo_equipo ?? 'No especificado' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Estado de Recepción:</strong><br>
                                    @if($recepcionSeleccionada->estado_recepcion === 'bueno')
                                        <span class="badge badge-success">Bueno</span>
                                    @elseif($recepcionSeleccionada->estado_recepcion === 'regular')
                                        <span class="badge badge-warning">Regular</span>
                                    @else
                                        <span class="badge badge-danger">Malo</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Estado:</strong><br>
                                    @if($recepcionSeleccionada->estado === 'pendiente')
                                        <span class="badge badge-secondary">Pendiente</span>
                                    @elseif($recepcionSeleccionada->estado === 'en_proceso')
                                        <span class="badge badge-warning">En Proceso</span>
                                    @else
                                        <span class="badge badge-success">Completado</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <p><strong>Marca:</strong><br>{{ $recepcionSeleccionada->marca ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Modelo:</strong><br>{{ $recepcionSeleccionada->modelo ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>N° Serie:</strong><br>{{ $recepcionSeleccionada->numero_serie ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <p><strong>Descripción del Problema:</strong><br>{{ $recepcionSeleccionada->descripcion_problema }}</p>
                            </div>
                        </div>

                        @if($recepcionSeleccionada->accesorios_recibidos)
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <p><strong>Accesorios Recibidos:</strong><br>{{ $recepcionSeleccionada->accesorios_recibidos }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="row mt-3 pt-3 border-top">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> Registrado: {{ $recepcionSeleccionada->created_at->format('d/m/Y H:i') }}<br>
                                    @if($recepcionSeleccionada->updated_at != $recepcionSeleccionada->created_at)
                                        <i class="fas fa-edit"></i> Última actualización: {{ $recepcionSeleccionada->updated_at->format('d/m/Y H:i') }}
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
