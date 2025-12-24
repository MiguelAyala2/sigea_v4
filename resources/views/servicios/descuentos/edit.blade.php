@extends('adminlte::page')

@section('title', 'Editar Descuento')

@section('content')
    @livewire('servicios.descuentos.edit', ['id' => $id])
@stop
