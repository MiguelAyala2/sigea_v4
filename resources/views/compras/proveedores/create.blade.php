@extends('adminlte::page')

@section('title', 'Nuevo Proveedor')

@section('content_header')
    <h1>Nuevo Proveedor</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <livewire:compras.proveedor-form />
        </div>
    </div>
@stop
