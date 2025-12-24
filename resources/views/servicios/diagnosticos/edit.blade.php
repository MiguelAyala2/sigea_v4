@extends('adminlte::page')

@section('title', 'Editar Diagnóstico')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Diagnóstico</h1>
@stop

@section('content')
    @livewire('servicios.diagnosticos.edit', ['id' => $diagnostico->id])
@stop

@section('css')
@stop

@section('js')
@stop
