@extends('adminlte::page')
@section('title', 'Registrar Cobranza')
@section('content_header')<h1><i class="fas fa-dollar-sign"></i> Registrar Cobranza</h1>@stop
@section('content')
<div class="card"><div class="card-body"><form>
<div class="row">
<div class="col-md-6"><label>Factura *</label><select class="form-control"><option>001-001-0000125 - Juan Pérez - ₲ 850.000</option></select></div>
<div class="col-md-3"><label>Saldo Pendiente</label><input type="text" class="form-control" value="₲ 850.000" readonly></div>
<div class="col-md-3"><label>Fecha Cobranza</label><input type="date" class="form-control" value="2025-12-16"></div>
</div>
<h5 class="mt-3">Formas de Pago</h5>
<table class="table table-sm table-bordered">
<tr><td>Forma de Pago</td><td><select class="form-control form-control-sm"><option>Efectivo</option><option>Tarjeta</option><option>Cheque</option><option>Transferencia</option></select></td></tr>
<tr><td>Monto</td><td><input type="number" class="form-control form-control-sm" placeholder="₲"></td></tr>
<tr><td>Referencia/N° Comprobante</td><td><input type="text" class="form-control form-control-sm"></td></tr>
</table>
<button class="btn btn-success btn-lg"><i class="fas fa-save"></i> Registrar Cobranza</button>
</form></div></div>
<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual</strong></div>
@stop
