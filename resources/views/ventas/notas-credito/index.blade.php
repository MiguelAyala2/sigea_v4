@extends('adminlte::page')

@section('title', 'Notas de Crédito')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-undo mr-2"></i>
                Notas de Crédito
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Notas de Crédito</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.nota-credito-list')
@stop

@section('css')
    <style>
        .badge {
            font-size: 0.85em;
        }
    </style>
@stop
