@extends('adminlte::page')
@section('title', 'Registrar Pedido')
@section('content_header')<h1><i class="fas fa-plus-circle"></i> Registrar Pedido de Cliente</h1>@stop
@section('content')
<div class="card"><div class="card-body"><form>
<div class="row">
<div class="col-md-6"><label>Cliente *</label><input type="text" class="form-control" placeholder="Buscar cliente..."></div>
<div class="col-md-3"><label>Fecha Pedido</label><input type="date" class="form-control" value="2025-12-16"></div>
<div class="col-md-3"><label>Fecha Entrega</label><input type="date" class="form-control"></div>
</div>
<h5 class="mt-3">Productos</h5>
<table class="table table-sm table-bordered">
<thead class="thead-light"><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Total</th><th>Acción</th></tr></thead>
<tbody><tr><td><input type="text" class="form-control form-control-sm"></td><td><input type="number" class="form-control form-control-sm"></td><td><input type="number" class="form-control form-control-sm"></td><td>₲ 0</td><td><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td></tr></tbody>
<tfoot><tr class="table-success"><td colspan="3" class="text-right"><strong>TOTAL:</strong></td><td colspan="2"><strong>₲ 0</strong></td></tr></tfoot>
</table>
<button class="btn btn-success"><i class="fas fa-save"></i> Guardar Pedido</button>
</form></div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
