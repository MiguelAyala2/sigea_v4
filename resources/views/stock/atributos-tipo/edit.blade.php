@extends('adminlte::page')

@section('title', 'Editar Atributo Tipo')

@section('content_header')
    <h1>
        <i class="fas fa-edit"></i> Editar Atributo Tipo
    </h1>
@stop

@section('content')
    @livewire('stock.atributos-tipo.edit', ['atributo' => $atributo])
@stop
