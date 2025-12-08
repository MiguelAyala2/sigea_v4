@extends('layouts.app')

@section('subtitle', 'Stock')
@section('content_header_title', 'Unidades de Medida')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    @livewire('stock.unidades-medida.edit', ['unidadMedida' => $unidadMedida])
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2/css/select2.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/adminlte/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
@stop
