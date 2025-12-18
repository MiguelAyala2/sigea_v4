@extends('adminlte::page')
@section('title', 'Apertura de Caja')
@section('content_header')
    <h1><i class="fas fa-unlock"></i> Apertura de Caja</h1>
@stop
@section('content')
    <div class="card">
        <div class="card-header bg-success text-white"><h3 class="card-title">Nueva Apertura</h3></div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Punto de Venta *</label><select class="form-control"><option>CAJA 01 - Mostrador</option><option>CAJA 02 - Depósito</option></select></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Usuario</label><input type="text" class="form-control" value="Usuario Actual" readonly></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Fecha</label><input type="date" class="form-control" value="2025-12-16" readonly></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Hora</label><input type="time" class="form-control" value="08:00" readonly></div></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><div class="form-group"><label>Monto Inicial (Fondo Fijo) *</label><input type="number" class="form-control" placeholder="₲ 500.000"></div></div>
                    <div class="col-md-6"><div class="form-group"><label>Timbrado Activo</label><select class="form-control"><option>12345678 (Vigente hasta 31/12/2025)</option></select></div></div>
                </div>
                <div class="row"><div class="col-md-12"><div class="form-group"><label>Observaciones</label><textarea class="form-control" rows="2"></textarea></div></div></div>
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-unlock-alt"></i> Abrir Caja</button>
            </form>
        </div>
    </div>
    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong></div>
@stop
