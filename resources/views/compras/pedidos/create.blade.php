@extends('adminlte::page')

@section('title', 'Nuevo Pedido de Compra')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Nuevo Pedido de Compra</h1>
        <a href="{{ route('compras.pedidos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @livewire('compras.pedido-compra-form')
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
