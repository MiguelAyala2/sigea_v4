@extends('adminlte::page')

@section('title', 'Registrar Reclamo')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Registrar Reclamo de Cliente</h1>
@stop

@section('content')
    @livewire('servicios.reclamos-manager')
@stop
