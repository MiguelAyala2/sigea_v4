@extends('adminlte::page')

@section('title', 'Cobranzas por Forma de Pago')

@section('content_header')
    <h1>
        <i class="fas fa-credit-card mr-2"></i>
        Cobranzas por Forma de Pago
    </h1>
@stop

@section('content')
    @livewire('ventas.cobranzas.forma-pago')
@stop
