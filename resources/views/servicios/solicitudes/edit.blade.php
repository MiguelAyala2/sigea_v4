@extends('layouts.app')

@section('subtitle', 'Editar Solicitud de Servicio')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Editar Solicitud de Servicio')

@section('content_body')
    @livewire('servicios.solicitudes.edit', ['solicitud' => $solicitud])
@stop
