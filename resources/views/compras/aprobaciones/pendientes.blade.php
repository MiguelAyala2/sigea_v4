@extends('layouts.app')

@section('title', 'Aprobaciones Pendientes')
@section('content_header_title', 'Aprobaciones Pendientes')
@section('content_header_subtitle', 'Documentos que requieren su atención')

@section('content_body')
    @livewire('compras.aprobaciones.pendientes')
@endsection