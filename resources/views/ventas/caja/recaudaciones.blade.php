@extends('adminlte::page')
@section('title', 'Recaudaciones a Depositar')
@section('content_header')<h1><i class="fas fa-university"></i> Recaudaciones a Depositar</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<table class="table table-bordered table-hover">
<thead class="thead-light"><tr><th>Fecha Cierre</th><th>Caja</th><th>Efectivo</th><th>Cheques</th><th>Total</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>
<tr><td>15/12/2025</td><td>CAJA 01</td><td>₲ 1.640.000</td><td>₲ 160.000</td><td>₲ 1.800.000</td><td><span class="badge badge-warning">Pendiente Depósito</span></td><td><button class="btn btn-sm btn-success"><i class="fas fa-check"></i> Registrar Depósito</button></td></tr>
<tr><td>14/12/2025</td><td>CAJA 02</td><td>₲ 950.000</td><td>₲ 0</td><td>₲ 950.000</td><td><span class="badge badge-success">Depositado</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button></td></tr>
</tbody>
</table>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
