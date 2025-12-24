@extends('adminlte::page')

@section('title', 'Editar Promoción')

@section('content')
    @livewire('servicios.promociones.edit', ['id' => $id])
@stop
