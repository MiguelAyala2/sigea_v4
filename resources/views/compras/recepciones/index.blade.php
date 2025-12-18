@extends('layouts.app')

@section('title', 'Recepciones')
@section('content_header_title', 'Recepciones de Mercadería')
@section('content_header_subtitle', 'Control de recepción vs facturas de compra')

@section('content_body')
    @livewire('compras.recepciones.index')
@endsection

@push('css')
    <style>
        .small-box {
            transition: transform 0.2s;
        }
        .small-box:hover {
            transform: translateY(-5px);
        }
        .badge-completa {
            background-color: #28a745;
        }
        .badge-parcial {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-pendiente {
            background-color: #dc3545;
        }
    </style>
@endpush