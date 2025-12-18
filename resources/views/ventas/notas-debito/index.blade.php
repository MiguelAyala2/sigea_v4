@extends('adminlte::page')
@section('title', 'Notas de Débito')
@section('content_header')<h1><i class="fas fa-plus-square"></i> Notas de Débito</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<table class="table table-bordered table-hover">
<thead class="thead-light"><tr><th>N° Nota</th><th>Fecha</th><th>Factura Ref.</th><th>Cliente</th><th>Monto</th><th>Motivo</th><th>Acciones</th></tr></thead>
<tbody>
<tr><td>ND-000008</td><td>14/12/2025</td><td>001-001-0000115</td><td>Carlos Rodríguez</td><td>₲ 120.000</td><td>Interés por Mora</td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button></td></tr>
</tbody>
</table>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
