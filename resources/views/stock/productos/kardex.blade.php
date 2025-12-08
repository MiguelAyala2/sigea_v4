@extends('adminlte::page')

@section('title', 'Kardex de Producto')

@section('content_header')
    <h1>
        <i class="fas fa-list"></i> Kardex de Movimientos
    </h1>
@stop

@section('content')
    @livewire('stock.productos.kardex', ['producto' => $producto])
@stop

@section('css')
    <style>
        .table th {
            vertical-align: middle;
        }
        .info-box-number {
            font-size: 1.5rem;
        }
    </style>
@stop
