@extends('adminlte::page')

@section('title', 'Nueva Compra')

@section('content_header')
    <h1>Registrar Nueva Compra (Factura)</h1>
@stop

@section('content')
    @livewire('compras.compra-form')
@stop

@section('css')
@stop

@section('js')
@stop

@push('css')
    <style>
        .form-step {
            border-left: 4px solid #007bff;
            padding-left: 15px;
            margin-bottom: 20px;
        }
        .step-title {
            font-weight: 600;
            color: #007bff;
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-calcular totales
            Livewire.on('calcularTotales', function(data) {
                console.log('Totales calculados:', data);
            });
            
            // Confirmación al salir sin guardar
            window.addEventListener('beforeunload', function(e) {
                if (Livewire.get('hasChanges')) {
                    e.preventDefault();
                    e.returnValue = '¿Está seguro de salir? Los cambios no guardados se perderán.';
                }
            });
        });
    </script>
@endpush