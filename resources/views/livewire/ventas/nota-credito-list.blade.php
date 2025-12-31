<div>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('ventas.notas-credito.crear') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Nota de Crédito
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
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Buscar por número, factura o cliente...">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filter_estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="BORRADOR">Borrador</option>
                        <option value="EMITIDA">Emitida</option>
                        <option value="APLICADA">Aplicada</option>
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
                            <th>Número NC</th>
                            <th>Factura</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notasCredito as $nc)
                            <tr>
                                <td>
                                    <a href="{{ route('ventas.notas-credito.show', $nc) }}" class="text-primary">
                                        {{ $nc->numero_timbrado }}-{{ $nc->numero_nota }}
                                    </a>
                                    @if($nc->es_electronica)
                                        <i class="fas fa-laptop text-success ml-1" title="Nota Electrónica"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($nc->factura)
                                        <a href="{{ route('ventas.facturas.show', $nc->factura) }}" class="text-info">
                                            {{ $nc->factura->numero_completo }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $nc->fecha_emision->format('d/m/Y') }}</td>
                                <td>{{ $nc->cliente->nombre }}</td>
                                <td class="text-right">₲ {{ number_format($nc->total, 0, ',', '.') }}</td>
                                <td>
                                    @switch($nc->estado)
                                        @case('BORRADOR')
                                            <span class="badge badge-secondary">Borrador</span>
                                            @break
                                        @case('EMITIDA')
                                            <span class="badge badge-primary">Emitida</span>
                                            @break
                                        @case('APLICADA')
                                            <span class="badge badge-success">Aplicada</span>
                                            @break
                                        @case('ANULADA')
                                            <span class="badge badge-danger">Anulada</span>
                                            @break
                                        @default
                                            <span class="badge badge-light">{{ $nc->estado }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('ventas.notas-credito.show', $nc) }}" class="btn btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($nc->estado == 'BORRADOR')
                                            <a href="{{ route('ventas.notas-credito.editar', $nc) }}" class="btn btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No se encontraron notas de crédito</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-3">
                {{ $notasCredito->links() }}
            </div>
        </div>
    </div>
</div>
