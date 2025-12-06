@extends('layouts.app')

@section('subtitle', 'Editar Empresa')
@section('content_header_title', 'Configuración')
@section('content_header_subtitle', 'Editar Datos de la Empresa')

@section('content_body')
    @livewire('empresa.empresa.edit')
@stop