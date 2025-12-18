<div>
    <!-- Filtros -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Buscar</label>
                        <input type="text" wire:model.live.debounce.500ms="search" class="form-control" placeholder="Número de factura, proveedor...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Estado</label>
                        <select wire:model.live="estado_filtro" class="form-control">
                            <option value="">Todos</option>
                            <option value="BORRADOR">Borrador</option>
                            <option value="PENDIENTE">Pendiente</option>
                            <option value="APROBADA">Aprobada</option>
                            <option value="PAGADA">Pagada</option>
                            <option value="PARCIAL">Pago Parcial</option>
                            <option value="ANULADA">Anulada</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Desde</label>
                        <input type="date" wire:model.live="fecha_desde" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Hasta</label>
                        <input type="date" wire:model.live="fecha_hasta" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button wire:click="limpiarFiltros" class="btn btn-secondary btn-block">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Compras -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nro. Factura</th>
                            <th>Fecha Emisión</th>
                            <th>Proveedor</th>
                            <th>Orden Compra</th>
                            <th>Tipo</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compras as $compra)
                        <tr>
                            <td>
                                <strong>{{ $compra->numero_factura }}</strong>
                                @if($compra->timbrado)
                                    <br><small class="text-muted">Timb: {{ $compra->timbrado }}</small>
                                @endif
                            </td>
                            <td>{{ $compra->fecha_emision->format('d/m/Y') }}</td>
                            <td>{{ $compra->proveedor->nombre_fantasia ?? '-' }}</td>
                            <td>
                                @if($compra->ordenCompra)
                                    <a href="{{ route('compras.ordenes.show', $compra->ordenCompra->id) }}">
                                        {{ $compra->ordenCompra->numero_orden }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $compra->tipo_factura === 'CONTADO' ? 'success' : 'warning' }}">
                                    {{ $compra->tipo_factura }}
                                </span>
                            </td>
                            <td class="text-right">₲ {{ number_format($compra->total, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @switch($compra->estado)
                                    @case('BORRADOR')
                                        <span class="badge badge-secondary">Borrador</span>
                                        @break
                                    @case('PENDIENTE')
                                        <span class="badge badge-warning">Pendiente</span>
                                        @break
                                    @case('APROBADA')
                                        <span class="badge badge-info">Aprobada</span>
                                        @break
                                    @case('PAGADA')
                                        <span class="badge badge-success">Pagada</span>
                                        @break
                                    @case('PARCIAL')
                                        <span class="badge badge-primary">Pago Parcial</span>
                                        @break
                                    @case('ANULADA')
                                        <span class="badge badge-danger">Anulada</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-center">
                                <a href="{{ route('compras.compras.show', $compra->id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($compra->estado === 'BORRADOR' || $compra->estado === 'PENDIENTE')
                                <a href="{{ route('compras.compras.edit', $compra->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-inbox"></i> No se encontraron compras registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $compras->links() }}
            </div>
        </div>
    </div>
</div>
