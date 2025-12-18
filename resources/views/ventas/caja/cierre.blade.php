@extends('adminlte::page')
@section('title', 'Cierre de Caja')
@section('content_header')<h1><i class="fas fa-lock"></i> Cierre de Caja</h1>@stop
@section('content')
<div class="card"><div class="card-header bg-danger text-white"><h3 class="card-title">Resumen del Día</h3></div><div class="card-body">
<table class="table"><tr><td><strong>Saldo Inicial:</strong></td><td class="text-right">₲ 500.000</td></tr>
<tr><td><strong>Total Ingresos:</strong></td><td class="text-right text-success">₲ 2.500.000</td></tr>
<tr><td><strong>Total Egresos:</strong></td><td class="text-right text-danger">₲ 350.000</td></tr>
<tr class="table-primary"><td><strong>Saldo Esperado:</strong></td><td class="text-right"><strong>₲ 2.650.000</strong></td></tr>
<tr><td><strong>Saldo Real Contado:</strong></td><td><input type="number" class="form-control" placeholder="₲"></td></tr>
<tr><td><strong>Diferencia:</strong></td><td class="text-right">₲ 0</td></tr></table>
<button class="btn btn-danger btn-lg"><i class="fas fa-lock"></i> Cerrar Caja</button>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
