@extends('layouts.app')

@section('subtitle', 'Nuevo Diagnóstico')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Nuevo Diagnóstico')

@section('content_body')
    @livewire('servicios.diagnosticos.create')
@stop
