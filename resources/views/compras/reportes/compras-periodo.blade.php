@extends('layouts.app')

@section('title', 'Compras por Período')
@section('content_header_title', 'Compras por Período')
@section('content_header_subtitle', 'Análisis de compras en un rango de fechas específico')

@section('content_body')
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Reporte de Compras por Período</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-filter mr-2"></i>
                Seleccione un rango de fechas para analizar las compras realizadas en ese período.
                Puede filtrar por proveedor, sucursal o tipo de documento.
            </div>

            <form action="{{ route('compras.reportes.compras-periodo') }}" method="GET" id="reporteForm">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_desde">Fecha Desde *</label>
                            <input type="date" class="form-control" name="fecha_desde"
                                   id="fecha_desde"
                                   value="{{ request('fecha_desde', date('Y-m-01')) }}"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_hasta">Fecha Hasta *</label>
                            <input type="date" class="form-control" name="fecha_hasta"
                                   id="fecha_hasta"
                                   value="{{ request('fecha_hasta', date('Y-m-t')) }}"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="proveedor_id">Proveedor</label>
                            <select class="form-control select2" name="proveedor_id" id="proveedor_id">
                                <option value="">Todos los proveedores</option>
                                {{-- Los proveedores se cargarán dinámicamente --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sucursal_id">Sucursal</label>
                            <select class="form-control select2" name="sucursal_id" id="sucursal_id">
                                <option value="">Todas las sucursales</option>
                                {{-- Las sucursales se cargarán dinámicamente --}}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tipo_documento">Tipo de Documento</label>
                            <select class="form-control" name="tipo_documento" id="tipo_documento">
                                <option value="">Todos</option>
                                <option value="FACTURA">Factura</option>
                                <option value="NOTA_CREDITO">Nota de Crédito</option>
                                <option value="NOTA_DEBITO">Nota de Débito</option>
                                <option value="AUTOFACTURA">Autofactura</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select class="form-control" name="estado" id="estado">
                                <option value="">Todos</option>
                                <option value="APROBADA" selected>Aprobada</option>
                                <option value="PENDIENTE">Pendiente</option>
                                <option value="RECHAZADA">Rechazada</option>
                                <option value="ANULADA">Anulada</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="orden">Ordenar por</label>
                            <select class="form-control" name="orden" id="orden">
                                <option value="fecha_asc">Fecha (ascendente)</option>
                                <option value="fecha_desc">Fecha (descendente)</option>
                                <option value="monto_asc">Monto (menor a mayor)</option>
                                <option value="monto_desc">Monto (mayor a menor)</option>
                                <option value="proveedor">Proveedor (A-Z)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search mr-2"></i> Generar Reporte
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            @if(isset($compras))
                <hr class="my-4">

                {{-- RESUMEN EJECUTIVO --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary">
                                <i class="fas fa-file-invoice"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Compras</span>
                                <span class="info-box-number">{{ $compras->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Monto Total</span>
                                <span class="info-box-number">
                                    Gs. {{ number_format($compras->sum('total'), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-calculator"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Promedio</span>
                                <span class="info-box-number">
                                    Gs. {{ number_format($compras->count() > 0 ? $compras->sum('total') / $compras->count() : 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-truck"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Proveedores</span>
                                <span class="info-box-number">
                                    {{ $compras->pluck('proveedor_id')->unique()->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABLA DE RESULTADOS --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list mr-2"></i>
                            Detalle de Compras
                            <span class="badge badge-primary ml-2">{{ $compras->count() }} registros</span>
                        </h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-success" onclick="exportarExcel()">
                                <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="exportarPDF()">
                                <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>N° Factura</th>
                                    <th>Proveedor</th>
                                    <th>Sucursal</th>
                                    <th>Tipo Doc.</th>
                                    <th class="text-right">Subtotal</th>
                                    <th class="text-right">IVA</th>
                                    <th class="text-right">Total</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($compras as $compra)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($compra->fecha_emision)->format('d/m/Y') }}</td>
                                        <td>
                                            <strong>{{ $compra->numero_factura }}</strong>
                                            <br>
                                            <small class="text-muted">Timb: {{ $compra->timbrado }}</small>
                                        </td>
                                        <td>
                                            {{ $compra->proveedor->razon_social ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">{{ $compra->proveedor->ruc ?? '' }}</small>
                                        </td>
                                        <td>{{ $compra->sucursal->nombre ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ str_replace('_', ' ', $compra->tipo_documento) }}
                                            </span>
                                        </td>
                                        <td class="text-right">Gs. {{ number_format($compra->subtotal, 0, ',', '.') }}</td>
                                        <td class="text-right">Gs. {{ number_format($compra->total_iva, 0, ',', '.') }}</td>
                                        <td class="text-right">
                                            <strong>Gs. {{ number_format($compra->total, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @switch($compra->estado)
                                                @case('APROBADA')
                                                    <span class="badge badge-success">Aprobada</span>
                                                    @break
                                                @case('PENDIENTE')
                                                    <span class="badge badge-warning">Pendiente</span>
                                                    @break
                                                @case('RECHAZADA')
                                                    <span class="badge badge-danger">Rechazada</span>
                                                    @break
                                                @case('ANULADA')
                                                    <span class="badge badge-dark">Anulada</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $compra->estado }}</span>
                                            @endswitch
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('compras.compras.show', $compra->id) }}"
                                               class="btn btn-sm btn-info"
                                               title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                                            <p class="text-muted">No se encontraron compras para el período seleccionado</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($compras->count() > 0)
                                <tfoot>
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="5" class="text-right">TOTALES:</td>
                                        <td class="text-right">Gs. {{ number_format($compras->sum('subtotal'), 0, ',', '.') }}</td>
                                        <td class="text-right">Gs. {{ number_format($compras->sum('total_iva'), 0, ',', '.') }}</td>
                                        <td class="text-right">Gs. {{ number_format($compras->sum('total'), 0, ',', '.') }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- GRÁFICOS Y ANÁLISIS --}}
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-chart-pie mr-2"></i>Compras por Proveedor</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoPorProveedor" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-chart-line mr-2"></i>Evolución de Compras</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoEvolucion" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-contract fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Seleccione un rango de fechas para generar el reporte</h5>
                    <p class="text-muted">
                        Complete los filtros arriba y haga clic en "Generar Reporte" para ver los resultados.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Inicializar Select2 para los selectores
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Seleccione...',
            allowClear: true
        });

        @if(isset($compras) && $compras->count() > 0)
            // Gráfico por Proveedor
            var proveedoresData = @json($compras->groupBy('proveedor.razon_social')->map(function($items, $key) {
                return [
                    'proveedor' => $key ?? 'Sin proveedor',
                    'total' => $items->sum('total')
                ];
            })->values());

            new Chart(document.getElementById('graficoPorProveedor'), {
                type: 'doughnut',
                data: {
                    labels: proveedoresData.map(item => item.proveedor),
                    datasets: [{
                        data: proveedoresData.map(item => item.total),
                        backgroundColor: [
                            '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                            '#6c757d', '#343a40', '#fd7e14', '#20c997', '#6610f2'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Gráfico de Evolución
            var evolucionData = @json($compras->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->fecha_emision)->format('Y-m-d');
            })->map(function($items, $key) {
                return [
                    'fecha' => $key,
                    'total' => $items->sum('total')
                ];
            })->values());

            new Chart(document.getElementById('graficoEvolucion'), {
                type: 'line',
                data: {
                    labels: evolucionData.map(item => new Date(item.fecha).toLocaleDateString('es-PY')),
                    datasets: [{
                        label: 'Total Compras (Gs.)',
                        data: evolucionData.map(item => item.total),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Gs. ' + value.toLocaleString('es-PY');
                                }
                            }
                        }
                    }
                }
            });
        @endif
    });

    function exportarExcel() {
        alert('Función de exportación a Excel en desarrollo');
    }

    function exportarPDF() {
        alert('Función de exportación a PDF en desarrollo');
    }
</script>
@endpush