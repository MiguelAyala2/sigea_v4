<div>
    <!-- Filtros -->
    <div class="row mb-3">
        <div class="col-md-3">
            <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar...">
        </div>

        <div class="col-md-2">
            <select wire:model.live="estado" class="form-control">
                <option value="">Todos los estados</option>
                <option value="PENDIENTE">Pendiente</option>
                <option value="APROBADO">Aprobado</option>
                <option value="RECHAZADO">Rechazado</option>
            </select>
        </div>
        <div class="col-md-2">
            <select wire:model.live="prioridad" class="form-control">
                <option value="">Todas las prioridades</option>
                <option value="NORMAL">Normal</option>
                <option value="URGENTE">Urgente</option>
                <option value="CRITICA">Crítica</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" wire:model.live="fecha_desde" class="form-control" placeholder="Fecha desde">
        </div>
        <div class="col-md-2">
            <input type="date" wire:model.live="fecha_hasta" class="form-control" placeholder="Fecha hasta">
        </div>
        <div class="col-md-1">
            <button wire:click="limpiarFiltros" class="btn btn-secondary btn-block" title="Limpiar filtros">
                <i class="fas fa-eraser"></i>
            </button>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Fecha</th>
                    <th>Solicitante</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th class="text-right">Total Est.</th>
                    {{-- <th class="text-center">% Ordenado</th> --}}
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidos as $pedido)
                <tr>
                    <td>
                        <a href="{{ route('compras.pedidos.show', $pedido->id) }}">
                            {{ $pedido->numero_pedido }}
                        </a>
                    </td>
                    <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                    <td>{{ $pedido->usuarioSolicitante->name }}</td>
                    <td>{{ $pedido->tipo_pedido }}</td>
                    <td>
                        <span class="badge badge-{{ $pedido->prioridad == 'CRITICA' ? 'danger' : ($pedido->prioridad == 'URGENTE' ? 'warning' : 'info') }}">
                            {{ $pedido->prioridad }}
                        </span>
                    </td>
                    <td class="text-right">₲ {{ number_format($pedido->total_estimado, 0, ',', '.') }}</td>
                    {{-- <td class="text-center">{{ number_format($pedido->porcentaje_ordenado, 1) }}%</td> --}}
                    <td>
                        <span class="badge badge-{{ $pedido->estado == 'APROBADO' ? 'success' : ($pedido->estado == 'RECHAZADO' ? 'danger' : 'warning') }}">
                            {{ $pedido->estado }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('compras.pedidos.show', $pedido->id) }}" class="btn btn-sm btn-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>

                        @if($pedido->estado == 'PENDIENTE')
                        <a href="{{ route('compras.pedidos.edit', $pedido->id) }}" class="btn btn-sm btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>

                        <button wire:click="eliminar({{ $pedido->id }})" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro?')">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No hay pedidos de compra registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-3">
        {{ $pedidos->links() }}
    </div>
</div>
