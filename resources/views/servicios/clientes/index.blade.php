@extends('layouts.app')

@section('subtitle', 'Clientes')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Clientes')

@section('content_body')
    @livewire('servicios.clientes.index')
@stop
