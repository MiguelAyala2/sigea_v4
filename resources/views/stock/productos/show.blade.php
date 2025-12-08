@extends('adminlte::page')

@section('title', 'Detalle del Producto')

@section('content_header')
    <h1>
        <i class="fas fa-box"></i> Detalle del Producto
    </h1>
@stop

@section('content')
    @livewire('stock.productos.show', ['producto' => $producto])
@stop

@section('css')
    <style>
        .badge-lg {
            font-size: 1.1rem;
            padding: 0.5rem 0.75rem;
        }
    </style>
@stop
