@extends('adminlte::page')
@section('title', 'Movimientos de Caja')
@section('content_header')
    <h1><i class="fas fa-exchange-alt"></i> Movimientos de Caja</h1>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr><th>Hora</th><th>Tipo</th><th>Concepto</th><th>Entrada</th><th>Salida</th><th>Saldo</th></tr>
                </thead>
                <tbody>
                    <tr><td>08:00</td><td><span class="badge badge-success">Apertura</span></td><td>Fondo Fijo</td><td>₲ 500.000</td><td>-</td><td>₲ 500.000</td></tr>
                    <tr><td>09:15</td><td><span class="badge badge-primary">Venta</span></td><td>Factura 001-001-0000125</td><td>₲ 250.000</td><td>-</td><td>₲ 750.000</td></tr>
                    <tr><td>10:30</td><td><span class="badge badge-danger">Egreso</span></td><td>Compra Insumos</td><td>-</td><td>₲ 150.000</td><td>₲ 600.000</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
