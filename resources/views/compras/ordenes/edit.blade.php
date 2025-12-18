@extends('adminlte::page')

@section('title', 'Editar Orden de Compra')

@section('content_header')
    <h1>Editar Orden de Compra #{{ $orden->numero_orden }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @livewire('compras.orden-compra-form', ['ordenId' => $orden->id])
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
