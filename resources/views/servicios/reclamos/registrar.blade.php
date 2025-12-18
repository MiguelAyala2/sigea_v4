@extends('adminlte::page')

@section('title', 'Registrar Reclamo')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Registrar Reclamo de Cliente</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h3 class="card-title">Datos del Reclamo</h3>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cliente *</label>
                            <input type="text" class="form-control" placeholder="Buscar cliente...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Servicio Relacionado</label>
                            <select class="form-control">
                                <option>SRV-000012 - Mantenimiento Bomba (10/12/2025)</option>
                                <option>SRV-000008 - Reparación Motor (28/11/2025)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo de Reclamo *</label>
                            <select class="form-control">
                                <option>Calidad del Servicio</option>
                                <option>Demora en Entrega</option>
                                <option>Falla Post-Servicio</option>
                                <option>Atención al Cliente</option>
                                <option>Costo/Facturación</option>
                                <option>Otro</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Prioridad *</label>
                            <select class="form-control">
                                <option>Baja</option>
                                <option>Media</option>
                                <option>Alta</option>
                                <option>Urgente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha del Reclamo</label>
                            <input type="date" class="form-control" value="2025-12-16">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Descripción del Reclamo *</label>
                            <textarea class="form-control" rows="4" placeholder="Detalle el motivo del reclamo"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Responsable Asignado</label>
                            <select class="form-control">
                                <option>Gerente de Servicios</option>
                                <option>Supervisor Técnico</option>
                                <option>Atención al Cliente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Canal de Recepción</label>
                            <select class="form-control">
                                <option>Presencial</option>
                                <option>Teléfono</option>
                                <option>Email</option>
                                <option>WhatsApp</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-danger btn-lg"><i class="fas fa-save"></i> Registrar Reclamo</button>
                <button type="button" class="btn btn-secondary btn-lg"><i class="fas fa-times"></i> Cancelar</button>
            </form>
        </div>
    </div>

    <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong></div>
@stop
