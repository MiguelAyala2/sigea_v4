@extends('adminlte::page')

@section('title', 'Reporte Valorizado')

@section('content_header')
    <h1>
        <i class="fas fa-dollar-sign"></i> Reporte de Inventario Valorizado
    </h1>
@stop

@section('content')
    @livewire('stock.reportes.valorizado')
@stop
