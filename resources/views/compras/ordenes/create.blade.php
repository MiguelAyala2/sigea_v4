@extends('adminlte::page')

@section('title', 'Nueva Orden de Compra')

@section('content_header')
    <h1>Nueva Orden de Compra</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @livewire('compras.orden-compra-form', ['presupuestoId' => request('presupuesto_id')])
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
