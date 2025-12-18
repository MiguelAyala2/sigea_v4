@extends('adminlte::page')

@section('title', 'Libro de Compras')

@section('content_header')
    <h1>Libro de Compras</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filtros</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('compras.reportes.libro-compras') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="proveedor_id">Proveedor</label>
                            <select name="proveedor_id" id="proveedor_id" class="form-control">
                                <option value="">Todos los proveedores</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Reporte de Compras</h3>
            <div class="card-tools">
                <a href="{{ route('compras.reportes.libro-compras.exportar-pdf', request()->all()) }}" class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
                <a href="{{ route('compras.reportes.libro-compras.exportar-excel', request()->all()) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h5>Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</h5>
                </div>
            </div>

            <table class="table table-bordered table-striped table-sm">
                <thead class="bg-primary">
                    <tr>
                        <th>Tipo</th>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>RUC/DNI</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Impuesto</th>
                        <th class="text-right">Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transacciones as $transaccion)
                        <tr>
                            <td>
                                @if($transaccion['tipo'] === 'Factura')
                                    <span class="badge badge-info">{{ $transaccion['tipo'] }}</span>
                                @elseif($transaccion['tipo'] === 'Nota Crédito')
                                    <span class="badge badge-success">{{ $transaccion['tipo'] }}</span>
                                @else
                                    <span class="badge badge-warning">{{ $transaccion['tipo'] }}</span>
                                @endif
                            </td>
                            <td>{{ $transaccion['numero'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaccion['fecha'])->format('d/m/Y') }}</td>
                            <td>{{ $transaccion['proveedor'] }}</td>
                            <td>{{ $transaccion['ruc'] }}</td>
                            <td class="text-right">Gs. {{ number_format($transaccion['subtotal'], 0, ',', '.') }}</td>
                            <td class="text-right">Gs. {{ number_format($transaccion['impuesto'], 0, ',', '.') }}</td>
                            <td class="text-right">Gs. {{ number_format($transaccion['total'], 0, ',', '.') }}</td>
                            <td>
                                @if($transaccion['estado'] === 'finalizada' || $transaccion['estado'] === 'aplicada')
                                    <span class="badge badge-success">{{ ucfirst($transaccion['estado']) }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($transaccion['estado']) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No hay transacciones en el período seleccionado</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($transacciones->count() > 0)
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="5" class="text-right">TOTALES:</th>
                            <th class="text-right">Gs. {{ number_format($totales['subtotal'], 0, ',', '.') }}</th>
                            <th class="text-right">Gs. {{ number_format($totales['impuesto'], 0, ',', '.') }}</th>
                            <th class="text-right">Gs. {{ number_format($totales['total'], 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Resumen Tributario</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Compras</span>
                            <span class="info-box-number">{{ $transacciones->where('tipo', 'Factura')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-file-invoice"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Notas de Crédito</span>
                            <span class="info-box-number">{{ $transacciones->where('tipo', 'Nota Crédito')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-file-invoice-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Notas de Débito</span>
                            <span class="info-box-number">{{ $transacciones->where('tipo', 'Nota Débito')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Base Imponible</span>
                            <span class="info-box-number">Gs. {{ number_format($totales['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total IVA</span>
                            <span class="info-box-number">Gs. {{ number_format($totales['impuesto'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total General</span>
                            <span class="info-box-number">Gs. {{ number_format($totales['total'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    @media print {
        .card-tools, .btn, .sidebar, .main-header, .main-footer {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@stop