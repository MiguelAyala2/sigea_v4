@extends('adminlte::page')

@section('title', 'Editar Factura')

@section('content_header')
    <h1>
        <i class="fas fa-edit mr-2"></i>
        Editar Factura #{{ $factura->numero_completo }}
    </h1>
@stop

@section('content')
    @livewire('ventas.factura-form', [
        'facturaId' => $factura->id
    ])
@stop
