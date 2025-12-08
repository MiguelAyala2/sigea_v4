@extends('adminlte::page')

@section('title', 'Reporte Rotación')

@section('content_header')
    <h1>
        <i class="fas fa-sync-alt"></i> Reporte de Rotación de Inventario
    </h1>
@stop

@section('content')
    @livewire('stock.reportes.rotacion')
@stop

@section('css')
    <style>
        .badge-lg {
            font-size: 1rem;
            padding: 0.4rem 0.6rem;
        }
    </style>
@stop
