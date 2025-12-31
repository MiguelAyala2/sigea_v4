<div>
    {{-- Filtros de período --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Período de Consulta</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <button wire:click="setPeriodo('hoy'); cargarDatos()" class="btn btn-outline-primary btn-block {{ $periodo == 'hoy' ? 'active' : '' }}">
                        Hoy
                    </button>
                </div>
                <div class="col-md-2">
                    <button wire:click="setPeriodo('semana_actual'); cargarDatos()" class="btn btn-outline-primary btn-block {{ $periodo == 'semana_actual' ? 'active' : '' }}">
                        Esta Semana
                    </button>
                </div>
                <div class="col-md-2">
                    <button wire:click="setPeriodo('mes_actual'); cargarDatos()" class="btn btn-outline-primary btn-block {{ $periodo == 'mes_actual' ? 'active' : '' }}">
                        Este Mes
                    </button>
                </div>
                <div class="col-md-2">
                    <button wire:click="setPeriodo('trimestre_actual'); cargarDatos()" class="btn btn-outline-primary btn-block {{ $periodo == 'trimestre_actual' ? 'active' : '' }}">
                        Este Trimestre
                    </button>
                </div>
                <div class="col-md-2">
                    <button wire:click="setPeriodo('anio_actual'); cargarDatos()" class="btn btn-outline-primary btn-block {{ $periodo == 'anio_actual' ? 'active' : '' }}">
                        Este Año
                    </button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <label>Fecha Desde</label>
                    <input type="date" wire:model="fecha_desde" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Fecha Hasta</label>
                    <input type="date" wire:model="fecha_hasta" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button wire:click="actualizarPeriodo" class="btn btn-primary btn-block">
                        <i class="fas fa-sync"></i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen en tarjetas --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>₲ {{ number_format($ventas_mes, 0, ',', '.') }}</h3>
                    <p>Ventas del Período</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>₲ {{ number_format($total_cobrado, 0, ',', '.') }}</h3>
                    <p>Total Cobrado</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>₲ {{ number_format($pendiente_cobro, 0, ',', '.') }}</h3>
                    <p>Pendiente Cobro</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $facturas_emitidas }}</h3>
                    <p>Facturas Emitidas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Ventas por Mes (Últimos 12 meses)</h3>
                </div>
                <div class="card-body">
                    @if(count($ventas_por_mes) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventas_por_mes as $item)
                                        <tr>
                                            <td>{{ $item['mes'] }}</td>
                                            <td class="text-right">₲ {{ number_format($item['total'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-bar fa-3x"></i>
                            <p class="mt-2">No hay datos de ventas mensuales</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Cobranzas por Forma de Pago</h3>
                </div>
                <div class="card-body">
                    @if(count($cobranzas_por_forma_pago) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Forma de Pago</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cobranzas_por_forma_pago as $item)
                                        <tr>
                                            <td>{{ $item['forma_pago'] }}</td>
                                            <td class="text-right">₲ {{ number_format($item['total'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-pie fa-3x"></i>
                            <p class="mt-2">No hay datos de cobranzas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Productos más vendidos --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Productos Más Vendidos (Top 10)</h3>
                </div>
                <div class="card-body">
                    @if(count($productos_mas_vendidos) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Producto</th>
                                        <th class="text-right">Cantidad</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productos_mas_vendidos as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->nombre }}</td>
                                            <td class="text-right">{{ number_format($item->cantidad_total, 0, ',', '.') }}</td>
                                            <td class="text-right">₲ {{ number_format($item->monto_total, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-boxes fa-3x"></i>
                            <p class="mt-2">No hay datos de productos vendidos</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Ventas por Cliente (Top 10)</h3>
                </div>
                <div class="card-body">
                    @if(count($ventas_por_cliente) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th class="text-right">Facturas</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventas_por_cliente as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item['cliente'] }}</td>
                                            <td class="text-right">{{ $item['cantidad_facturas'] }}</td>
                                            <td class="text-right">₲ {{ number_format($item['total_ventas'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x"></i>
                            <p class="mt-2">No hay datos de ventas por cliente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Reportes Disponibles --}}
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h3 class="card-title">Reportes Disponibles para Exportar</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box bg-gradient-info">
                        <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Reporte de Ventas</span>
                            <span class="info-box-number">Por período</span>
                            <button wire:click="exportarPDF" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button wire:click="exportarExcel" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-gradient-success">
                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ventas por Cliente</span>
                            <span class="info-box-number">Ranking</span>
                            <button wire:click="exportarPDF" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button wire:click="exportarExcel" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-gradient-warning">
                        <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Productos Más Vendidos</span>
                            <span class="info-box-number">Top 10</span>
                            <button wire:click="exportarPDF" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button wire:click="exportarExcel" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box bg-gradient-danger">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Cuentas por Cobrar</span>
                            <span class="info-box-number">Vencidas/Vigentes</span>
                            <button wire:click="exportarPDF" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button wire:click="exportarExcel" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-gradient-primary">
                        <span class="info-box-icon"><i class="fas fa-money-check-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Cobranzas Realizadas</span>
                            <span class="info-box-number">Por período</span>
                            <button wire:click="exportarPDF" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button wire:click="exportarExcel" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-gradient-secondary">
                        <span class="info-box-icon"><i class="fas fa-cash-register"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Movimientos de Caja</span>
                            <span class="info-box-number">Histórico</span>
                            <button wire:click="exportarPDF" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button wire:click="exportarExcel" class="btn btn-sm btn-light mt-2">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
</div>
