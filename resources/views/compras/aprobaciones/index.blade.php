@extends('layouts.app')

@section('title', 'Aprobaciones')
@section('content_header_title', 'Aprobaciones')
@section('content_header_subtitle', 'Gestión de flujos de aprobación')

@section('content_body')
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-check-double mr-2"></i>Flujos de Aprobación</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                Esta sección muestra todos los flujos de aprobación configurados en el sistema.
                Próximamente podrá gestionar los diferentes niveles de aprobación por tipo de documento.
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>3</h3>
                            <p>Niveles de Aprobación</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>5</h3>
                            <p>Tipos de Documentos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>8</h3>
                            <p>Roles Configurados</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center py-4">
                <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Configuración en Desarrollo</h5>
                <p class="text-muted">
                    La configuración avanzada de flujos de aprobación estará disponible 
                    en la próxima versión del módulo Compras.
                </p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .small-box {
        border-radius: 5px;
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        color: white;
    }
    .small-box>.inner {
        padding: 10px;
    }
    .small-box h3 {
        font-size: 38px;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    .small-box p {
        font-size: 15px;
        margin: 0;
    }
    .small-box .icon {
        position: absolute;
        top: -10px;
        right: 10px;
        z-index: 0;
        font-size: 70px;
        color: rgba(255,255,255,0.15);
    }
</style>
@endpush