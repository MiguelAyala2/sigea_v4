@extends('adminlte::page')

@section('title', 'Nueva Nota de Débito')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-plus-circle mr-2"></i>
                Nueva Nota de Débito
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ventas.notas-debito.index') }}">Notas de Débito</a></li>
                <li class="breadcrumb-item active">Nueva</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.nota-debito-form', ['facturaId' => $facturaId ?? null])
@stop
