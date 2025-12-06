@extends('layouts.app')

@section('subtitle', 'Editar Timbrado')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Editar Timbrado')

@section('content_body')
    @livewire('empresa.timbrados.edit', ['timbrado' => $timbrado])
@stop