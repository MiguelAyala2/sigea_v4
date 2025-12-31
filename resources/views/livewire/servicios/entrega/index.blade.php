<div>
    {{-- Estadísticas --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pendiente_entrega'] }}</h3>
                    <p>Pendientes de Entrega</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['entregadas_hoy'] }}</h3>
                    <p>Entregadas Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['entregadas_mes'] }}</h3>
                    <p>Entregadas este Mes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </div>

    <x-adminlte-card theme="light" title="Órdenes de Servicio" icon="fas fa-check-circle">
        {{-- Filtros --}}
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <x-adminlte-input name="buscador" wire:model.live.debounce.300ms="buscador"
                    placeholder="Buscar por código, cliente o equipo..."
                    igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-2 mb-2">
                <x-adminlte-select name="estado" wire:model.live="estado"
                    igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los estados</option>
                    <option value="finalizada">Finalizadas</option>
                    <option value="entregada">Entregadas</option>
                </x-adminlte-select>
            </div>

            <div class="col-md-2 mb-2">
                <x-adminlte-input-date name="fecha_desde" wire:model.live="fecha_desde"
                    placeholder="Desde" igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-secondary">
                            <i class="fas fa-calendar"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            <div class="col-md-2 mb-2">
                <x-adminlte-input-date name="fecha_hasta" wire:model.live="fecha_hasta"
                    placeholder="Hasta" igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-secondary">
                            <i class="fas fa-calendar"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            <div class="col-md-2 mb-2">
                <button wire:click="limpiarFiltros" class="btn btn-secondary btn-sm btn-block">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
            </div>
        </div>

        {{-- Información de registros --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="small text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Mostrando {{ $ordenes->firstItem() ?? 0 }} a {{ $ordenes->lastItem() ?? 0 }}
                    de {{ $ordenes->total() }} órdenes
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        @if($ordenes->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 12%">Código</th>
                            <th style="width: 10%">Fecha Orden</th>
                            <th style="width: 18%">Cliente</th>
                            <th style="width: 18%">Equipo</th>
                            <th style="width: 10%">Técnico</th>
                            <th style="width: 10%">Progreso</th>
                            <th style="width: 12%">Estado</th>
                            <th style="width: 10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenes as $orden)
                            <tr>
                                <td>
                                    <span class="badge badge-info">{{ $orden->codigo }}</span>
                                </td>
                                <td>
                                    {{ $orden->fecha_orden->format('d/m/Y') }}
                                </td>
                                <td>
                                    <strong>{{ $orden->cliente }}</strong>
                                </td>
                                <td>
                                    {{ $orden->equipo }}
                                </td>
                                <td>
                                    <small>{{ $orden->tecnico_nombre }}</small>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar progress-bar-striped
                                            @if($orden->progreso == 100) bg-success
                                            @elseif($orden->progreso >= 75) bg-info
                                            @elseif($orden->progreso >= 50) bg-warning
                                            @else bg-secondary
                                            @endif"
                                            role="progressbar"
                                            style="width: {{ $orden->progreso }}%"
                                            aria-valuenow="{{ $orden->progreso }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                            {{ $orden->progreso }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $estadoBadge = match($orden->estado) {
                                            'finalizada' => 'badge-success',
                                            'entregada' => 'badge-primary',
                                            'en_proceso' => 'badge-info',
                                            'pausada' => 'badge-warning',
                                            'cancelada' => 'badge-danger',
                                            default => 'badge-secondary'
                                        };
                                        $estadoTexto = match($orden->estado) {
                                            'finalizada' => 'Finalizada',
                                            'entregada' => 'Entregada',
                                            'en_proceso' => 'En Proceso',
                                            'pausada' => 'Pausada',
                                            'cancelada' => 'Cancelada',
                                            default => 'Pendiente'
                                        };
                                    @endphp
                                    <span class="badge {{ $estadoBadge }}">{{ $estadoTexto }}</span>
                                    @if($orden->fecha_entrega)
                                        <br><small class="text-muted">{{ $orden->fecha_entrega->format('d/m/Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if($orden->estado == 'finalizada')
                                            <button wire:click="abrirModalEntrega({{ $orden->id }})"
                                                class="btn btn-success" title="Registrar Entrega">
                                                <i class="fas fa-handshake"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-info" title="Ver Detalle">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-secondary" title="Imprimir">
                                            <i class="fas fa-print"></i>
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
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <small class="text-muted">registros por página</small>
                </div>
                <div>
                    {{ $ordenes->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-check fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No hay órdenes de servicio</h5>
                <p class="text-muted">
                    @if($buscador || $estado || $fecha_desde || $fecha_hasta)
                        No se encontraron órdenes con los filtros aplicados.
                    @else
                        No hay órdenes de servicio registradas.
                    @endif
                </p>
            </div>
        @endif
    </x-adminlte-card>

    {{-- Modal de Entrega --}}
    @if($mostrarModalEntrega && $ordenSeleccionada)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title">
                            <i class="fas fa-handshake mr-2"></i>
                            Registrar Entrega - {{ $ordenSeleccionada->codigo }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarModalEntrega">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- Información de la Orden --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Información de la Orden</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Cliente:</strong><br>{{ $ordenSeleccionada->cliente }}</p>
                                        <p><strong>Equipo:</strong><br>{{ $ordenSeleccionada->equipo }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Técnico:</strong><br>{{ $ordenSeleccionada->tecnico_nombre }}</p>
                                        <p><strong>Fecha Finalización:</strong><br>
                                            {{ $ordenSeleccionada->fecha_finalizacion ? $ordenSeleccionada->fecha_finalizacion->format('d/m/Y H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Formulario de Entrega --}}
                        <form wire:submit.prevent="registrarEntrega">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-adminlte-input-date name="fecha_entrega" label="Fecha de Entrega"
                                        wire:model="fecha_entrega" required>
                                        <x-slot name="prependSlot">
                                            <div class="input-group-text bg-success">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </x-slot>
                                    </x-adminlte-input-date>
                                    @error('fecha_entrega') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <x-adminlte-input name="recibido_por" label="Recibido Por"
                                        wire:model="recibido_por" placeholder="Nombre de quien recibe" required>
                                        <x-slot name="prependSlot">
                                            <div class="input-group-text bg-success">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </x-slot>
                                    </x-adminlte-input>
                                    @error('recibido_por') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <x-adminlte-input name="documento_receptor" label="Documento del Receptor"
                                        wire:model="documento_receptor" placeholder="C.I. o RUC (opcional)">
                                        <x-slot name="prependSlot">
                                            <div class="input-group-text bg-success">
                                                <i class="fas fa-id-card"></i>
                                            </div>
                                        </x-slot>
                                    </x-adminlte-input>
                                    @error('documento_receptor') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-12">
                                    <x-adminlte-textarea name="observaciones_entrega" label="Observaciones de Entrega"
                                        wire:model="observaciones_entrega"
                                        placeholder="Observaciones adicionales sobre la entrega (opcional)"
                                        rows="3">
                                    </x-adminlte-textarea>
                                    @error('observaciones_entrega') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarModalEntrega">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-success" wire:click="registrarEntrega">
                            <i class="fas fa-check mr-1"></i> Confirmar Entrega
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
