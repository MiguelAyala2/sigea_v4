@extends('layouts.app')

@section('title', 'Mis Aprobaciones')
@section('content_header_title', 'Mis Aprobaciones')
@section('content_header_subtitle', 'Historial de documentos aprobados/rechazados')

@section('content_body')
    @livewire('compras.aprobaciones.mis-aprobaciones')
@endsection

@push('styles')
<style>
    .info-box {
        box-shadow: 0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2);
        border-radius: .25rem;
        background: #fff;
        display: flex;
        margin-bottom: 1rem;
        min-height: 80px;
        padding: .5rem;
        position: relative;
    }
    .info-box .info-box-icon {
        border-radius: .25rem;
        align-items: center;
        display: flex;
        font-size: 1.875rem;
        justify-content: center;
        text-align: center;
        width: 70px;
    }
    .info-box .info-box-content {
        flex: 1;
        padding: 0 10px;
    }
    .info-box .info-box-number {
        font-size: 2.2rem;
        font-weight: 700;
    }
    .info-box .progress {
        height: 5px;
        margin: 5px 0;
    }
</style>
@endpush