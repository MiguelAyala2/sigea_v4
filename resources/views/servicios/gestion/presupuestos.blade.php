@extends('adminlte::page')

@section('title', 'Presupuestos de Servicio')

@section('content_header')
    <h1><i class="fas fa-file-invoice-dollar"></i> Presupuestos de Servicio</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>N° Presupuesto</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Equipo</th>
                        <th>Monto Total</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PRE-SRV-000012</td>
                        <td>16/12/2025</td>
                        <td>María López</td>
                        <td>Bomba Grundfos</td>
                        <td>₲ 850.000</td>
                        <td><span class="badge badge-warning">Pendiente Aprobación</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-print"><i class="fas fa-print"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong></div>
@stop
