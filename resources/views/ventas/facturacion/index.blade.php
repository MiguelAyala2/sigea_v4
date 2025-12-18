@extends('adminlte::page')
@section('title', 'Generar Venta / Factura')
@section('content_header')<h1><i class="fas fa-receipt"></i> Generar Venta / Factura</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<div class="row">
<div class="col-md-4"><label>Cliente *</label><input type="text" class="form-control" placeholder="Buscar cliente..."></div>
<div class="col-md-4"><label>Timbrado</label><select class="form-control"><option>12345678 (Vigente)</option></select></div>
<div class="col-md-4"><label>N° Factura</label><input type="text" class="form-control" value="001-001-0000125" readonly></div>
</div>
<h5 class="mt-3">Productos</h5>
<table class="table table-sm table-bordered">
<thead class="thead-light"><tr><th>Código</th><th>Producto</th><th>Cant.</th><th>Precio</th><th>IVA %</th><th>Subtotal</th><th>Acción</th></tr></thead>
<tbody><tr><td><input type="text" class="form-control form-control-sm"></td><td>-</td><td><input type="number" class="form-control form-control-sm"></td><td><input type="number" class="form-control form-control-sm"></td><td><select class="form-control form-control-sm"><option>10%</option><option>5%</option><option>Exenta</option></select></td><td>₲ 0</td><td><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td></tr></tbody>
<tfoot><tr class="table-success"><td colspan="5" class="text-right"><strong>TOTAL:</strong></td><td colspan="2"><strong>₲ 0</strong></td></tr></tfoot>
</table>
<button class="btn btn-success btn-lg"><i class="fas fa-save"></i> Generar Factura</button>
<button class="btn btn-info btn-lg"><i class="fas fa-print"></i> Imprimir</button>
</div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
