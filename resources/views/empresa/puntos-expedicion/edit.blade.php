@extends('layouts.app')

@section('subtitle', 'Editar Punto de Expedición')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Editar Punto de Expedición')

@section('content_body')
    @livewire('empresa.puntos-expedicion.edit', ['puntoExpedicion' => $puntoExpedicion])
@stop