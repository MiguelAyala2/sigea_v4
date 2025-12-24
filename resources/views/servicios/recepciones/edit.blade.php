@extends('layouts.app')

@section('subtitle', 'Editar Recepción')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Editar Recepción de Equipo / Producto')

@section('content_body')
    @livewire('servicios.recepciones.edit', ['recepcion' => $recepcion])
@stop
