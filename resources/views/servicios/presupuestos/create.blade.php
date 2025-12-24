@extends('adminlte::page')

@section('title', 'Nuevo Presupuesto')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus"></i> Nuevo Presupuesto</h1>
        <div data-header-buttons>
            @stack('content_header_buttons')
        </div>
    </div>
@stop

@section('content')
    @livewire('servicios.presupuestos.create')
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
@stop
