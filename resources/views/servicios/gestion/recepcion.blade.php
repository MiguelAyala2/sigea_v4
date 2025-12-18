@extends('adminlte::page')

@section('title', 'Recepción de Equipos')

@section('content_header')
    <h1><i class="fas fa-box-open"></i> Recepción de Equipos / Productos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Datos de Recepción</h3>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>N° Recepción</label>
                            <input type="text" class="form-control" value="REC-000025" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha de Recepción *</label>
                            <input type="date" class="form-control" value="2025-12-16">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Solicitud Relacionada</label>
                            <select class="form-control">
                                <option>SOL-000015 - María López</option>
                                <option>SOL-000014 - Carlos Rodríguez</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cliente *</label>
                            <input type="text" class="form-control" placeholder="Buscar cliente...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Contacto del Cliente</label>
                            <input type="text" class="form-control" placeholder="Teléfono o email">
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Datos del Equipo / Producto</h5>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo de Equipo *</label>
                            <select class="form-control">
                                <option>Bomba de Agua</option>
                                <option>Motor Eléctrico</option>
                                <option>Equipo Hidráulico</option>
                                <option>Otro</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Marca</label>
                            <input type="text" class="form-control" placeholder="Marca del equipo">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Modelo</label>
                            <input type="text" class="form-control" placeholder="Modelo">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>N° de Serie</label>
                            <input type="text" class="form-control" placeholder="Número de serie">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Estado de Recepción *</label>
                            <select class="form-control">
                                <option>Bueno</option>
                                <option>Regular</option>
                                <option>Malo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Descripción del Problema / Motivo *</label>
                            <textarea class="form-control" rows="3" placeholder="Detalle el problema o motivo del servicio"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Accesorios Recibidos</label>
                            <textarea class="form-control" rows="2" placeholder="Cable, control remoto, manual, etc."></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Registrar Recepción
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong> - Formulario de demostración sin funcionalidad.
    </div>
@stop
