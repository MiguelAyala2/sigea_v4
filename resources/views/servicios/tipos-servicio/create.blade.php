@extends('adminlte::page')

@section('title', 'Nuevo Tipo de Servicio')

@section('content_header')
    <h1><i class="fas fa-plus"></i> Nuevo Tipo de Servicio</h1>
@stop

@section('content')
    @livewire('servicios.tipos-servicio.create')
@stop

@section('css')
@stop

@section('js')
@stop
