@extends('adminlte::page')

@section('title', 'Reporte Stock Bajo')

@section('content_header')
    <h1>
        <i class="fas fa-exclamation-triangle"></i> Reporte de Stock Bajo y Agotado
    </h1>
@stop

@section('content')
    @livewire('stock.reportes.stock-bajo')
@stop
