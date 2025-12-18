@extends('adminlte::page')
@section('title', 'Cuentas a Cobrar')
@section('content_header')<h1><i class="fas fa-hand-holding-usd"></i> Cuentas a Cobrar</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<table class="table table-bordered table-hover">
<thead class="thead-light"><tr><th>Factura</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Saldo</th><th>Vencimiento</th><th>Acciones</th></tr></thead>
<tbody>
<tr><td>001-001-0000125</td><td>Juan Pérez</td><td>10/12/2025</td><td>₲ 850.000</td><td class="text-danger">₲ 850.000</td><td>09/01/2026</td><td><button class="btn btn-sm btn-success"><i class="fas fa-dollar-sign"></i> Cobrar</button></td></tr>
<tr><td>001-001-0000120</td><td>María López</td><td>05/12/2025</td><td>₲ 1.200.000</td><td class="text-warning">₲ 600.000</td><td>04/01/2026</td><td><button class="btn btn-sm btn-success"><i class="fas fa-dollar-sign"></i> Cobrar</button></td></tr>
</tbody>
</table>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
