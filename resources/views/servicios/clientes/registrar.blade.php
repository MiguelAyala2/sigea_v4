@extends('adminlte::page')

@section('title', 'Registrar Cliente')

@section('content_header')
    <h1><i class="fas fa-user-plus"></i> Registrar Cliente</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Datos del Cliente</h3>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de Cliente *</label>
                            <select class="form-control">
                                <option>Persona Física</option>
                                <option>Persona Jurídica</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Documento/RUC *</label>
                            <input type="text" class="form-control" placeholder="Ej: 12345678-9">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre/Razón Social *</label>
                            <input type="text" class="form-control" placeholder="Nombre completo o razón social">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre Fantasía</label>
                            <input type="text" class="form-control" placeholder="Nombre comercial">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Teléfono *</label>
                            <input type="text" class="form-control" placeholder="(021) 123-4567">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Celular</label>
                            <input type="text" class="form-control" placeholder="0981 123-456">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" placeholder="cliente@email.com">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Dirección</label>
                            <textarea class="form-control" rows="2" placeholder="Dirección completa"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ciudad</label>
                            <input type="text" class="form-control" placeholder="Ciudad">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Departamento</label>
                            <select class="form-control">
                                <option>Central</option>
                                <option>Alto Paraná</option>
                                <option>Itapúa</option>
                                <option>Asunción</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>País</label>
                            <input type="text" class="form-control" value="Paraguay" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea class="form-control" rows="3" placeholder="Notas adicionales sobre el cliente"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Guardar Cliente
                        </button>
                        <a href="#" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong> - Esta es una interfaz de demostración sin funcionalidad backend.
    </div>
@stop
