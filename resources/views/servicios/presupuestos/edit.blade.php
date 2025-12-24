@extends('adminlte::page')

@section('title', 'Editar Presupuesto')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-edit"></i> Editar Presupuesto</h1>
        <div>
            <button type="button" class="btn btn-info" onclick="imprimirPresupuesto()">
                <i class="fas fa-print"></i> Imprimir PDF
            </button>
            <a href="{{ route('servicios.presupuestos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    @livewire('servicios.presupuestos.edit', ['id' => $presupuesto->id])
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
@stop
