@extends('adminlte::page')
@section('title', 'Notas de Remisión')
@section('content_header')<h1><i class="fas fa-truck"></i> Notas de Remisión</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<table class="table table-bordered table-hover">
<thead class="thead-light"><tr><th>N° Remisión</th><th>Fecha</th><th>Cliente</th><th>Productos</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>
<tr><td>REM-000045</td><td>16/12/2025</td><td>Juan Pérez</td><td>5 items</td><td><span class="badge badge-warning">Pendiente Entrega</span></td><td><button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-print"><i class="fas fa-print"></i></button></td></tr>
</tbody>
</table>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
