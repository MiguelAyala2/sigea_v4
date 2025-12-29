<div>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('ventas.facturas.crear') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Factura
                    </a>
                </div>
                <div class="col-md-6 text-right">
                    <button wire:click="limpiarFiltros" class="btn btn-secondary">
                        <i class="fas fa-eraser"></i> Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- Filtros --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Buscar por número o cliente...">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filter_estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="BORRADOR">Borrador</option>
                        <option value="EMITIDA">Emitida</option>
                        <option value="PAGADA">Pagada</option>
                        <option value="PARCIALMENTE_PAGADA">Parcialmente Pagada</option>
                        <option value="VENCIDA">Vencida</option>
                        <option value="ANULADA">Anulada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" wire:model.live="filter_fecha_desde" class="form-control" placeholder="Desde">
                </div>
                <div class="col-md-2">
                    <input type="date" wire:model.live="filter_fecha_hasta" class="form-control" placeholder="Hasta">
                </div>
            </div>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                            <tr>
                                <td>
                                    <a href="{{ route('ventas.facturas.show', $factura) }}" class="text-primary">
                                        {{ $factura->numero_completo }}
                                    </a>
                                    @if($factura->es_electronica)
                                        <i class="fas fa-laptop text-success ml-1" title="Factura Electrónica"></i>
                                    @endif
                                </td>
                                <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                                <td>{{ $factura->cliente->nombre }}</td>
                                <td class="text-right">₲ {{ number_format($factura->total, 0, ',', '.') }}</td>
                                <td>
                                    @switch($factura->estado)
                                        @case('BORRADOR')
                                            <span class="badge badge-secondary">Borrador</span>
                                            @break
                                        @case('EMITIDA')
                                            <span class="badge badge-primary">Emitida</span>
                                            @break
                                        @case('PAGADA')
                                            <span class="badge badge-success">Pagada</span>
                                            @break
                                        @case('PARCIALMENTE_PAGADA')
                                            <span class="badge badge-info">Parc. Pagada</span>
                                            @break
                                        @case('VENCIDA')
                                            <span class="badge badge-warning">Vencida</span>
                                            @break
                                        @case('ANULADA')
                                            <span class="badge badge-danger">Anulada</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('ventas.facturas.show', $factura) }}" class="btn btn-sm btn-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($factura->estado === 'BORRADOR')
                                        <a href="{{ route('ventas.facturas.edit', $factura) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No se encontraron facturas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-3">
                {{ $facturas->links() }}
            </div>
        </div>
    </div>
</div>
