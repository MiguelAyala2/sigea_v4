@extends('adminlte::page')

@section('title', 'Informes de Ventas')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-chart-line mr-2"></i>
                Informes de Ventas
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Informes</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.informes-ventas')
@stop
