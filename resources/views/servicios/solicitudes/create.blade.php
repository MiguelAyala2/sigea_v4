@extends('layouts.app')

@section('subtitle', 'Nueva Solicitud de Servicio')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Nueva Solicitud de Servicio')

@section('content_body')
    @livewire('servicios.solicitudes.create')
@stop
