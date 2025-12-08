@extends('adminlte::page')

@section('title', 'Inventario Físico')

@section('content_header')
    <h1>
        <i class="fas fa-clipboard-list"></i> Toma de Inventario Físico
    </h1>
@stop

@section('content')
    @livewire('stock.inventario-stock')
@stop
