@extends('adminlte::page')

@section('title', 'Historial de Servicios por Cliente')

@section('content_header')
    <h1>
        <i class="fas fa-history mr-2"></i>
        Historial de Servicios por Cliente
    </h1>
@stop

@section('content')
    @livewire('servicios.clientes.historial-servicios')
@stop
