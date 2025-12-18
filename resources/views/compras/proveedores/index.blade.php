@extends('adminlte::page')

@section('title', 'Proveedores')

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1>Proveedores</h1>
        </div>
        <div class="col-md-6 text-right">
            @can('Proveedores Crear')
                <a href="{{ route('compras.proveedores.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Proveedor
                </a>
            @endcan
        </div>
    </div>
@stop

@section('content')
    <livewire:compras.proveedor-table />
@stop

@section('css')
@stop

@section('js')
@stop
