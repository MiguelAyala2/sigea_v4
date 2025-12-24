@extends('layouts.app')

@section('subtitle', 'Editar Cliente')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Editar Cliente')

@section('content_body')
    @livewire('servicios.clientes.edit', ['cliente' => $cliente])
@stop
