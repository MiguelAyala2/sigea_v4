@extends('adminlte::page')

@section('title', 'Órdenes de Servicio')

@section('content_header')
    <h1><i class="fas fa-file-signature"></i> Órdenes de Servicio</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-success">
            <h3 class="card-title">Órdenes de Trabajo Activas</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>N° Orden</th>
                        <th>Cliente</th>
                        <th>Equipo</th>
                        <th>Técnico</th>
                        <th>Progreso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ORD-SRV-000025</td>
                        <td>María López</td>
                        <td>Bomba Grundfos</td>
                        <td>Ing. Juan Técnico</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 60%">60%</div>
                            </div>
                        </td>
                        <td><span class="badge badge-warning">En Proceso</span></td>
                        <td>
                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-success"><i class="fas fa-tasks"></i> Actividades</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Insumos y Repuestos Utilizados</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Costo Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>REP-001</td>
                        <td>Rodamiento SKF 6205</td>
                        <td>2</td>
                        <td>₲ 45.000</td>
                        <td>₲ 90.000</td>
                    </tr>
                    <tr>
                        <td>REP-002</td>
                        <td>Retén de aceite</td>
                        <td>1</td>
                        <td>₲ 25.000</td>
                        <td>₲ 25.000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong></div>
@stop
