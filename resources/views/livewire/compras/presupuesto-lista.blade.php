<div>
    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar por número o proveedor...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="estado_filter" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="PENDIENTE">Pendiente</option>
                        <option value="RECIBIDO">Recibido</option>
                        <option value="EN_EVALUACION">En Evaluación</option>
                        <option value="SELECCIONADO">Seleccionado/Aprobado</option>
                        <option value="RECHAZADO">Rechazado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" wire:model.live="fecha_desde" class="form-control" placeholder="Desde">
                </div>
                <div class="col-md-2">
                    <input type="date" wire:model.live="fecha_hasta" class="form-control" placeholder="Hasta">
                </div>
                <div class="col-md-1">
                    <button wire:click="$set('search', '')" class="btn btn-secondary btn-block" title="Limpiar filtros">
                        <i class="fas fa-eraser"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Presupuestos -->
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Número</th>
                    <th>Proveedor</th>
                    <th>Pedido Compra</th>
                    <th>Fecha Solicitud</th>
                    <th>Condición Pago</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presupuestos as $presupuesto)
                <tr>
                    <td>
                        <strong>{{ $presupuesto->numero_presupuesto }}</strong>
                    </td>
                    <td>
                        {{ $presupuesto->proveedor->nombre_fantasia ?? 'N/A' }}
                        <br>
                        <small class="text-muted">{{ $presupuesto->proveedor->razon_social ?? '' }}</small>
                    </td>
                    <td>
                        @if($presupuesto->pedidoCompra)
                            <a href="{{ route('compras.pedidos.show', $presupuesto->pedido_compra_id) }}">
                                {{ $presupuesto->pedidoCompra->numero_pedido }}
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $presupuesto->fecha_solicitud->format('d/m/Y') }}</td>
                    <td>
                        @switch($presupuesto->condicion_pago)
                            @case('CONTADO') Contado @break
                            @case('7_DIAS') 7 Días @break
                            @case('15_DIAS') 15 Días @break
                            @case('30_DIAS') 30 Días @break
                            @case('60_DIAS') 60 Días @break
                            @case('90_DIAS') 90 Días @break
                        @endswitch
                    </td>
                    <td class="text-right">
                        <strong>₲ {{ number_format($presupuesto->total, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-center">
                        @switch($presupuesto->estado)
                            @case('PENDIENTE')
                                <span class="badge badge-warning">Pendiente</span>
                                @break
                            @case('RECIBIDO')
                                <span class="badge badge-info">Recibido</span>
                                @break
                            @case('EN_EVALUACION')
                                <span class="badge badge-primary">En Evaluación</span>
                                @break
                            @case('SELECCIONADO')
                            @case('APROBADO')
                                <span class="badge badge-success">Aprobado</span>
                                @break
                            @case('RECHAZADO')
                                <span class="badge badge-danger">Rechazado</span>
                                @break
                            @default
                                <span class="badge badge-secondary">{{ $presupuesto->estado }}</span>
                                @break
                        @endswitch
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('compras.presupuestos.show', $presupuesto->id) }}"
                               class="btn btn-info" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('compras.presupuestos.edit', $presupuesto->id) }}"
                               class="btn btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No se encontraron presupuestos</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-3">
        {{ $presupuestos->links() }}
    </div>
</div>
