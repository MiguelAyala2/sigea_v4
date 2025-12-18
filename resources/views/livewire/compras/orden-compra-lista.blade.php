<div>
    <!-- Filtros -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Buscar</label>
                        <input type="text" wire:model.live.debounce.500ms="search" class="form-control" placeholder="Número de orden, proveedor...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado</label>
                        <select wire:model.live="estado_filtro" class="form-control">
                            <option value="">Todos</option>
                            <option value="PENDIENTE">Pendiente</option>
                            <option value="APROBADO">Aprobado</option>
                            <option value="RECHAZADO">Rechazado</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Fecha Desde</label>
                        <input type="date" wire:model.live="fecha_desde" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Fecha Hasta</label>
                        <input type="date" wire:model.live="fecha_hasta" class="form-control">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button wire:click="limpiarFiltros" class="btn btn-secondary btn-block" title="Limpiar filtros">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Órdenes -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Condición Pago</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Estado</th>
                            <th>Fecha Entrega</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenes as $orden)
                        <tr>
                            <td>
                                <strong>{{ $orden->numero_orden }}</strong>
                                @if($orden->presupuesto_id)
                                    <br><small class="text-muted">Presup: {{ $orden->presupuesto->numero_presupuesto ?? '-' }}</small>
                                @endif
                            </td>
                            <td>{{ $orden->fecha_orden->format('d/m/Y') }}</td>
                            <td>{{ $orden->proveedor->nombre_fantasia ?? '-' }}</td>
                            <td>
                                @switch($orden->condicion_pago)
                                    @case('CONTADO') Contado @break
                                    @case('7_DIAS') 7 Días @break
                                    @case('15_DIAS') 15 Días @break
                                    @case('30_DIAS') 30 Días @break
                                    @case('60_DIAS') 60 Días @break
                                    @case('90_DIAS') 90 Días @break
                                @endswitch
                            </td>
                            <td class="text-right">₲ {{ number_format($orden->total, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @switch($orden->estado)
                                    @case('PENDIENTE')
                                        <span class="badge badge-warning">Pendiente</span>
                                        @break
                                    @case('APROBADO')
                                        <span class="badge badge-success">Aprobado</span>
                                        @break
                                    @case('RECHAZADO')
                                        <span class="badge badge-danger">Rechazado</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $orden->estado }}</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $orden->fecha_entrega_esperada ? $orden->fecha_entrega_esperada->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('compras.ordenes.show', $orden->id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($orden->estado == 'PENDIENTE')
                                <a href="{{ route('compras.ordenes.edit', $orden->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-inbox"></i> No se encontraron órdenes de compra
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $ordenes->links() }}
            </div>
        </div>
    </div>
</div>
