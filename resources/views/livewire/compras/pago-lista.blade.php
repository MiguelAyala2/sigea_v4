<div>
    <!-- Resumen de Totales -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-white mb-0">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>
                                Compras/Facturas Aprobadas
                            </h5>
                            <small class="text-white-50">Total de facturas aprobadas pendientes de pago</small>
                        </div>
                        <div class="text-right">
                            <h3 class="text-white mb-0 font-weight-bold">
                                Gs. {{ number_format($totalGeneral, 0, ',', '.') }}
                            </h3>
                            <small class="text-white-50">{{ $compras->total() }} factura(s)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar por número de factura o proveedor...">
                </div>
                <div class="col-md-3">
                    <input type="date" wire:model.live="fecha_desde" class="form-control" placeholder="Desde">
                </div>
                <div class="col-md-3">
                    <input type="date" wire:model.live="fecha_hasta" class="form-control" placeholder="Hasta">
                </div>
                <div class="col-md-1">
                    <button wire:click="limpiarFiltros" class="btn btn-secondary btn-block" title="Limpiar filtros">
                        <i class="fas fa-eraser"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Compras/Facturas Aprobadas -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nro. Factura</th>
                            <th>Timbrado</th>
                            <th>Proveedor</th>
                            <th>Fecha Emisión</th>
                            <th>Fecha Vencimiento</th>
                            <th>Tipo</th>
                            <th>Condición</th>
                            <th class="text-right">Total (Gs.)</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compras as $compra)
                        <tr>
                            <td>
                                <strong>{{ $compra->numero_factura }}</strong>
                            </td>
                            <td>
                                @if($compra->timbrado)
                                    <small class="text-muted">{{ $compra->timbrado }}</small>
                                @else
                                    <small class="text-muted">N/A</small>
                                @endif
                            </td>
                            <td>
                                {{ $compra->proveedor->razon_social ?? 'N/A' }}
                                @if($compra->proveedor)
                                    <br><small class="text-muted">RUC: {{ $compra->proveedor->ruc ?? '' }}</small>
                                @endif
                            </td>
                            <td>{{ $compra->fecha_emision ? $compra->fecha_emision->format('d/m/Y') : '-' }}</td>
                            <td>{{ $compra->fecha_vencimiento ? $compra->fecha_vencimiento->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($compra->tipo_factura == 'CONTADO')
                                    <span class="badge badge-info">Contado</span>
                                @elseif($compra->tipo_factura == 'CREDITO')
                                    <span class="badge badge-warning">Crédito</span>
                                @else
                                    {{ $compra->tipo_factura }}
                                @endif
                            </td>
                            <td>
                                <small>
                                    @switch($compra->condicion_pago)
                                        @case('CONTADO') Contado @break
                                        @case('7_DIAS') 7 Días @break
                                        @case('15_DIAS') 15 Días @break
                                        @case('30_DIAS') 30 Días @break
                                        @case('60_DIAS') 60 Días @break
                                        @case('90_DIAS') 90 Días @break
                                        @default {{ $compra->condicion_pago }}
                                    @endswitch
                                </small>
                            </td>
                            <td class="text-right">
                                <strong>{{ number_format($compra->total ?? 0, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-success">Aprobada</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                <p>No se encontraron compras/facturas aprobadas</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($compras->count() > 0)
                    <tfoot>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="7" class="text-right">TOTAL DE ESTA PÁGINA:</td>
                            <td class="text-right">{{ number_format($compras->sum('total'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-3">
        {{ $compras->links() }}
    </div>
</div>
