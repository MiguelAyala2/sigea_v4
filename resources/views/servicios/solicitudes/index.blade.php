@extends('layouts.app')

@section('subtitle', 'Solicitudes de Servicio')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Solicitudes de Servicio')

@section('content_body')
    @livewire('servicios.solicitudes.index')
@stop
