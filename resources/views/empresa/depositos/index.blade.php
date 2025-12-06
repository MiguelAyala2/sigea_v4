@extends('layouts.app')

@section('subtitle', 'Depósitos')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Depósitos')

@section('content_body')
    @livewire('empresa.depositos.index')
@stop