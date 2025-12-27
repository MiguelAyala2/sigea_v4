@extends('adminlte::page')

@section('title', 'Informes y Reportes')

@section('content_header')
    <h1><i class="fas fa-chart-bar"></i> Informes y Reportes</h1>
@stop

@section('content')
    <div class="row">
        <!-- SOLICITUDES DE SERVICIO -->
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Solicitudes de Servicio</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('servicios.informes.solicitudes') }}" method="POST" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Desde</label>
                                    <input type="date" name="fecha_desde" class="form-control" required value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Hasta</label>
                                    <input type="date" name="fecha_hasta" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                </button>
                                <button type="submit" name="formato" value="excel" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- PRESUPUESTOS -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calculator"></i> Presupuestos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('servicios.informes.presupuestos') }}" method="POST" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Desde</label>
                                    <input type="date" name="fecha_desde" class="form-control" required value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Hasta</label>
                                    <input type="date" name="fecha_hasta" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                </button>
                                <button type="submit" name="formato" value="excel" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ÓRDENES DE SERVICIO -->
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tools"></i> Órdenes de Servicio</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('servicios.informes.ordenes') }}" method="POST" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Desde</label>
                                    <input type="date" name="fecha_desde" class="form-control" required value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Hasta</label>
                                    <input type="date" name="fecha_hasta" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                </button>
                                <button type="submit" name="formato" value="excel" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RECLAMOS -->
        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Reclamos de Clientes</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('servicios.informes.reclamos') }}" method="POST" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Desde</label>
                                    <input type="date" name="fecha_desde" class="form-control" required value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Hasta</label>
                                    <input type="date" name="fecha_hasta" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                </button>
                                <button type="submit" name="formato" value="excel" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
