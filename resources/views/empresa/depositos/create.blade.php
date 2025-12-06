@extends('layouts.app')

@section('subtitle', 'Nuevo Depósito')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Nuevo Depósito')

@section('content_body')
    @livewire('empresa.depositos.create')
@stop