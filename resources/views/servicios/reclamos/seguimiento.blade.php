@extends('adminlte::page')

@section('title', 'Seguimiento de Reclamos')

@section('content_header')
    <h1><i class="fas fa-search"></i> Seguimiento de Reclamos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-control">
                        <option>Todos los estados</option>
                        <option>Pendiente</option>
                        <option>En Proceso</option>
                        <option>Resuelto</option>
                        <option>Cerrado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control">
                        <option>Todas las prioridades</option>
                        <option>Urgente</option>
                        <option>Alta</option>
                        <option>Media</option>
                        <option>Baja</option>
                    </select>
                </div>
            </div>

            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>N° Reclamo</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Prioridad</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>REC-000015</td>
                        <td>16/12/2025</td>
                        <td>María López</td>
                        <td>Falla Post-Servicio</td>
                        <td><span class="badge badge-danger">Alta</span></td>
                        <td>Supervisor Técnico</td>
                        <td><span class="badge badge-warning">En Proceso</span></td>
                        <td>
                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-success"><i class="fas fa-comment"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>REC-000014</td>
                        <td>10/12/2025</td>
                        <td>Carlos Rodríguez</td>
                        <td>Demora en Entrega</td>
                        <td><span class="badge badge-warning">Media</span></td>
                        <td>Gerente de Servicios</td>
                        <td><span class="badge badge-success">Resuelto</span></td>
                        <td>
                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong></div>
@stop
