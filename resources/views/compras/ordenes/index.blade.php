@extends('adminlte::page')

@section('title', 'Órdenes de Compra')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Órdenes de Compra</h1>
        <div>
            <a href="{{ route('compras.ordenes.exportar-pdf') }}" class="btn btn-sm btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('compras.ordenes.exportar-excel') }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
            <a href="{{ route('compras.ordenes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Orden de Compra
            </a>
        </div>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @livewire('compras.orden-compra-lista')
@stop

@section('css')
@stop

@section('js')
@stop
