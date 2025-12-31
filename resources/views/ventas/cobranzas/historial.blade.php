@extends('adminlte::page')

@section('title', 'Historial de Cobranzas')

@section('content_header')
    <h1>
        <i class="fas fa-history mr-2"></i>
        Historial de Cobranzas
    </h1>
@stop

@section('content')
    @livewire('ventas.cobranzas.historial-cobranzas')
@stop
