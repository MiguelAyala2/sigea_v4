@extends('layouts.app')

@section('subtitle', 'Nuevo Timbrado')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Nuevo Timbrado')

@section('content_body')
    @livewire('empresa.timbrados.create')
@stop