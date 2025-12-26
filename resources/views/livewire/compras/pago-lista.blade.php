<div>
    <!-- Resumen de Totales -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card {{ $totalGeneral >= 0 ? 'bg-success' : 'bg-info' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-white mb-0">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>
                                Cuentas por Pagar (Neto)
                            </h5>
                            <small class="text-white-50">Total pendiente (Facturas - Notas de Crédito)</small>
                        </div>
                        <div class="text-right">
                            <h3 class="text-white mb-0 font-weight-bold">
                                Gs. {{ number_format($totalGeneral, 0, ',', '.') }}
                            </h3>
                            <small class="text-white-50">{{ $cuentas->total() }} registro(s)</small>
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
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar por número de documento o proveedor...">
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

    <!-- Tabla de Cuentas por Pagar -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nro. Documento</th>
                            <th>Timbrado</th>
                            <th>Proveedor</th>
                            <th>Fecha Emisión</th>
                            <th>Fecha Vencimiento</th>
                            <th>Tipo</th>
                            <th>Condición</th>
                            <th class="text-right">Saldo Pendiente (Gs.)</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cuentas as $cuenta)
                        <tr class="{{ $cuenta->tipo == 'NOTA_CREDITO' ? 'table-info' : '' }}">
                            <td>
                                <strong>{{ $cuenta->numero_documento }}</strong>
                                @if($cuenta->tipo == 'NOTA_CREDITO')
                                    <br><small class="badge badge-info">Nota de Crédito</small>
                                @endif
                            </td>
                            <td>
                                @if($cuenta->timbrado)
                                    <small class="text-muted">{{ $cuenta->timbrado }}</small>
                                @else
                                    <small class="text-muted">N/A</small>
                                @endif
                            </td>
                            <td>
                                {{ $cuenta->proveedor->razon_social ?? 'N/A' }}
                                @if($cuenta->proveedor)
                                    <br><small class="text-muted">RUC: {{ $cuenta->proveedor->ruc ?? '' }}</small>
                                @endif
                            </td>
                            <td>{{ $cuenta->fecha_emision ? $cuenta->fecha_emision->format('d/m/Y') : '-' }}</td>
                            <td>{{ $cuenta->fecha_vencimiento ? $cuenta->fecha_vencimiento->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($cuenta->tipo == 'CONTADO')
                                    <span class="badge badge-info">Contado</span>
                                @elseif($cuenta->tipo == 'CREDITO')
                                    <span class="badge badge-warning">Crédito</span>
                                @elseif($cuenta->tipo == 'NOTA_CREDITO')
                                    <span class="badge badge-primary">N/C</span>
                                @elseif($cuenta->tipo == 'NOTA_DEBITO')
                                    <span class="badge badge-danger">N/D</span>
                                @else
                                    {{ $cuenta->tipo }}
                                @endif
                            </td>
                            <td>
                                <small>
                                    @switch($cuenta->condicion_pago)
                                        @case('CONTADO') Contado @break
                                        @case('7_DIAS') 7 Días @break
                                        @case('15_DIAS') 15 Días @break
                                        @case('30_DIAS') 30 Días @break
                                        @case('60_DIAS') 60 Días @break
                                        @case('90_DIAS') 90 Días @break
                                        @default {{ $cuenta->condicion_pago }}
                                    @endswitch
                                </small>
                            </td>
                            <td class="text-right {{ $cuenta->saldo_pendiente < 0 ? 'text-danger' : '' }}">
                                <strong>{{ number_format($cuenta->saldo_pendiente ?? 0, 0, ',', '.') }}</strong>
                                @if($cuenta->tipo == 'NOTA_CREDITO')
                                    <br><small class="text-muted">(Reduce deuda)</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($cuenta->estado == 'PENDIENTE')
                                    <span class="badge badge-warning">Pendiente</span>
                                @elseif($cuenta->estado == 'PARCIALMENTE_PAGADO')
                                    <span class="badge badge-info">Parcial</span>
                                @elseif($cuenta->estado == 'APLICADA')
                                    <span class="badge badge-success">Aplicada</span>
                                @elseif($cuenta->estado == 'PAGADO')
                                    <span class="badge badge-success">Pagado</span>
                                @else
                                    <span class="badge badge-secondary">{{ $cuenta->estado }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                <p>No se encontraron cuentas por pagar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($cuentas->count() > 0)
                    <tfoot>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="7" class="text-right">TOTAL DE ESTA PÁGINA:</td>
                            <td class="text-right">{{ number_format($cuentas->sum('saldo_pendiente'), 0, ',', '.') }}</td>
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
        {{ $cuentas->links() }}
    </div>
</div>
