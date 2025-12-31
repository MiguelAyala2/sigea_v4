@extends('adminlte::page')

@section('title', 'Registrar Cobranza')

@section('content_header')
    <h1>
        <i class="fas fa-dollar-sign mr-2"></i>
        Registrar Cobranza
    </h1>
@stop

@section('content')
    @livewire('ventas.cobranzas.registrar-cobranza')
@stop
