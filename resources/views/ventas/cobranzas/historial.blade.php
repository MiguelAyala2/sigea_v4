@extends('adminlte::page')
@section('title', 'Historial de Cobranzas')
@section('content_header')<h1><i class="fas fa-history"></i> Historial de Cobranzas</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<table class="table table-bordered table-hover">
<thead class="thead-light"><tr><th>Fecha</th><th>N° Recibo</th><th>Factura</th><th>Cliente</th><th>Forma de Pago</th><th>Monto</th><th>Acciones</th></tr></thead>
<tbody>
<tr><td>16/12/2025</td><td>REC-000245</td><td>001-001-0000125</td><td>Juan Pérez</td><td>Efectivo</td><td>₲ 850.000</td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-print"><i class="fas fa-print"></i></button></td></tr>
<tr><td>15/12/2025</td><td>REC-000244</td><td>001-001-0000120</td><td>María López</td><td>Tarjeta</td><td>₲ 600.000</td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-print"><i class="fas fa-print"></i></button></td></tr>
</tbody>
</table>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
