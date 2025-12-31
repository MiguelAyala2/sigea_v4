@extends('adminlte::page')

@section('title', 'Notas de Débito')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-plus-square mr-2"></i>
                Notas de Débito
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Notas de Débito</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.nota-debito-list')
@stop

@section('css')
    <style>
        .badge {
            font-size: 0.85em;
        }
    </style>
@stop
