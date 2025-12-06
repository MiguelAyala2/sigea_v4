@extends('layouts.app')

@section('subtitle', 'Sucursales')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Sucursales')

@section('content_body')
    @livewire('empresa.sucursales.index')
@stop