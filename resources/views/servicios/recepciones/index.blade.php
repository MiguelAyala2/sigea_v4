@extends('layouts.app')

@section('subtitle', 'Recepciones de Equipos')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Recepciones de Equipos')

@section('content_body')
    @livewire('servicios.recepciones.index')
@stop
