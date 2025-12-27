@extends('adminlte::page')

@section('title', 'Órdenes de Servicio')

@section('content_header')
    <h1><i class="fas fa-tools"></i> Órdenes de Servicio</h1>
@stop

@section('content')
    @livewire('servicios.ordenes-servicio.index')
@stop
