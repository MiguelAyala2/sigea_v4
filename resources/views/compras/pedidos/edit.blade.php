@extends('adminlte::page')

@section('title', 'Editar Pedido de Compra')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Editar Pedido de Compra - {{ $pedido->numero_pedido }}</h1>
        <a href="{{ route('compras.pedidos.show', $pedido->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @livewire('compras.pedido-compra-form', ['pedidoId' => $pedido->id])
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
