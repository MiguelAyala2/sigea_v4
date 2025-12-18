@extends('layouts.app')

@section('title', 'Nueva Recepción')
@section('content_header_title', 'Nueva Recepción')
@section('content_header_subtitle', 'Registrar recepción de mercadería')

@section('content_body')
    <div class="row">
        <div class="col-md-12">
            @if(isset($compra_id))
                @livewire('compras.recepciones.create', ['compra_id' => $compra_id])
            @else
                @livewire('compras.recepciones.create')
            @endif
        </div>
    </div>
@endsection

@push('css')
    <style>
        .select2-container--default .select2-selection--single {
            height: calc(1.8125rem + 2px) !important;
            padding: .25rem .5rem !important;
            font-size: .875rem !important;
            line-height: 1.5 !important;
        }
        .form-section {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .section-title {
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            margin-bottom: 15px;
            color: #495057;
            font-weight: 600;
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Select2 si es necesario
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
            }
            
            // Validación de fechas
            const fechaRecepcion = document.getElementById('fecha_recepcion');
            const fechaRemision = document.getElementById('fecha_remision');
            
            if (fechaRecepcion && fechaRemision) {
                fechaRemision.addEventListener('change', function() {
                    if (new Date(this.value) < new Date(fechaRecepcion.value)) {
                        alert('La fecha de remisión no puede ser anterior a la fecha de recepción.');
                        this.value = '';
                    }
                });
            }
        });
    </script>
@endpush