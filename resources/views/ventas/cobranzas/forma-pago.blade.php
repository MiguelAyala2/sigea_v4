@extends('adminlte::page')
@section('title', 'Cobranzas por Forma de Pago')
@section('content_header')<h1><i class="fas fa-credit-card"></i> Cobranzas por Forma de Pago</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<table class="table table-bordered table-hover">
<thead class="thead-light"><tr><th>Forma de Pago</th><th>Cantidad</th><th>Total Cobrado</th><th>Porcentaje</th></tr></thead>
<tbody>
<tr><td><i class="fas fa-money-bill-wave text-success"></i> Efectivo</td><td>45</td><td>₲ 8.500.000</td><td>42%</td></tr>
<tr><td><i class="fas fa-credit-card text-primary"></i> Tarjeta</td><td>32</td><td>₲ 7.200.000</td><td>35%</td></tr>
<tr><td><i class="fas fa-money-check text-info"></i> Cheque</td><td>12</td><td>₲ 2.800.000</td><td>14%</td></tr>
<tr><td><i class="fas fa-exchange-alt text-warning"></i> Transferencia</td><td>18</td><td>₲ 1.900.000</td><td>9%</td></tr>
<tr class="table-success"><td><strong>TOTAL</strong></td><td><strong>107</strong></td><td><strong>₲ 20.400.000</strong></td><td><strong>100%</strong></td></tr>
</tbody>
</table>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
