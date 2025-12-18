<div>
    {{-- Header del Dashboard --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="text-muted mb-0">
                        <i class="fas fa-shopping-cart mr-2"></i> Dashboard Compras
                    </h3>
                    <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
                {{-- <div>
                    <a href="{{ route('compras.dashboard.exportar-pdf') }}" class="btn btn-sm btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                    <a href="{{ route('compras.dashboard.exportar-excel') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                </div> --}}
            </div>
        </div>
    </div>

    {{-- SECCIÓN 1: PEDIDOS DE COMPRA --}}
    <div class="row mb-3">
        <div class="col-12">
            <x-adminlte-card theme="primary" title="Pedidos de Compra" icon="fas fa-clipboard-list" collapsible>
                <x-slot name="toolsSlot">
                    <a href="{{ route('compras.pedidos.exportar-pdf') }}" class="btn btn-sm btn-Light" target="_blank">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                    <a href="{{ route('compras.pedidos.exportar-excel') }}" class="btn btn-sm btn-Light">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                </x-slot>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.pedidos.index', ['estado' => 'PENDIENTE']) }}" class="text-decoration-none">
                            <div class="small-box bg-warning">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $pedidos['pendientes'] }}</h4>
                                    <p class="mb-0">Pendientes</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($pedidos['pendientes_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.pedidos.index', ['estado' => 'APROBADO']) }}" class="text-decoration-none">
                            <div class="small-box bg-success">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $pedidos['aprobados'] }}</h4>
                                    <p class="mb-0">Aprobados</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($pedidos['aprobados_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.pedidos.index', ['estado' => 'RECHAZADO']) }}" class="text-decoration-none">
                            <div class="small-box bg-danger">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $pedidos['rechazados'] }}</h4>
                                    <p class="mb-0">Rechazados</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($pedidos['rechazados_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    {{-- SECCIÓN 2: PRESUPUESTOS --}}
    <div class="row mb-3">
        <div class="col-12">
            <x-adminlte-card theme="info" title="Presupuestos" icon="fas fa-file-invoice-dollar" collapsible>
                <x-slot name="toolsSlot">
                    <a href="{{ route('compras.presupuestos.exportar-pdf') }}" class="btn btn-sm btn-Light mr-1" target="_blank">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                    <a href="{{ route('compras.presupuestos.exportar-excel') }}" class="btn btn-sm btn-Light">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                </x-slot>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.presupuestos.index', ['estado' => 'PENDIENTE']) }}" class="text-decoration-none">
                            <div class="small-box bg-warning">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $presupuestos['pendientes'] }}</h4>
                                    <p class="mb-0">Pendientes</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($presupuestos['pendientes_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.presupuestos.index', ['estado' => 'APROBADO']) }}" class="text-decoration-none">
                            <div class="small-box bg-success">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $presupuestos['aprobados'] }}</h4>
                                    <p class="mb-0">Aprobados</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($presupuestos['aprobados_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.presupuestos.index', ['estado' => 'RECHAZADO']) }}" class="text-decoration-none">
                            <div class="small-box bg-danger">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $presupuestos['rechazados'] }}</h4>
                                    <p class="mb-0">Rechazados</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($presupuestos['rechazados_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    {{-- SECCIÓN 3: ÓRDENES DE COMPRA --}}
    <div class="row mb-3">
        <div class="col-12">
            <x-adminlte-card theme="purple" title="Órdenes de Compra" icon="fas fa-file-signature" collapsible>
                <x-slot name="toolsSlot">
                    <a href="{{ route('compras.ordenes.exportar-pdf') }}" class="btn btn-sm btn-Light mr-1" target="_blank">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                    <a href="{{ route('compras.ordenes.exportar-excel') }}" class="btn btn-sm btn-Light">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                </x-slot>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.ordenes.index', ['estado' => 'PENDIENTE']) }}" class="text-decoration-none">
                            <div class="small-box bg-warning">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $ordenes['pendientes'] }}</h4>
                                    <p class="mb-0">Pendientes</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($ordenes['pendientes_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.ordenes.index', ['estado' => 'APROBADO']) }}" class="text-decoration-none">
                            <div class="small-box bg-success">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $ordenes['aprobados'] }}</h4>
                                    <p class="mb-0">Aprobados</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($ordenes['aprobados_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.ordenes.index', ['estado' => 'RECHAZADO']) }}" class="text-decoration-none">
                            <div class="small-box bg-danger">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $ordenes['rechazados'] }}</h4>
                                    <p class="mb-0">Rechazados</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($ordenes['rechazados_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    {{-- SECCIÓN 4: COMPRAS/FACTURAS --}}
    <div class="row mb-3">
        <div class="col-12">
            <x-adminlte-card theme="teal" title="Compras/Facturas" icon="fas fa-file-invoice" collapsible>
                <x-slot name="toolsSlot">
                    <a href="{{ route('compras.compras.exportar-pdf') }}" class="btn btn-sm btn-Light mr-1" target="_blank">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                    <a href="{{ route('compras.compras.exportar-excel') }}" class="btn btn-sm btn-Light">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                </x-slot>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.compras.index', ['estado' => 'PENDIENTE']) }}" class="text-decoration-none">
                            <div class="small-box bg-warning">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $compras['pendientes'] }}</h4>
                                    <p class="mb-0">Pendientes</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($compras['pendientes_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.compras.index', ['estado' => 'APROBADO']) }}" class="text-decoration-none">
                            <div class="small-box bg-success">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $compras['aprobados'] }}</h4>
                                    <p class="mb-0">Aprobados</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($compras['aprobados_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('compras.compras.index', ['estado' => 'RECHAZADO']) }}" class="text-decoration-none">
                            <div class="small-box bg-danger">
                                <div class="inner p-3">
                                    <h4 class="mb-1">{{ $compras['rechazados'] }}</h4>
                                    <p class="mb-0">Rechazados</p>
                                    <p class="mb-0 font-weight-bold text-white" style="font-size: 1rem;">TOTAL: Gs. {{ number_format($compras['rechazados_total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </div>
</div>

@push('css')
<style>
.small-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    transition: all 0.3s;
}
.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,.3);
}
.small-box .inner {
    min-height: 90px;
}
</style>
@endpush
