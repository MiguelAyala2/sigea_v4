@extends('adminlte::page')
@section('title', 'Notas de Crédito')
@section('content_header')<h1><i class="fas fa-undo"></i> Notas de Crédito</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<table class="table table-bordered table-hover">
<thead class="thead-light"><tr><th>N° Nota</th><th>Fecha</th><th>Factura Ref.</th><th>Cliente</th><th>Monto</th><th>Motivo</th><th>Acciones</th></tr></thead>
<tbody>
<tr><td>NC-000012</td><td>15/12/2025</td><td>001-001-0000120</td><td>María López</td><td>₲ 250.000</td><td>Devolución Producto</td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button></td></tr>
</tbody>
</table>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
