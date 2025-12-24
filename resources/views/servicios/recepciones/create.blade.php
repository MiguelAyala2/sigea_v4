@extends('layouts.app')

@section('subtitle', 'Recepción de Equipos / Productos')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Recepción de Equipos / Productos')

@section('content_body')
    @livewire('servicios.recepciones.create')
@stop
