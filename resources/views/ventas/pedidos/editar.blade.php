@extends('adminlte::page')

@section('title', 'Editar Pedido')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Pedido {{ $pedido->numero_pedido }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Modificar Pedido de Cliente</h3>
            <div class="card-tools">
                <span class="badge badge-warning">Estado: {{ $pedido->estado }}</span>
            </div>
        </div>
        <div class="card-body">
            @livewire('ventas.pedido-cliente-form', ['pedidoId' => $pedido->id])
        </div>
    </div>
@stop
