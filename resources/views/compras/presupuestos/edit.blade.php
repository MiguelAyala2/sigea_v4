@extends('adminlte::page')

@section('title', 'Editar Presupuesto')

@section('content_header')
    <h1>Editar Presupuesto #{{ $presupuesto->numero_presupuesto }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @livewire('compras.presupuesto-form', ['presupuestoId' => $presupuesto->id])
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
