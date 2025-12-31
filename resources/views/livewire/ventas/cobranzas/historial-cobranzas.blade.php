<div>
    <x-adminlte-card theme="light" title="Historial de Cobranzas" icon="fas fa-history">
        {{-- Estadísticas --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>₲ {{ number_format($stats['total_cobrado'], 0, ',', '.') }}</h3>
                        <p>Total Cobrado</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['cantidad'] }}</h3>
                        <p>Cobranzas Registradas</p>
                    </div>
                    <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>₲ {{ number_format($stats['efectivo'], 0, ',', '.') }}</h3>
                        <p>Efectivo</p>
                    </div>
                    <div class="icon"><i class="fas fa-cash-register"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>₲ {{ number_format($stats['otros'], 0, ',', '.') }}</h3>
                        <p>Otras Formas</p>
                    </div>
                    <div class="icon"><i class="fas fa-credit-card"></i></div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <x-adminlte-input name="buscador" wire:model.live.debounce.300ms="buscador"
                    placeholder="Buscar por factura, cliente, comprobante..."
                    igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-2 mb-2">
                <x-adminlte-select name="forma_pago" wire:model.live="forma_pago"
                    igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todas las formas</option>
                    @foreach(\App\Models\Ventas\MovimientoCaja::FORMAS_PAGO as $key => $valor)
                        <option value="{{ $key }}">{{ $valor }}</option>
                    @endforeach
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

        {{-- Información --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="small text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Mostrando {{ $cobranzas->firstItem() ?? 0 }} a {{ $cobranzas->lastItem() ?? 0 }}
                    de {{ $cobranzas->total() }} cobranzas
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        @if($cobranzas->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 10%">Fecha</th>
                            <th style="width: 12%">N° Comprobante</th>
                            <th style="width: 15%">Factura</th>
                            <th style="width: 20%">Cliente</th>
                            <th style="width: 12%">Forma de Pago</th>
                            <th style="width: 13%" class="text-right">Monto</th>
                            <th style="width: 10%">Usuario</th>
                            <th style="width: 8%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cobranzas as $cobranza)
                            <tr>
                                <td>
                                    {{ $cobranza->fecha_movimiento->format('d/m/Y') }}<br>
                                    <small class="text-muted">{{ $cobranza->hora_movimiento }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $cobranza->comprobante_numero ?? 'S/N' }}
                                    </span>
                                </td>
                                <td>
                                    @if($cobranza->factura)
                                        <strong>{{ $cobranza->factura->numero_factura }}</strong>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cobranza->factura && $cobranza->factura->cliente)
                                        <strong>{{ $cobranza->factura->cliente->nombre }}</strong>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $iconClass = match($cobranza->forma_pago) {
                                            'EFECTIVO' => 'fas fa-money-bill-wave text-success',
                                            'TARJETA_CREDITO', 'TARJETA_DEBITO' => 'fas fa-credit-card text-primary',
                                            'CHEQUE' => 'fas fa-money-check text-info',
                                            'TRANSFERENCIA' => 'fas fa-exchange-alt text-warning',
                                            'QR' => 'fas fa-qrcode text-secondary',
                                            default => 'fas fa-coins text-muted'
                                        };
                                    @endphp
                                    <i class="{{ $iconClass }} mr-2"></i>
                                    {{ $cobranza->forma_pago_texto }}
                                </td>
                                <td class="text-right">
                                    <strong class="text-success">
                                        ₲ {{ number_format($cobranza->monto, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td>
                                    <small>
                                        {{ $cobranza->usuarioResponsable->name ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" title="Ver detalle">
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
                    <tfoot>
                        <tr class="table-success font-weight-bold">
                            <td colspan="5" class="text-right">
                                <strong>TOTAL MOSTRADO:</strong>
                            </td>
                            <td class="text-right">
                                <strong>₲ {{ number_format($cobranzas->sum('monto'), 0, ',', '.') }}</strong>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
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
                    {{ $cobranzas->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No hay cobranzas registradas</h5>
                <p class="text-muted">
                    @if($buscador || $forma_pago || $fecha_desde || $fecha_hasta)
                        No se encontraron cobranzas con los filtros aplicados.
                    @else
                        No hay cobranzas registradas en el sistema.
                    @endif
                </p>
                <a href="{{ route('ventas.cobranzas.registrar') }}" class="btn btn-success">
                    <i class="fas fa-plus mr-2"></i> Registrar Primera Cobranza
                </a>
            </div>
        @endif
    </x-adminlte-card>
</div>
