@extends('layouts.app')

@section('title', 'Aprobaciones')
@section('content_header_title', 'Aprobaciones')
@section('content_header_subtitle', 'Gestión de flujos de aprobación')

@section('content_body')
    @livewire('compras.aprobaciones.index')
@endsection

@push('styles')
<style>
    .small-box {
        border-radius: 5px;
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        color: white;
    }
    .small-box>.inner {
        padding: 10px;
    }
    .small-box h3 {
        font-size: 38px;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    .small-box p {
        font-size: 15px;
        margin: 0;
    }
    .small-box .icon {
        position: absolute;
        top: -10px;
        right: 10px;
        z-index: 0;
        font-size: 70px;
        color: rgba(255,255,255,0.15);
    }
</style>
@endpush