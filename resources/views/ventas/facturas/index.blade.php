@extends('adminlte::page')

@section('title', 'Facturas de Venta')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-file-invoice-dollar mr-2"></i>
                Facturas de Venta
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Facturas</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.factura-list')
@stop

@section('css')
    <style>
        .badge {
            font-size: 0.85em;
        }
    </style>
@stop
