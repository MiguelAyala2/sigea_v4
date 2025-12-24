@extends('layouts.app')

@section('subtitle', 'Diagnósticos')
@section('content_header_title', 'Servicios')
@section('content_header_subtitle', 'Diagnósticos')

@section('content_body')
    @livewire('servicios.diagnosticos.index')
@stop
