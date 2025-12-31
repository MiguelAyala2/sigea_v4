@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Dashboard')
@section('content_header_title', 'Dashboard')
@section('content_header_subtitle', 'Panel de Control')

{{-- Content body: main page content --}}

@section('content_body')
    @livewire('dashboard.home')
@stop

@push('css')
    <style>
        .small-box {
            border-radius: 5px;
        }
        .btn-app {
            width: 100%;
        }
    </style>
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
        console.log('Dashboard SIGEA v4 cargado');
    </script>
@endpush