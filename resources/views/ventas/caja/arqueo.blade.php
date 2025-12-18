@extends('adminlte::page')
@section('title', 'Arqueo de Caja')
@section('content_header')<h1><i class="fas fa-calculator"></i> Arqueo de Caja</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<h5>Detalle de Billetes y Monedas</h5>
<table class="table table-sm table-bordered">
<tr><td>₲ 100.000 x</td><td><input type="number" class="form-control form-control-sm" value="10"></td><td>=</td><td>₲ 1.000.000</td></tr>
<tr><td>₲ 50.000 x</td><td><input type="number" class="form-control form-control-sm" value="8"></td><td>=</td><td>₲ 400.000</td></tr>
<tr><td>₲ 20.000 x</td><td><input type="number" class="form-control form-control-sm" value="12"></td><td>=</td><td>₲ 240.000</td></tr>
<tr class="table-success"><td colspan="3"><strong>TOTAL EFECTIVO:</strong></td><td><strong>₲ 1.640.000</strong></td></tr>
</table>
<h5>Otros Medios de Pago</h5>
<table class="table table-sm"><tr><td>Tarjetas:</td><td class="text-right">₲ 850.000</td></tr>
<tr><td>Cheques:</td><td class="text-right">₲ 160.000</td></tr>
<tr class="table-primary"><td><strong>TOTAL GENERAL:</strong></td><td class="text-right"><strong>₲ 2.650.000</strong></td></tr></table>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
