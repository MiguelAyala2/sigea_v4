@extends('adminlte::page')

@section('title', 'Editar Proveedor')

@section('content_header')
    <h1>Editar Proveedor</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <livewire:compras.proveedor-form :proveedor="$proveedor" />
        </div>
    </div>
@stop
