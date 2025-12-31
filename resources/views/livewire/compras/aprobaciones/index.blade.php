<div>
    {{-- Mensajes Flash --}}
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="¡Éxito!" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="light" title="Gestión de Aprobaciones" icon="fas fa-check-double">
        {{-- Estadísticas --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['pendientes'] }}</h3>
                        <p>Pendientes</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['aprobados'] }}</h3>
                        <p>Aprobados</p>
                    </div>
                    <div class="icon"><i class="fas fa-check"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['rechazados'] }}</h3>
                        <p>Rechazados</p>
                    </div>
                    <div class="icon"><i class="fas fa-times"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $stats['vencidos'] }}</h3>
                        <p>Vencidos</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <x-adminlte-input name="buscador" wire:model.live.debounce.300ms="buscador"
                    placeholder="Buscar por aprobador, rol, comentarios..."
                    igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-2 mb-2">
                <x-adminlte-select name="tipo_documento" wire:model.live="tipo_documento"
                    igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los tipos</option>
                    @foreach(App\Models\Compras\AprobacionFlujo::TIPOS_DOCUMENTO as $key => $valor)
                        <option value="{{ $key }}">{{ $valor }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>

            <div class="col-md-2 mb-2">
                <x-adminlte-select name="estado" wire:model.live="estado"
                    igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los estados</option>
                    @foreach(App\Models\Compras\AprobacionFlujo::ESTADOS as $key => $valor)
                        <option value="{{ $key }}">{{ $valor }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>

            <div class="col-md-2 mb-2">
                <x-adminlte-select name="nivel_aprobacion" wire:model.live="nivel_aprobacion"
                    igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los niveles</option>
                    <option value="1">Nivel 1</option>
                    <option value="2">Nivel 2</option>
                    <option value="3">Nivel 3</option>
                </x-adminlte-select>
            </div>

            <div class="col-md-2 mb-2">
                <button wire:click="limpiarFiltros" class="btn btn-secondary btn-sm btn-block">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
            </div>
        </div>

        {{-- Tabla --}}
        @if($aprobaciones->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tipo Documento</th>
                            <th>Nivel</th>
                            <th>Aprobador</th>
                            <th>Fecha Asignación</th>
                            <th>Fecha Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aprobaciones as $aprobacion)
                            <tr class="{{ $aprobacion->esta_vencida ? 'table-danger' : '' }}">
                                <td>#{{ $aprobacion->id }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $aprobacion->documento_tipo_texto }}</span>
                                </td>
                                <td>{{ $aprobacion->nivel_aprobacion_texto }}</td>
                                <td>
                                    <strong>{{ $aprobacion->aprobador->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $aprobacion->rol_requerido }}</small>
                                </td>
                                <td>{{ $aprobacion->fecha_asignacion->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $aprobacion->fecha_vencimiento_formateada }}
                                    @if($aprobacion->tiempo_restante)
                                        <br><small class="text-muted">{{ $aprobacion->tiempo_restante }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($aprobacion->estado) {
                                            'APROBADO' => 'success',
                                            'RECHAZADO' => 'danger',
                                            'PENDIENTE' => 'warning',
                                            'OBSERVADO' => 'info',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">{{ $aprobacion->estado_texto }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <select class="form-control form-control-sm" style="width: 70px; display:inline-block;"
                            wire:model.live="paginado">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <small class="text-muted">registros por página</small>
                </div>
                <div>
                    {{ $aprobaciones->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-double fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No hay aprobaciones registradas</h5>
                <p class="text-muted">
                    @if($buscador || $tipo_documento || $estado || $nivel_aprobacion)
                        No se encontraron resultados con los filtros aplicados.
                    @else
                        No existen flujos de aprobación en el sistema.
                    @endif
                </p>
            </div>
        @endif
    </x-adminlte-card>
</div>
