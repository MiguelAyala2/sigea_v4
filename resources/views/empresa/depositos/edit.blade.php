@extends('layouts.app')

@section('subtitle', 'Editar Depósito')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Editar Depósito')

@section('content_body')
    @livewire('empresa.depositos.edit', ['deposito' => $deposito])
@stop