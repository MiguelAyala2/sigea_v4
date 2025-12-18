@extends('adminlte::page')

@section('title', 'Descuentos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-percent"></i> Descuentos</h1>
        <button class="btn btn-success"><i class="fas fa-plus"></i> Nuevo Descuento</button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Tipo Descuento</th>
                        <th>Valor</th>
                        <th>Aplicable a</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>DESC-001</td>
                        <td>Cliente Frecuente</td>
                        <td>Porcentaje</td>
                        <td>15%</td>
                        <td>Todos los servicios</td>
                        <td><span class="badge badge-success">Activo</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>DESC-002</td>
                        <td>Pago al Contado</td>
                        <td>Monto Fijo</td>
                        <td>₲ 50.000</td>
                        <td>Servicios > ₲ 500.000</td>
                        <td><span class="badge badge-success">Activo</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong></div>
@stop
