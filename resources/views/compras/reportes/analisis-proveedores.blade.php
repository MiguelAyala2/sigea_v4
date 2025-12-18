@extends('layouts.app')

@section('title', 'Análisis de Proveedores')
@section('content_header_title', 'Análisis de Proveedores')
@section('content_header_subtitle', 'Estadísticas y evaluación de proveedores')

@section('content_body')
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-chart-line mr-2"></i>Análisis y Estadísticas de Proveedores</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-chart-bar mr-2"></i>
                Este reporte analiza el desempeño de los proveedores basado en compras, 
                tiempos de entrega, calidad y precios.
            </div>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha Desde</label>
                        <input type="date" class="form-control" id="fecha_desde" value="{{ date('Y-m-01') }}" disabled>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_hasta">Fecha Hasta</label>
                        <input type="date" class="form-control" id="fecha_hasta" value="{{ date('Y-m-t') }}" disabled>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="criterio">Criterio de Análisis</label>
                        <select class="form-control" id="criterio" disabled>
                            <option>Volumen de Compras</option>
                            <option>Tiempo de Entrega</option>
                            <option>Calidad/Precio</option>
                            <option>Cumplimiento de Plazos</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="top_n">Top N Proveedores</label>
                        <select class="form-control" id="top_n" disabled>
                            <option>5</option>
                            <option>10</option>
                            <option>15</option>
                            <option>Todos</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="text-center py-4">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Ejemplo de Análisis ABC</h5>
                            </div>
                            <div class="card-body">
                                <div class="progress mb-3" style="height: 30px;">
                                    <div class="progress-bar bg-danger" style="width: 20%">
                                        <strong>A: 20% Proveedores</strong>
                                    </div>
                                    <div class="progress-bar bg-warning" style="width: 30%">
                                        <strong>B: 30% Proveedores</strong>
                                    </div>
                                    <div class="progress-bar bg-success" style="width: 50%">
                                        <strong>C: 50% Proveedores</strong>
                                    </div>
                                </div>
                                
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <h3 class="text-danger">80%</h3>
                                            <p class="mb-0">del Volumen Total</p>
                                            <small class="text-muted">Categoría A</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <h3 class="text-warning">15%</h3>
                                            <p class="mb-0">del Volumen Total</p>
                                            <small class="text-muted">Categoría B</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <h3 class="text-success">5%</h3>
                                            <p class="mb-0">del Volumen Total</p>
                                            <small class="text-muted">Categoría C</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-trophy mr-2"></i>Top 5 Proveedores</h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>1. Grundfos Paraguay S.A.</strong>
                                        <br>
                                        <small class="text-muted">RUC: 80012345-1</small>
                                    </div>
                                    <span class="badge badge-success badge-pill">Gs. 125.8M</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>2. Tigre Paraguay S.A.</strong>
                                        <br>
                                        <small class="text-muted">RUC: 80023456-2</small>
                                    </div>
                                    <span class="badge badge-success badge-pill">Gs. 89.3M</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>3. Amanco Py</strong>
                                        <br>
                                        <small class="text-muted">RUC: 80034567-3</small>
                                    </div>
                                    <span class="badge badge-success badge-pill">Gs. 67.5M</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>4. Rotoplas Paraguay</strong>
                                        <br>
                                        <small class="text-muted">RUC: 80045678-4</small>
                                    </div>
                                    <span class="badge badge-success badge-pill">Gs. 45.2M</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>5. Pedrollo Distribuidor</strong>
                                        <br>
                                        <small class="text-muted">RUC: 80056789-5</small>
                                    </div>
                                    <span class="badge badge-success badge-pill">Gs. 32.8M</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-clock mr-2"></i>Tiempos de Entrega</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-3">
                                <i class="fas fa-chart-pie fa-4x text-info mb-3"></i>
                                <p class="text-muted">
                                    Gráficos de tiempos promedio de entrega por proveedor.
                                    En desarrollo.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <button class="btn btn-primary btn-lg" disabled>
                    <i class="fas fa-cogs mr-2"></i> Generar Reporte Completo
                </button>
                <p class="text-muted small mt-2">
                    <i class="fas fa-hard-hat mr-1"></i>
                    El sistema de análisis avanzado de proveedores está en desarrollo.
                </p>
            </div>
        </div>
    </div>
@endsection