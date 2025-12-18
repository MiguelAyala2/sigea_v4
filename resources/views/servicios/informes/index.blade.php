@extends('adminlte::page')

@section('title', 'Informes Web - Servicios')

@section('content_header')
    <h1><i class="fas fa-chart-line"></i> Informes y Estadísticas de Servicios</h1>
@stop

@section('content')
    <!-- Tarjetas de Resumen -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>45</h3>
                    <p>Servicios Este Mes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tools"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>32</h3>
                    <p>Servicios Completados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>13</h3>
                    <p>En Proceso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cog fa-spin"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>5</h3>
                    <p>Reclamos Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Servicios por Estado</h3>
                </div>
                <div class="card-body">
                    <canvas id="chartEstados" style="height: 250px;"></canvas>
                    <p class="text-center text-muted mt-3">
                        <i class="fas fa-info-circle"></i> Gráfico de ejemplo - requiere Chart.js
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ingresos por Mes</h3>
                </div>
                <div class="card-body">
                    <canvas id="chartIngresos" style="height: 250px;"></canvas>
                    <p class="text-center text-muted mt-3">
                        <i class="fas fa-info-circle"></i> Gráfico de ejemplo - requiere Chart.js
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reportes Disponibles -->
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Reportes Disponibles</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                            <h5>Servicios del Mes</h5>
                            <p class="text-muted">Listado completo de servicios</p>
                            <button class="btn btn-sm btn-danger"><i class="fas fa-download"></i> Descargar PDF</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                            <h5>Ingresos por Técnico</h5>
                            <p class="text-muted">Productividad por técnico</p>
                            <button class="btn btn-sm btn-success"><i class="fas fa-download"></i> Descargar Excel</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-pie fa-3x text-info mb-3"></i>
                            <h5>Satisfacción del Cliente</h5>
                            <p class="text-muted">Encuestas y calificaciones</p>
                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Ver Reporte</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Fecha -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Generar Reporte Personalizado</h3>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha Desde</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha Hasta</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipo de Reporte</label>
                            <select class="form-control">
                                <option>Servicios Realizados</option>
                                <option>Ingresos</option>
                                <option>Reclamos</option>
                                <option>Clientes Frecuentes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-play"></i> Generar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong> - Los gráficos requieren implementación con Chart.js u otra librería.
    </div>
@stop
