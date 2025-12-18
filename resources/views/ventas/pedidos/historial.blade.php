@extends('adminlte::page')
@section('title', 'Historial de Pedidos')
@section('content_header')<h1><i class="fas fa-list"></i> Historial de Pedidos</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<table class="table table-bordered table-hover">
<thead class="thead-light"><tr><th>N° Pedido</th><th>Fecha</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>
<tr><td>PED-000125</td><td>16/12/2025</td><td>Juan Pérez</td><td>₲ 850.000</td><td><span class="badge badge-warning">Pendiente</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button></td></tr>
<tr><td>PED-000124</td><td>15/12/2025</td><td>María López</td><td>₲ 1.200.000</td><td><span class="badge badge-success">Entregado</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button></td></tr>
</tbody>
</table>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
