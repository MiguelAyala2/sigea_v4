<div>
    {{-- Mensaje de Bienvenida --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <h4><i class="fas fa-info-circle mr-2"></i>Bienvenido, {{ auth()->user()->name }}</h4>
                <p class="mb-0">Dashboard del Sistema Integrado de Gestión Empresarial - {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Estadísticas Principales --}}
    <div class="row">
        {{-- Ventas Hoy --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>₲ {{ number_format($ventasHoy, 0, ',', '.') }}</h3>
                    <p>Ventas Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <a href="{{ route('ventas.facturas.index') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Ventas del Mes --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>₲ {{ number_format($ventasMes, 0, ',', '.') }}</h3>
                    <p>Ventas del Mes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="{{ route('ventas.facturas.index') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Cuentas por Cobrar --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>₲ {{ number_format($cuentasPorCobrar, 0, ',', '.') }}</h3>
                    <p>Cuentas por Cobrar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ route('ventas.cuentas-cobrar.index') }}" class="small-box-footer">
                    {{ $facturasPendientes }} facturas <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Cuentas por Pagar --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>₲ {{ number_format($cuentasPorPagar, 0, ',', '.') }}</h3>
                    <p>Cuentas por Pagar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="{{ route('compras.cuentas-pagar.index') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Segunda Fila de Estadísticas --}}
    <div class="row">
        {{-- Servicios Pendientes --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $serviciosPendientes }}</h3>
                    <p>Servicios Pendientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('servicios.ordenes.index') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Servicios en Proceso --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $serviciosEnProceso }}</h3>
                    <p>Servicios en Proceso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cog fa-spin"></i>
                </div>
                <a href="{{ route('servicios.ordenes.index') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Servicios Finalizados --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $serviciosFinalizados }}</h3>
                    <p>Listos para Entrega</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('servicios.entrega.index') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Productos Stock Bajo --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $productosStockBajo }}</h3>
                    <p>Productos Stock Bajo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('stock.productos.index') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Últimas Ventas --}}
        <div class="col-lg-6">
            <x-adminlte-card title="Últimas Ventas" theme="primary" icon="fas fa-shopping-cart">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>N° Factura</th>
                                <th>Cliente</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasVentas as $factura)
                                <tr>
                                    <td><small>{{ $factura->fecha_emision->format('d/m/Y') }}</small></td>
                                    <td><span class="badge badge-info">{{ $factura->numero_factura }}</span></td>
                                    <td><small>{{ $factura->cliente->nombre ?? 'N/A' }}</small></td>
                                    <td class="text-right"><strong>₲ {{ number_format($factura->total, 0, ',', '.') }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay ventas registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-slot name="footerSlot">
                    <a href="{{ route('ventas.facturas.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye mr-1"></i> Ver todas las ventas
                    </a>
                </x-slot>
            </x-adminlte-card>
        </div>

        {{-- Productos Más Vendidos --}}
        <div class="col-lg-6">
            <x-adminlte-card title="Productos Más Vendidos (últimos 30 días)" theme="success" icon="fas fa-star">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productosMasVendidos as $producto)
                                <tr>
                                    <td><small>{{ $producto->nombre }}</small></td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $producto->total_vendido }}</span>
                                    </td>
                                    <td class="text-right">
                                        <strong>₲ {{ number_format($producto->total_monto, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-slot name="footerSlot">
                    <a href="{{ route('stock.productos.index') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-boxes mr-1"></i> Ver todos los productos
                    </a>
                </x-slot>
            </x-adminlte-card>
        </div>
    </div>

    {{-- Gráfico de Ventas y Alertas --}}
    <div class="row">
        {{-- Resumen Financiero --}}
        <div class="col-lg-6">
            <x-adminlte-card title="Resumen Financiero del Mes" theme="info" icon="fas fa-chart-pie">
                <div class="row text-center">
                    <div class="col-6 border-right">
                        <div class="description-block">
                            <h5 class="description-header text-success">₲ {{ number_format($ventasMes, 0, ',', '.') }}</h5>
                            <span class="description-text">INGRESOS</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header text-danger">₲ {{ number_format($comprasMes, 0, ',', '.') }}</h5>
                            <span class="description-text">EGRESOS</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-12">
                        <div class="description-block">
                            @php
                                $balance = $ventasMes - $comprasMes;
                                $balanceClass = $balance >= 0 ? 'text-success' : 'text-danger';
                            @endphp
                            <h5 class="description-header {{ $balanceClass }}">
                                ₲ {{ number_format(abs($balance), 0, ',', '.') }}
                            </h5>
                            <span class="description-text">
                                {{ $balance >= 0 ? 'UTILIDAD' : 'DÉFICIT' }} DEL MES
                            </span>
                        </div>
                    </div>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Alertas y Notificaciones --}}
        <div class="col-lg-6">
            <x-adminlte-card title="Alertas y Notificaciones" theme="warning" icon="fas fa-bell">
                <ul class="list-group list-group-flush">
                    @if($productosStockBajo > 0)
                        <li class="list-group-item">
                            <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                            <strong>{{ $productosStockBajo }}</strong> producto(s) con stock bajo
                            <a href="{{ route('stock.productos.index') }}" class="float-right">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </li>
                    @endif

                    @if($facturasPendientes > 0)
                        <li class="list-group-item">
                            <i class="fas fa-file-invoice text-info mr-2"></i>
                            <strong>{{ $facturasPendientes }}</strong> factura(s) pendiente(s) de cobro
                            <a href="{{ route('ventas.cuentas-cobrar.index') }}" class="float-right">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </li>
                    @endif

                    @if($solicitudesPendientes > 0)
                        <li class="list-group-item">
                            <i class="fas fa-clipboard-list text-secondary mr-2"></i>
                            <strong>{{ $solicitudesPendientes }}</strong> solicitud(es) de servicio pendiente(s)
                            <a href="{{ route('servicios.solicitudes.index') }}" class="float-right">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </li>
                    @endif

                    @if($serviciosFinalizados > 0)
                        <li class="list-group-item">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            <strong>{{ $serviciosFinalizados }}</strong> servicio(s) listo(s) para entrega
                            <a href="{{ route('servicios.entrega.index') }}" class="float-right">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </li>
                    @endif

                    @if($productosStockBajo == 0 && $facturasPendientes == 0 && $solicitudesPendientes == 0 && $serviciosFinalizados == 0)
                        <li class="list-group-item text-center text-muted">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            No hay alertas pendientes
                        </li>
                    @endif
                </ul>
            </x-adminlte-card>
        </div>
    </div>

    {{-- Accesos Rápidos --}}
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Accesos Rápidos" theme="dark" icon="fas fa-bolt">
                <div class="row text-center">
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="{{ route('ventas.facturas.crear') }}" class="btn btn-app bg-success">
                            <i class="fas fa-plus"></i> Nueva Venta
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="{{ route('compras.compras.create') }}" class="btn btn-app bg-primary">
                            <i class="fas fa-shopping-cart"></i> Nueva Compra
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="{{ route('servicios.solicitudes.create') }}" class="btn btn-app bg-info">
                            <i class="fas fa-clipboard-list"></i> Nueva Solicitud
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="{{ route('stock.productos.index') }}" class="btn btn-app bg-warning">
                            <i class="fas fa-boxes"></i> Inventario
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="{{ route('ventas.cobranzas.registrar') }}" class="btn btn-app bg-success">
                            <i class="fas fa-hand-holding-usd"></i> Registrar Cobro
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="{{ route('servicios.clientes.historial') }}" class="btn btn-app bg-secondary">
                            <i class="fas fa-history"></i> Historial
                        </a>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </div>
</div>
