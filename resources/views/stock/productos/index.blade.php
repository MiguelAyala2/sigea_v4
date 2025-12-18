@extends('layouts.app')

@section('subtitle', 'Stock')
@section('content_header_title', 'Productos')
@section('content_header_subtitle', 'Listado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="m-0 text-dark">Productos</h1>
            <small class="text-muted">Listado</small>
        </div>
        <div>
            <a href="{{ route('stock.productos.exportar-pdf') }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('stock.productos.exportar-excel') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
    </div>
@stop

@section('content_body')
    @livewire('stock.productos.index')
@stop
