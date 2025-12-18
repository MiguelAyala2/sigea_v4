@extends('adminlte::page')

@section('title', 'Diagnóstico Técnico')

@section('content_header')
    <h1><i class="fas fa-stethoscope"></i> Diagnóstico Técnico</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="card-title">Información del Equipo</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><p><strong>N° Recepción:</strong> REC-000025</p></div>
                <div class="col-md-3"><p><strong>Cliente:</strong> María López</p></div>
                <div class="col-md-3"><p><strong>Equipo:</strong> Bomba Grundfos</p></div>
                <div class="col-md-3"><p><strong>Fecha Recepción:</strong> 16/12/2025</p></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-warning">
            <h3 class="card-title">Diagnóstico Técnico</h3>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Técnico Asignado *</label>
                            <select class="form-control">
                                <option>Ing. Juan Técnico</option>
                                <option>Téc. Pedro Reparador</option>
                                <option>Ing. Ana Mecánica</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha Diagnóstico *</label>
                            <input type="date" class="form-control" value="2025-12-16">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Problema Detectado *</label>
                            <textarea class="form-control" rows="4" placeholder="Descripción detallada del problema encontrado"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Solución Propuesta *</label>
                            <textarea class="form-control" rows="4" placeholder="Detalle la solución técnica propuesta"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Estado del Diagnóstico *</label>
                            <select class="form-control">
                                <option>Reparable</option>
                                <option>No Reparable</option>
                                <option>Requiere Repuestos</option>
                                <option>En Evaluación</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tiempo Estimado (horas)</label>
                            <input type="number" class="form-control" placeholder="Ej: 8">
                        </div>
                    </div>
                </div>

                <h5>Repuestos Necesarios</h5>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Repuesto</th>
                            <th>Cantidad</th>
                            <th>Costo Est.</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control form-control-sm" placeholder="Descripción"></td>
                            <td><input type="number" class="form-control form-control-sm" placeholder="Cant."></td>
                            <td><input type="number" class="form-control form-control-sm" placeholder="₲"></td>
                            <td><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Agregar Repuesto</button>

                <hr>
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> Guardar Diagnóstico</button>
                <button type="button" class="btn btn-info btn-lg"><i class="fas fa-file-invoice-dollar"></i> Generar Presupuesto</button>
            </form>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong>
    </div>
@stop
