@extends('layouts.app')

@section('subtitle', 'Empresa')
@section('content_header_title', 'Configuración')
@section('content_header_subtitle', 'Datos de la Empresa')

@section('content_body')
    @livewire('empresa.empresa.index')
@stop