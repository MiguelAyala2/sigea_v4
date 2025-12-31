@extends('adminlte::page')

@section('title', 'Entrega de Servicios')

@section('content_header')
    <h1>
        <i class="fas fa-check-circle mr-2"></i>
        Entrega / Cierre de Servicios
    </h1>
@stop

@section('content')
    @livewire('servicios.entrega.index')
@stop
