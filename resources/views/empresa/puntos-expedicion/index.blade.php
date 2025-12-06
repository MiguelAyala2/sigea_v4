@extends('layouts.app')

@section('subtitle', 'Puntos de Expedición')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Puntos de Expedición')

@section('content_body')
    @livewire('empresa.puntos-expedicion.index')
@stop
