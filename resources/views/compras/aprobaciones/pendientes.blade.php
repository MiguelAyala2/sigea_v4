@extends('layouts.app')

@section('title', 'Aprobaciones Pendientes')
@section('content_header_title', 'Aprobaciones Pendientes')
@section('content_header_subtitle', 'Documentos que requieren su atención')

@section('content_body')
    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clock mr-2"></i>Mis Aprobaciones Pendientes</h5>
                <div class="badge badge-warning">3 pendientes</div>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Esta funcionalidad está en desarrollo. Pronto podrá ver y gestionar 
                todas las aprobaciones pendientes asignadas a su usuario.
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Documento</th>
                            <th>Solicitante</th>
                            <th>Monto</th>
                            <th>Vence</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-warning">
                            <td><i class="fas fa-file-invoice text-primary"></i> Compra</td>
                            <td>FC-001-0001234</td>
                            <td>Juan Pérez</td>
                            <td class="text-success">Gs. 5.250.000</td>
                            <td><span class="badge badge-danger">Hoy</span></td>
                            <td><span class="badge badge-warning">Pendiente</span></td>
                            <td>
                                <button class="btn btn-sm btn-success" disabled>
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="table-info">
                            <td><i class="fas fa-file-signature text-info"></i> Orden Compra</td>
                            <td>OC-2025-00123</td>
                            <td>María González</td>
                            <td class="text-success">Gs. 3.800.000</td>
                            <td><span class="badge badge-warning">2 días</span></td>
                            <td><span class="badge badge-info">Revisión</span></td>
                            <td>
                                <button class="btn btn-sm btn-success" disabled>
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-box-open text-success"></i> Recepción</td>
                            <td>REC-2025-00456</td>
                            <td>Carlos Ramírez</td>
                            <td class="text-success">Gs. 8.900.000</td>
                            <td><span class="badge badge-success">5 días</span></td>
                            <td><span class="badge badge-secondary">Espera</span></td>
                            <td>
                                <button class="btn btn-sm btn-success" disabled>
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="text-center py-3">
                <p class="text-muted">
                    <i class="fas fa-hard-hat mr-1"></i>
                    Esta sección está en construcción. Pronto estará completamente funcional.
                </p>
            </div>
        </div>
    </div>
@endsection