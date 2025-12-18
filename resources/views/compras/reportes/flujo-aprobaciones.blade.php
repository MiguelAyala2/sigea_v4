@extends('layouts.app')

@section('title', 'Flujo de Aprobaciones')
@section('content_header_title', 'Flujo de Aprobaciones')
@section('content_header_subtitle', 'Análisis de tiempos y eficiencia en aprobaciones')

@section('content_body')
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-project-diagram mr-2"></i>Reporte de Flujo de Aprobaciones</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-chart-line mr-2"></i>
                Analice los tiempos promedio de aprobación, cuellos de botella y eficiencia 
                del flujo de aprobaciones en su organización.
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tipo_documento">Tipo de Documento</label>
                        <select class="form-control" id="tipo_documento" disabled>
                            <option>Todos los documentos</option>
                            <option>Compras/Facturas</option>
                            <option>Órdenes de Compra</option>
                            <option>Pedidos de Compra</option>
                            <option>Recepciones</option>
                            <option>Pagos</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="periodo_reporte">Período</label>
                        <select class="form-control" id="periodo_reporte" disabled>
                            <option>Últimos 7 días</option>
                            <option>Último mes</option>
                            <option>Último trimestre</option>
                            <option>Último semestre</option>
                            <option>Último año</option>
                            <option>Personalizado</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="metrica_principal">Métrica Principal</label>
                        <select class="form-control" id="metrica_principal" disabled>
                            <option>Tiempo promedio de aprobación</option>
                            <option>Documentos aprobados vs rechazados</option>
                            <option>Cuellos de botella por nivel</option>
                            <option>Eficiencia por aprobador</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Tiempos Promedio por Nivel</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                                <h5 class="text-primary">En Desarrollo</h5>
                                <p class="text-muted">
                                    Gráfico de barras mostrando tiempos promedio de aprobación 
                                    por cada nivel del flujo.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-tachometer-alt mr-2"></i>Eficiencia por Aprobador</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-4">
                                <i class="fas fa-user-chart fa-3x text-success mb-3"></i>
                                <h5 class="text-success">En Desarrollo</h5>
                                <p class="text-muted">
                                    Tabla comparativa de eficiencia y tiempos de respuesta 
                                    por cada usuario aprobador.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>Cuellos de Botella</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle mr-2"></i>
                                Los cuellos de botella se identifican cuando un nivel de aprobación 
                                excede significativamente el tiempo promedio esperado.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nivel</th>
                                            <th>Documentos Pendientes</th>
                                            <th>Tiempo Promedio</th>
                                            <th>Tiempo Máximo</th>
                                            <th>Estado</th>
                                            <th>Recomendación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-danger">
                                            <td><strong>Nivel 3 - Gerencia</strong></td>
                                            <td>8 documentos</td>
                                            <td>5.2 días</td>
                                            <td>12 días</td>
                                            <td><span class="badge badge-danger">Crítico</span></td>
                                            <td>Revisar carga de trabajo</td>
                                        </tr>
                                        <tr class="table-warning">
                                            <td>Nivel 2 - Supervisión</td>
                                            <td>3 documentos</td>
                                            <td>2.1 días</td>
                                            <td>4 días</td>
                                            <td><span class="badge badge-warning">Atención</span></td>
                                            <td>Monitorear próximos días</td>
                                        </tr>
                                        <tr class="table-success">
                                            <td>Nivel 1 - Operativo</td>
                                            <td>1 documento</td>
                                            <td>0.5 días</td>
                                            <td>1 día</td>
                                            <td><span class="badge badge-success">Normal</span></td>
                                            <td>Sin acción requerida</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <div class="btn-group">
                    <button class="btn btn-outline-primary" disabled>
                        <i class="fas fa-download mr-2"></i> Descargar CSV
                    </button>
                    <button class="btn btn-outline-success ml-2" disabled>
                        <i class="fas fa-print mr-2"></i> Imprimir Reporte
                    </button>
                    <button class="btn btn-primary ml-2" disabled>
                        <i class="fas fa-cogs mr-2"></i> Generar Dashboard
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection