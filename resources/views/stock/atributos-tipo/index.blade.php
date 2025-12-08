@extends('adminlte::page')

@section('title', 'Atributos Tipo')

@section('content_header')
    <h1>
        <i class="fas fa-list-ul"></i> Gestión de Atributos Tipo
    </h1>
@stop

@section('content')
    @livewire('stock.atributos-tipo.index')
@stop
