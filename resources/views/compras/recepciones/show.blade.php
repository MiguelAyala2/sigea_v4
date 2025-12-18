@extends('layouts.app')

@section('title', 'Detalle de Recepción')
@section('content_header_title', 'Detalle de Recepción')
@section('content_header_subtitle', 'Información completa de la recepción de mercadería')

@section('content_body')
    <div class="row">
        <div class="col-md-12">
            @livewire('compras.recepciones.show', ['recepcion' => $recepcion])
        </div>
    </div>
@endsection

@push('css')
    <style>
        .recepcion-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .item-recepcion {
            border-left: 3px solid #28a745;
            padding-left: 15px;
            margin-bottom: 10px;
        }
    </style>
@endpush