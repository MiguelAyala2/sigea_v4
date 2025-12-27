@extends('adminlte::page')

@section('title', 'Seguimiento de Reclamos')

@section('content_header')
    <h1><i class="fas fa-search"></i> Seguimiento de Reclamos</h1>
@stop

@section('content')
    @livewire('servicios.reclamos-index')
@stop
