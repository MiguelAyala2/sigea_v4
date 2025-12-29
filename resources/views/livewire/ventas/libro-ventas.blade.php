<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>
                Filtros
            </h3>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mes">Mes</label>
                        <select
                            wire:model.live="mes"
                            id="mes"
                            class="form-control">
                            @foreach($meses as $num => $nombre)
                                <option value="{{ $num }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="anio">Año</label>
                        <select
                            wire:model.live="anio"
                            id="anio"
                            class="form-control">
                            @foreach($anios as $a)
                                <option value="{{ $a }}">{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="timbrado_id">Timbrado</label>
                        <select
                            wire:model.live="timbrado_id"
                            id="timbrado_id"
                            class="form-control">
                            <option value="">Todos los timbrados</option>
                            @foreach($timbrados as $timbrado)
                                <option value="{{ $timbrado->id }}">
                                    {{ $timbrado->numero_timbrado }} ({{ $timbrado->establecimiento }}-{{ $timbrado->punto_expedicion }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="estado_factura">Estado</label>
                        <select
                            wire:model.live="estado_factura"
                            id="estado_factura"
                            class="form-control">
                            <option value="">Todos</option>
                            <option value="EMITIDA">Emitida</option>
                            <option value="ANULADA">Anulada</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button
                        wire:click="exportarExcel"
                        class="btn btn-success btn-block">
                        <i class="fas fa-file-excel mr-1"></i>
                        Exportar a Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen de Totales --}}
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gravada 10%</span>
                    <span class="info-box-number">₲ {{ number_format($total_gravada_10, 0, ',', '.') }}</span>
                    <small>IVA 10%: ₲ {{ number_format($total_iva_10, 0, ',', '.') }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gravada 5%</span>
                    <span class="info-box-number">₲ {{ number_format($total_gravada_5, 0, ',', '.') }}</span>
                    <small>IVA 5%: ₲ {{ number_format($total_iva_5, 0, ',', '.') }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box bg-secondary">
                <span class="info-box-icon"><i class="fas fa-ban"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Exenta</span>
                    <span class="info-box-number">₲ {{ number_format($total_exenta, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total General</span>
                    <span class="info-box-number">₲ {{ number_format($total_general, 0, ',', '.') }}</span>
                    <small>IVA Total: ₲ {{ number_format($total_iva, 0, ',', '.') }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Facturas --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-book mr-2"></i>
                Libro de Ventas IVA - {{ $meses[$mes] }} {{ $anio }}
            </h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 90px;">Fecha</th>
                            <th style="width: 130px;">N° Factura</th>
                            <th style="width: 100px;">Timbrado</th>
                            <th>Cliente</th>
                            <th style="width: 120px;">RUC/CI</th>
                            <th class="text-right" style="width: 120px;">Gravada 10%</th>
                            <th class="text-right" style="width: 100px;">IVA 10%</th>
                            <th class="text-right" style="width: 120px;">Gravada 5%</th>
                            <th class="text-right" style="width: 100px;">IVA 5%</th>
                            <th class="text-right" style="width: 120px;">Exenta</th>
                            <th class="text-right" style="width: 120px;">Total</th>
                            <th style="width: 80px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                            @php
                                // Calcular base gravada 10% y 5%
                                $gravada_10 = $factura->iva_10 > 0 ? $factura->subtotal * ($factura->iva_10 / ($factura->subtotal + $factura->iva_10)) : 0;
                                $gravada_5 = $factura->iva_5 > 0 ? $factura->subtotal * ($factura->iva_5 / ($factura->subtotal + $factura->iva_5)) : 0;
                            @endphp
                            <tr class="{{ $factura->estado === 'ANULADA' ? 'table-danger' : '' }}">
                                <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                                <td>
                                    <strong>{{ $factura->numero_factura }}</strong>
                                    @if($factura->es_electronica)
                                        <br><small class="badge badge-info"><i class="fas fa-bolt"></i> E-Factura</small>
                                    @endif
                                </td>
                                <td><small>{{ $factura->numero_timbrado }}</small></td>
                                <td>
                                    <strong>{{ $factura->cliente->nombre ?? 'Sin cliente' }}</strong>
                                    @if($factura->estado === 'ANULADA')
                                        <br><small class="text-danger"><i class="fas fa-ban"></i> ANULADA</small>
                                    @endif
                                </td>
                                <td>
                                    @if($factura->cliente)
                                        {{ $factura->cliente->ruc ?: $factura->cliente->ci }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($factura->estado === 'ANULADA')
                                        <span class="text-muted">-</span>
                                    @else
                                        {{ $gravada_10 > 0 ? '₲ ' . number_format($gravada_10, 0, ',', '.') : '-' }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($factura->estado === 'ANULADA')
                                        <span class="text-muted">-</span>
                                    @else
                                        {{ $factura->iva_10 > 0 ? '₲ ' . number_format($factura->iva_10, 0, ',', '.') : '-' }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($factura->estado === 'ANULADA')
                                        <span class="text-muted">-</span>
                                    @else
                                        {{ $gravada_5 > 0 ? '₲ ' . number_format($gravada_5, 0, ',', '.') : '-' }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($factura->estado === 'ANULADA')
                                        <span class="text-muted">-</span>
                                    @else
                                        {{ $factura->iva_5 > 0 ? '₲ ' . number_format($factura->iva_5, 0, ',', '.') : '-' }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($factura->estado === 'ANULADA')
                                        <span class="text-muted">-</span>
                                    @else
                                        {{ $factura->exenta > 0 ? '₲ ' . number_format($factura->exenta, 0, ',', '.') : '-' }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($factura->estado === 'ANULADA')
                                        <span class="text-muted text-decoration-line-through">₲ {{ number_format($factura->total, 0, ',', '.') }}</span>
                                    @else
                                        <strong>₲ {{ number_format($factura->total, 0, ',', '.') }}</strong>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($factura->estado === 'EMITIDA')
                                        <span class="badge badge-success">Emitida</span>
                                    @elseif($factura->estado === 'ANULADA')
                                        <span class="badge badge-danger">Anulada</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <i class="fas fa-info-circle mr-2 text-muted"></i>
                                    <span class="text-muted">No hay facturas registradas en este período</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($facturas->count() > 0)
                        <tfoot class="table-success">
                            <tr>
                                <td colspan="5"><strong>TOTALES DEL PERÍODO:</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($total_gravada_10, 0, ',', '.') }}</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($total_iva_10, 0, ',', '.') }}</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($total_gravada_5, 0, ',', '.') }}</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($total_iva_5, 0, ',', '.') }}</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($total_exenta, 0, ',', '.') }}</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($total_general, 0, ',', '.') }}</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        @if($facturas->hasPages())
            <div class="card-footer">
                {{ $facturas->links() }}
            </div>
        @endif
    </div>

    {{-- Información Adicional --}}
    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Información:</strong> El Libro de Ventas IVA muestra todas las facturas emitidas en el período seleccionado.
        Las facturas anuladas se muestran resaltadas en rojo y no se incluyen en los totales.
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('info'))
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-info"></i>
            {{ session('info') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-ban"></i>
            {{ session('error') }}
        </div>
    @endif
</div>
