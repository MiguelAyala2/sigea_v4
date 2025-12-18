@extends('layouts.app')

@section('title', 'Flujo de Compra')
@section('content_header_title', 'Flujo de Compra')
@section('content_header_subtitle', 'Visualización del proceso completo')

@section('content_body')
    @livewire('compras.dashboard.flujo-compra')
@endsection