@extends('layouts.app')

@section('subtitle', 'Stock')
@section('content_header_title', 'Marcas')
@section('content_header_subtitle', 'Listado')

@section('content_body')
    @livewire('stock.marcas.index')
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