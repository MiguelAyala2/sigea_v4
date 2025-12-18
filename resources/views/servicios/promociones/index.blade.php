@extends('adminlte::page')

@section('title', 'Promociones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-tags"></i> Promociones</h1>
        <button class="btn btn-success"><i class="fas fa-plus"></i> Nueva Promoción</button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre Promoción</th>
                        <th>Tipo</th>
                        <th>Descuento</th>
                        <th>Vigencia</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PROM-001</td>
                        <td>Mantenimiento Preventivo 2x1</td>
                        <td>Servicio</td>
                        <td>50%</td>
                        <td>01/12/2025 - 31/12/2025</td>
                        <td><span class="badge badge-success">Activa</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>PROM-002</td>
                        <td>Black Friday Servicios</td>
                        <td>General</td>
                        <td>30%</td>
                        <td>24/11/2025 - 24/11/2025</td>
                        <td><span class="badge badge-secondary">Finalizada</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong></div>
@stop
