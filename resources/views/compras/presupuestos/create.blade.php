@extends('adminlte::page')

@section('title', 'Nuevo Presupuesto')

@section('content_header')
    <h1>Nuevo Presupuesto de Proveedor</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @livewire('compras.presupuesto-form')
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
