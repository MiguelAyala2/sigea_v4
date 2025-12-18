@extends('adminlte::page')

@section('title', 'Solicitudes de Servicio')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-clipboard-list"></i> Solicitudes de Servicio</h1>
        <button class="btn btn-success">
            <i class="fas fa-plus"></i> Nueva Solicitud
        </button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filtros</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control">
                        <option>Todos los estados</option>
                        <option>Pendiente</option>
                        <option>En Proceso</option>
                        <option>Completado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" placeholder="Fecha desde">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" placeholder="Fecha hasta">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Buscar cliente...">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>N° Solicitud</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Equipo/Producto</th>
                        <th>Tipo Servicio</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SOL-000015</td>
                        <td>16/12/2025</td>
                        <td>María López</td>
                        <td>Bomba Grundfos</td>
                        <td>Mantenimiento</td>
                        <td><span class="badge badge-warning">Media</span></td>
                        <td><span class="badge badge-info">Pendiente</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-success" title="Procesar">
                                <i class="fas fa-check"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>SOL-000014</td>
                        <td>15/12/2025</td>
                        <td>Carlos Rodríguez</td>
                        <td>Motor WEG 5HP</td>
                        <td>Reparación</td>
                        <td><span class="badge badge-danger">Alta</span></td>
                        <td><span class="badge badge-warning">En Proceso</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-success" title="Procesar">
                                <i class="fas fa-check"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>SOL-000013</td>
                        <td>14/12/2025</td>
                        <td>Ana Martínez</td>
                        <td>Equipo Hidráulico</td>
                        <td>Diagnóstico</td>
                        <td><span class="badge badge-success">Baja</span></td>
                        <td><span class="badge badge-success">Completado</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong> - Interfaz de demostración con datos ficticios.
    </div>
@stop
