@extends('layouts.app')

@section('subtitle', 'Nuevo Punto de Expedición')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Nuevo Punto de Expedición')

@section('content_body')
    @livewire('empresa.puntos-expedicion.create')
@stop