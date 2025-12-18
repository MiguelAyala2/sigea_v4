@extends('layouts.app')

@section('title', 'Dashboard Compras')
@section('content_header_title', 'Dashboard')
@section('content_header_subtitle', 'Módulo de Compras')

@section('content_body')
    @livewire('compras.dashboard.index')
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
@endpush