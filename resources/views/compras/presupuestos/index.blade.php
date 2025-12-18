@extends('adminlte::page')

@section('title', 'Presupuestos de Proveedores')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Presupuestos de Proveedores</h1>
        <div>
            <a href="{{ route('compras.presupuestos.exportar-pdf') }}" class="btn btn-sm btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('compras.presupuestos.exportar-excel') }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
            <a href="{{ route('compras.presupuestos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Presupuesto
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @livewire('compras.presupuesto-lista')
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
