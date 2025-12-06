@extends('layouts.app')

@section('subtitle', 'Nueva Sucursal')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Nueva Sucursal')

@section('content_body')
    @livewire('empresa.sucursales.create')
@stop