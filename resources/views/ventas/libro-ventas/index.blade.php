@extends('adminlte::page')
@section('title', 'Libro de Ventas IVA')
@section('content_header')<h1><i class="fas fa-book"></i> Libro de Ventas IVA</h1>@stop
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title">Filtros</h3></div><div class="card-body">
<div class="row">
<div class="col-md-3"><label>Mes</label><select class="form-control"><option>Diciembre 2025</option></select></div>
<div class="col-md-3"><label>Timbrado</label><select class="form-control"><option>12345678</option></select></div>
<div class="col-md-3"><label>&nbsp;</label><button class="btn btn-primary btn-block"><i class="fas fa-search"></i> Consultar</button></div>
<div class="col-md-3"><label>&nbsp;</label><button class="btn btn-success btn-block"><i class="fas fa-file-excel"></i> Exportar</button></div>
</div>
</div></div>
<div class="card"><div class="card-body">
<table class="table table-sm table-bordered">
<thead class="thead-light"><tr><th>Fecha</th><th>N° Factura</th><th>Cliente</th><th>RUC</th><th>Gravada 10%</th><th>Gravada 5%</th><th>Exenta</th><th>Total</th></tr></thead>
<tbody>
<tr><td>16/12/2025</td><td>001-001-0000125</td><td>Juan Pérez</td><td>1234567-8</td><td>₲ 750.000</td><td>₲ 0</td><td>₲ 100.000</td><td>₲ 850.000</td></tr>
<tr><td>16/12/2025</td><td>001-001-0000126</td><td>María López</td><td>2345678-9</td><td>₲ 1.080.000</td><td>₲ 120.000</td><td>₲ 0</td><td>₲ 1.200.000</td></tr>
</tbody>
<tfoot class="table-success"><tr><td colspan="4"><strong>TOTALES:</strong></td><td><strong>₲ 1.830.000</strong></td><td><strong>₲ 120.000</strong></td><td><strong>₲ 100.000</strong></td><td><strong>₲ 2.050.000</strong></td></tr></tfoot>
</table>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
