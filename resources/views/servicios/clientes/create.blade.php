@extends('layouts.app')

@section('subtitle', 'Registrar Cliente')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Registrar Cliente')

@section('content_body')
    @livewire('servicios.clientes.create')
@stop
