@extends('adminlte::page')

@section('title', 'Facturación')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-file-invoice-dollar mr-2"></i>
                Facturación
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Facturación</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    <h4>Módulo de Facturación</h4>
                    <p class="text-muted">Gestión de facturas de venta</p>
                    <a href="{{ route('ventas.facturas.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-list"></i> Ver Facturas
                    </a>
                    <a href="{{ route('ventas.facturas.crear') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Nueva Factura
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
