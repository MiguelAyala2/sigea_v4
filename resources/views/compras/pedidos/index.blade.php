@extends('adminlte::page')

@section('title', 'Pedidos de Compra')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Pedidos de Compra</h1>
        <div>
            <a href="{{ route('compras.pedidos.exportar-pdf') }}" class="btn btn-sm btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('compras.pedidos.exportar-excel') }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
            <a href="{{ route('compras.pedidos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Pedido
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @livewire('compras.pedido-compra-lista')
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
