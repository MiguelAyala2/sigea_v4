@extends('adminlte::page')

@section('title', 'Historial de Servicios por Cliente')

@section('content_header')
    <h1><i class="fas fa-history"></i> Historial de Servicios por Cliente</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="card-title">Buscar Cliente</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <input type="text" class="form-control" placeholder="Buscar por nombre, documento o teléfono...">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-secondary">
            <h3 class="card-title">Datos del Cliente</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <p><strong>Cliente:</strong> Juan Pérez González</p>
                    <p><strong>Documento:</strong> 1.234.567-8</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Teléfono:</strong> (021) 123-4567</p>
                    <p><strong>Celular:</strong> 0981 123-456</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Email:</strong> juan.perez@email.com</p>
                    <p><strong>Ciudad:</strong> Asunción</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Total Servicios:</strong> <span class="badge badge-primary">12</span></p>
                    <p><strong>Última Visita:</strong> 10/12/2025</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Historial de Servicios</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Fecha</th>
                        <th>N° Servicio</th>
                        <th>Equipo/Producto</th>
                        <th>Tipo Servicio</th>
                        <th>Estado</th>
                        <th>Monto</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10/12/2025</td>
                        <td>SRV-000012</td>
                        <td>Bomba de Agua Grundfos</td>
                        <td>Mantenimiento</td>
                        <td><span class="badge badge-success">Completado</span></td>
                        <td>₲ 850.000</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>28/11/2025</td>
                        <td>SRV-000008</td>
                        <td>Motor Eléctrico WEG</td>
                        <td>Reparación</td>
                        <td><span class="badge badge-warning">En Proceso</span></td>
                        <td>₲ 1.200.000</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>15/10/2025</td>
                        <td>SRV-000005</td>
                        <td>Equipo Hidráulico</td>
                        <td>Diagnóstico</td>
                        <td><span class="badge badge-success">Completado</span></td>
                        <td>₲ 450.000</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>02/09/2025</td>
                        <td>SRV-000002</td>
                        <td>Bomba Centrífuga</td>
                        <td>Instalación</td>
                        <td><span class="badge badge-success">Completado</span></td>
                        <td>₲ 2.500.000</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong> - Esta es una interfaz de demostración con datos de ejemplo.
    </div>
@stop
