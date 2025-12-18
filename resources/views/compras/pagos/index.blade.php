@extends('adminlte::page')

@section('title', 'Cuentas por Pagar')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Cuentas por Pagar / Pagos</h1>
        <div>
            <a href="{{ route('compras.pagos.exportar-pdf') }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('compras.pagos.exportar-excel') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
    </div>
@stop

@section('content')
    @livewire('compras.pago-lista')
@stop

@section('css')
@stop

@section('js')
@stop
