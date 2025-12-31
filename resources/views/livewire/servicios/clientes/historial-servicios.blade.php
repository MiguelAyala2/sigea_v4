<div>
    {{-- Búsqueda de Cliente --}}
    <x-adminlte-card theme="light" title="Buscar Cliente" icon="fas fa-search">
        <div class="row">
            <div class="col-md-10 mb-2">
                <x-adminlte-input name="buscador" wire:model.live.debounce.300ms="buscador"
                    placeholder="Buscar por nombre, documento, teléfono o celular..."
                    wire:keyup="buscarClientes"
                    igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>

                {{-- Resultados de búsqueda --}}
                @if($mostrarResultados && count($resultadosBusqueda) > 0)
                    <div class="card position-absolute w-100" style="z-index: 1000;">
                        <div class="list-group list-group-flush">
                            @foreach($resultadosBusqueda as $cliente)
                                <a href="#" wire:click.prevent="seleccionarCliente({{ $cliente->id }})"
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $cliente->nombre }}</h6>
                                        <small>{{ $cliente->documento }}</small>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-phone mr-1"></i>{{ $cliente->telefono ?? 'N/A' }}
                                        @if($cliente->celular)
                                            | <i class="fas fa-mobile-alt ml-2 mr-1"></i>{{ $cliente->celular }}
                                        @endif
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-2 mb-2">
                @if($clienteSeleccionado)
                    <button wire:click="limpiarCliente" class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                @endif
            </div>
        </div>
    </x-adminlte-card>

    {{-- Datos del Cliente Seleccionado --}}
    @if($clienteSeleccionado)
        <x-adminlte-card theme="info" title="Datos del Cliente" icon="fas fa-user">
            <div class="row">
                <div class="col-md-3">
                    <p><strong>Cliente:</strong><br>{{ $clienteSeleccionado->nombre }}</p>
                    <p><strong>Documento:</strong><br>{{ $clienteSeleccionado->documento }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Teléfono:</strong><br>{{ $clienteSeleccionado->telefono ?? 'N/A' }}</p>
                    <p><strong>Celular:</strong><br>{{ $clienteSeleccionado->celular ?? 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Email:</strong><br>{{ $clienteSeleccionado->email ?? 'N/A' }}</p>
                    <p><strong>Dirección:</strong><br>{{ Str::limit($clienteSeleccionado->direccion ?? 'N/A', 30) }}</p>
                </div>
                <div class="col-md-3">
                    <p>
                        <strong>Total Servicios:</strong><br>
                        <span class="badge badge-primary badge-lg">{{ $stats['total_servicios'] }}</span>
                    </p>
                    <p>
                        <strong>Última Visita:</strong><br>
                        {{ $stats['ultima_visita'] ? $stats['ultima_visita']->format('d/m/Y') : 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['completados'] }}</h3>
                            <p>Completados</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['en_proceso'] }}</h3>
                            <p>En Proceso</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cog"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $stats['pendientes'] }}</h3>
                            <p>Pendientes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_servicios'] }}</h3>
                            <p>Total</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                </div>
            </div>
        </x-adminlte-card>

        {{-- Filtros --}}
        <x-adminlte-card theme="light" title="Filtros" icon="fas fa-filter" collapsible>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <x-adminlte-select name="tipo_servicio" wire:model.live="tipo_servicio"
                        label="Tipo de Servicio" igroup-size="sm" fgroup-class="mb-0">
                        <option value="">Todos los tipos</option>
                        @foreach(\App\Models\Servicios\SolicitudServicio::TIPOS_SERVICIO as $key => $valor)
                            <option value="{{ $key }}">{{ $valor }}</option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                <div class="col-md-3 mb-2">
                    <x-adminlte-select name="estado" wire:model.live="estado"
                        label="Estado" igroup-size="sm" fgroup-class="mb-0">
                        <option value="">Todos los estados</option>
                        @foreach(\App\Models\Servicios\SolicitudServicio::ESTADOS as $key => $valor)
                            <option value="{{ $key }}">{{ $valor }}</option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                <div class="col-md-2 mb-2">
                    <x-adminlte-input-date name="fecha_desde" wire:model.live="fecha_desde"
                        label="Desde" igroup-size="sm" fgroup-class="mb-0">
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-secondary">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>

                <div class="col-md-2 mb-2">
                    <x-adminlte-input-date name="fecha_hasta" wire:model.live="fecha_hasta"
                        label="Hasta" igroup-size="sm" fgroup-class="mb-0">
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-secondary">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>

                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button wire:click="limpiarFiltros" class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-eraser"></i> Limpiar Filtros
                    </button>
                </div>
            </div>
        </x-adminlte-card>

        {{-- Historial de Servicios --}}
        <x-adminlte-card theme="light" title="Historial de Servicios" icon="fas fa-history">
            @if($servicios->count() > 0)
                {{-- Información de paginación --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="small text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Mostrando {{ $servicios->firstItem() ?? 0 }} a {{ $servicios->lastItem() ?? 0 }}
                            de {{ $servicios->total() }} servicios
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10%">Fecha</th>
                                <th style="width: 12%">N° Solicitud</th>
                                <th style="width: 20%">Producto/Equipo</th>
                                <th style="width: 15%">Tipo Servicio</th>
                                <th style="width: 12%">Prioridad</th>
                                <th style="width: 12%">Estado</th>
                                <th style="width: 19%">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($servicios as $servicio)
                                <tr>
                                    <td>
                                        {{ $servicio->fecha->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $servicio->numero_solicitud }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($servicio->producto)
                                            <strong>{{ $servicio->producto->nombre }}</strong>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $iconClass = match($servicio->tipo_servicio) {
                                                'mantenimiento' => 'fas fa-wrench text-primary',
                                                'reparacion' => 'fas fa-tools text-warning',
                                                'diagnostico' => 'fas fa-stethoscope text-info',
                                                default => 'fas fa-cog text-muted'
                                            };
                                        @endphp
                                        <i class="{{ $iconClass }} mr-1"></i>
                                        {{ \App\Models\Servicios\SolicitudServicio::TIPOS_SERVICIO[$servicio->tipo_servicio] ?? $servicio->tipo_servicio }}
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($servicio->prioridad) {
                                                'alta' => 'badge-danger',
                                                'media' => 'badge-warning',
                                                'baja' => 'badge-secondary',
                                                default => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ \App\Models\Servicios\SolicitudServicio::PRIORIDADES[$servicio->prioridad] ?? $servicio->prioridad }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $estadoBadge = match($servicio->estado) {
                                                'completado' => 'badge-success',
                                                'en_proceso' => 'badge-info',
                                                'pendiente' => 'badge-secondary',
                                                default => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $estadoBadge }}">
                                            {{ \App\Models\Servicios\SolicitudServicio::ESTADOS[$servicio->estado] ?? $servicio->estado }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($servicio->observaciones, 50) ?? '-' }}</small>
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
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <small class="text-muted">registros por página</small>
                    </div>
                    <div>
                        {{ $servicios->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay servicios registrados</h5>
                    <p class="text-muted">
                        Este cliente no tiene servicios registrados en el período seleccionado.
                    </p>
                </div>
            @endif
        </x-adminlte-card>
    @else
        {{-- Mensaje cuando no hay cliente seleccionado --}}
        <x-adminlte-card theme="light">
            <div class="text-center py-5">
                <i class="fas fa-user-search fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Busque un cliente para ver su historial</h5>
                <p class="text-muted">
                    Utilice el buscador de arriba para encontrar un cliente por nombre, documento o teléfono.
                </p>
            </div>
        </x-adminlte-card>
    @endif
</div>
