@extends('adminlte::page')

@section('title', 'Ajuste de Stock')

@section('content_header')
    <h1>
        <i class="fas fa-balance-scale"></i> Ajuste de Stock
    </h1>
@stop

@section('content')
    @livewire('stock.ajuste-stock')
@stop

@section('css')
    <style>
        .badge-lg {
            font-size: 1.1rem;
            padding: 0.5rem 0.75rem;
        }
    </style>
@stop
