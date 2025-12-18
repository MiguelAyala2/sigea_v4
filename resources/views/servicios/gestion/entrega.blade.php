@extends('adminlte::page')

@section('title', 'Entrega y Cierre de Servicio')

@section('content_header')
    <h1><i class="fas fa-check-circle"></i> Entrega y Cierre de Servicio</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Información del Servicio</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><p><strong>N° Orden:</strong> ORD-SRV-000025</p></div>
                <div class="col-md-3"><p><strong>Cliente:</strong> María López</p></div>
                <div class="col-md-3"><p><strong>Equipo:</strong> Bomba Grundfos</p></div>
                <div class="col-md-3"><p><strong>Técnico:</strong> Ing. Juan Técnico</p></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success">
            <h3 class="card-title">Datos de Entrega</h3>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha de Entrega *</label>
                            <input type="date" class="form-control" value="2025-12-16">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Persona que Retira *</label>
                            <input type="text" class="form-control" placeholder="Nombre completo">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Documento</label>
                            <input type="text" class="form-control" placeholder="C.I. o RUC">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Observaciones de Entrega</label>
                            <textarea class="form-control" rows="3" placeholder="Notas sobre la entrega del equipo"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="garantia">
                            <label class="form-check-label" for="garantia">
                                El servicio incluye garantía de 90 días
                            </label>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Resumen del Servicio</h5>
                <table class="table table-sm">
                    <tr><td><strong>Mano de Obra:</strong></td><td class="text-right">₲ 500.000</td></tr>
                    <tr><td><strong>Repuestos:</strong></td><td class="text-right">₲ 115.000</td></tr>
                    <tr><td><strong>Otros Costos:</strong></td><td class="text-right">₲ 50.000</td></tr>
                    <tr class="table-success"><td><strong>TOTAL:</strong></td><td class="text-right"><strong>₲ 665.000</strong></td></tr>
                </table>

                <hr>
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-check-circle"></i> Finalizar y Entregar</button>
                <button type="button" class="btn btn-info btn-lg"><i class="fas fa-print"></i> Imprimir Comprobante</button>
            </form>
        </div>
    </div>

    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong></div>
@stop
