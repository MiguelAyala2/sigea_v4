@extends('adminlte::page')

@section('title', 'Editar Tipo de Servicio')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Tipo de Servicio</h1>
@stop

@section('content')
    @livewire('servicios.tipos-servicio.edit', ['id' => $id])
@stop

@section('css')
@stop

@section('js')
@stop
