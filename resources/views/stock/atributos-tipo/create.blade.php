@extends('adminlte::page')

@section('title', 'Crear Atributo Tipo')

@section('content_header')
    <h1>
        <i class="fas fa-plus"></i> Crear Nuevo Atributo Tipo
    </h1>
@stop

@section('content')
    @livewire('stock.atributos-tipo.create')
@stop
