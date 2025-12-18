@extends('layouts.app')

@section('title', 'Mis Aprobaciones')
@section('content_header_title', 'Mis Aprobaciones')
@section('content_header_subtitle', 'Historial de documentos aprobados/rechazados')

@section('content_body')
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-history mr-2"></i>Mi Historial de Aprobaciones</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                Aquí podrá ver el historial completo de todos los documentos que ha aprobado o rechazado.
                Esta funcionalidad está en desarrollo.
            </div>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Aprobados</span>
                            <span class="info-box-number">42</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 70%"></div>
                            </div>
                            <span class="progress-description">70% de total</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Rechazados</span>
                            <span class="info-box-number">8</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 15%"></div>
                            </div>
                            <span class="progress-description">15% de total</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Observados</span>
                            <span class="info-box-number">5</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 10%"></div>
                            </div>
                            <span class="progress-description">10% de total</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="info-box bg-secondary">
                        <span class="info-box-icon"><i class="fas fa-chart-bar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Promedio</span>
                            <span class="info-box-number">2.3 días</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 65%"></div>
                            </div>
                            <span class="progress-description">Tiempo respuesta</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Estadísticas en Desarrollo</h5>
                <p class="text-muted">
                    El sistema de estadísticas y reportes de aprobaciones está siendo implementado.
                    Estará disponible en la próxima actualización.
                </p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .info-box {
        box-shadow: 0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2);
        border-radius: .25rem;
        background: #fff;
        display: flex;
        margin-bottom: 1rem;
        min-height: 80px;
        padding: .5rem;
        position: relative;
    }
    .info-box .info-box-icon {
        border-radius: .25rem;
        align-items: center;
        display: flex;
        font-size: 1.875rem;
        justify-content: center;
        text-align: center;
        width: 70px;
    }
    .info-box .info-box-content {
        flex: 1;
        padding: 0 10px;
    }
    .info-box .info-box-number {
        font-size: 2.2rem;
        font-weight: 700;
    }
    .info-box .progress {
        height: 5px;
        margin: 5px 0;
    }
</style>
@endpush