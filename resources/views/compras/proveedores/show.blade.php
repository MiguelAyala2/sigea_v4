@extends('adminlte::page')

@section('title', 'Ver Proveedor')

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1>Proveedor: {{ $proveedor->razon_social }}</h1>
        </div>
        <div class="col-md-6 text-right">
            @can('compras.proveedores.editar')
                <a href="{{ route('compras.proveedores.edit', $proveedor) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endcan
            <a href="{{ route('compras.proveedores.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-id-card"></i> Información General</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Razón Social:</dt>
                        <dd class="col-sm-8">{{ $proveedor->razon_social }}</dd>

                        <dt class="col-sm-4">Nombre Fantasía:</dt>
                        <dd class="col-sm-8">{{ $proveedor->nombre_fantasia ?? '-' }}</dd>

                        <dt class="col-sm-4">RUC:</dt>
                        <dd class="col-sm-8">{{ $proveedor->ruc_formateado }}</dd>

                        <dt class="col-sm-4">Tipo Persona:</dt>
                        <dd class="col-sm-8">{{ $proveedor->tipo_persona_texto }}</dd>

                        <dt class="col-sm-4">Tipo Proveedor:</dt>
                        <dd class="col-sm-8"><span class="badge badge-info">{{ $proveedor->tipo_proveedor_texto }}</span></dd>

                        <dt class="col-sm-4">Estado:</dt>
                        <dd class="col-sm-8">
                            @if($proveedor->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-danger">Inactivo</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-phone"></i> Contacto</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Teléfono:</dt>
                        <dd class="col-sm-8">{{ $proveedor->telefono ?? '-' }}</dd>

                        <dt class="col-sm-4">Celular:</dt>
                        <dd class="col-sm-8">{{ $proveedor->celular ?? '-' }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $proveedor->email ?? '-' }}</dd>

                        <dt class="col-sm-4">Sitio Web:</dt>
                        <dd class="col-sm-8">
                            @if($proveedor->sitio_web)
                                <a href="{{ $proveedor->sitio_web }}" target="_blank">{{ $proveedor->sitio_web }}</a>
                            @else
                                -
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Compras Recientes</h3>
        </div>
        <div class="card-body">
            @if($proveedor->compras && $proveedor->compras->count() > 0)
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Nro. Factura</th>
                            <th>Estado</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proveedor->compras as $compra)
                            <tr>
                                <td>{{ $compra->fecha_emision->format('d/m/Y') }}</td>
                                <td>{{ $compra->numero_factura }}</td>
                                <td><span class="badge badge-info">{{ $compra->estado }}</span></td>
                                <td>₲ {{ number_format($compra->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted text-center">No hay compras registradas para este proveedor.</p>
            @endif
        </div>
    </div>
@stop
