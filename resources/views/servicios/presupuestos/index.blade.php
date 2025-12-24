@extends('adminlte::page')

@section('title', 'Presupuestos')

@section('content_header')
    <h1><i class="fas fa-file-invoice-dollar"></i> Presupuestos de Servicio</h1>
@stop

@section('content')
    @livewire('servicios.presupuestos.index')
@stop
