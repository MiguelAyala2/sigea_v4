@extends('layouts.app')

@section('subtitle', 'Editar Sucursal')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Editar Sucursal')

@section('content_body')
    @livewire('empresa.sucursales.edit', ['sucursal' => $sucursal])
@stop