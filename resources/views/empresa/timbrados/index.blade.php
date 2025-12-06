@extends('layouts.app')

@section('subtitle', 'Timbrados')
@section('content_header_title', 'Empresa')
@section('content_header_subtitle', 'Timbrados')

@section('content_body')
    @livewire('empresa.timbrados.index')
@stop