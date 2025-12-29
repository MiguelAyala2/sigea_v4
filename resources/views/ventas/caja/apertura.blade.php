@extends('adminlte::page')

@section('title', 'Apertura de Caja')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-unlock mr-2"></i>
                Apertura de Caja
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item"><a href="#">Caja</a></li>
                <li class="breadcrumb-item active">Apertura</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.caja.apertura')
@stop

@section('css')
    <style>
        .card-primary.card-outline {
            border-top: 3px solid #007bff;
        }
    </style>
@stop

@section('js')
    <script>
        // Livewire events
        window.addEventListener('livewire:init', () => {
            Livewire.on('cajaAbierta', () => {
                console.log('Caja abierta exitosamente');
            });
        });
    </script>
@stop
