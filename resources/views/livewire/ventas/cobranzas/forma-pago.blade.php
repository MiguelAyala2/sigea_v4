<div>
    <x-adminlte-card theme="light" title="Cobranzas por Forma de Pago" icon="fas fa-credit-card">
        {{-- Filtros de Período --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="btn-group btn-group-sm" role="group">
                    <button wire:click="setPeriodo('hoy')" class="btn {{ $periodo === 'hoy' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Hoy
                    </button>
                    <button wire:click="setPeriodo('semana_actual')" class="btn {{ $periodo === 'semana_actual' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Semana
                    </button>
                    <button wire:click="setPeriodo('mes_actual')" class="btn {{ $periodo === 'mes_actual' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Mes
                    </button>
                    <button wire:click="setPeriodo('trimestre_actual')" class="btn {{ $periodo === 'trimestre_actual' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Trimestre
                    </button>
                    <button wire:click="setPeriodo('anio_actual')" class="btn {{ $periodo === 'anio_actual' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Año
                    </button>
                </div>

                <div class="float-right">
                    <div class="form-inline">
                        <label class="mr-2">Desde:</label>
                        <input type="date" wire:model="fecha_desde" wire:change="actualizarPeriodo" class="form-control form-control-sm mr-3">
                        <label class="mr-2">Hasta:</label>
                        <input type="date" wire:model="fecha_hasta" wire:change="actualizarPeriodo" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>

        {{-- Estadísticas Resumen --}}
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
                        <h3>{{ $stats['cantidad_cobranzas'] }}</h3>
                        <p>Cobranzas Registradas</p>
                    </div>
                    <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>₲ {{ number_format($stats['promedio_cobranza'], 0, ',', '.') }}</h3>
                        <p>Promedio por Cobranza</p>
                    </div>
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $stats['forma_mas_usada'] }}</h3>
                        <p>Forma Más Usada</p>
                    </div>
                    <div class="icon"><i class="fas fa-star"></i></div>
                </div>
            </div>
        </div>

        {{-- Tabla de Cobranzas por Forma de Pago --}}
        @if($cobranzasPorFormaPago->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th><i class="fas fa-credit-card mr-2"></i>Forma de Pago</th>
                            <th class="text-center"><i class="fas fa-list-ol mr-2"></i>Cantidad</th>
                            <th class="text-right"><i class="fas fa-dollar-sign mr-2"></i>Total Cobrado</th>
                            <th class="text-center"><i class="fas fa-percentage mr-2"></i>Porcentaje</th>
                            <th class="text-center">Gráfico</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cobranzasPorFormaPago as $item)
                            <tr>
                                <td>
                                    @php
                                        $iconClass = match($item->forma_pago) {
                                            'EFECTIVO' => 'fas fa-money-bill-wave text-success',
                                            'TARJETA_CREDITO', 'TARJETA_DEBITO' => 'fas fa-credit-card text-primary',
                                            'CHEQUE' => 'fas fa-money-check text-info',
                                            'TRANSFERENCIA' => 'fas fa-exchange-alt text-warning',
                                            'QR' => 'fas fa-qrcode text-secondary',
                                            default => 'fas fa-coins text-muted'
                                        };
                                    @endphp
                                    <i class="{{ $iconClass }} mr-2"></i>
                                    <strong>{{ $item->forma_pago_texto }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary badge-lg">{{ $item->cantidad }}</span>
                                </td>
                                <td class="text-right">
                                    <strong class="text-success">₲ {{ number_format($item->total, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ number_format($item->porcentaje, 1) }}%</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                             style="width: {{ $item->porcentaje }}%"
                                             aria-valuenow="{{ $item->porcentaje }}"
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($item->porcentaje, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="table-success font-weight-bold">
                            <td><strong><i class="fas fa-calculator mr-2"></i>TOTAL</strong></td>
                            <td class="text-center"><strong>{{ $cantidadTotal }}</strong></td>
                            <td class="text-right"><strong>₲ {{ number_format($totalGeneral, 0, ',', '.') }}</strong></td>
                            <td class="text-center"><strong>100%</strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No hay cobranzas registradas</h5>
                <p class="text-muted">No se encontraron cobranzas en el período seleccionado.</p>
            </div>
        @endif
    </x-adminlte-card>
</div>
