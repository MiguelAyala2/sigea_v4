@extends('adminlte::page')

@section('title', 'Tipos de Servicio')

@section('content_header')
    <h1><i class="fas fa-list-ul"></i> Tipos de Servicio</h1>
@stop

@section('content')
    @livewire('servicios.tipos-servicio.index')
@stop

@section('css')
@stop

@section('js')
@stop
