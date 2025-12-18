@extends('adminlte::page')

@section('title', 'Editar Compra')

@section('content_header')
    <h1>Editar Compra #{{ $compra->numero_factura }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @livewire('compras.compra-form', ['compraId' => $compra->id])
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop

@push('css')
    <style>
        .readonly-field {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        .field-label {
            font-weight: 500;
            color: #495057;
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirmar cambios importantes
            Livewire.on('confirmarCambio', function(data) {
                if (confirm(data.mensaje)) {
                    Livewire.dispatch(data.evento, data.parametros);
                }
            });
        });
    </script>
@endpush