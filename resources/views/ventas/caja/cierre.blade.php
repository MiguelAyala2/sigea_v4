@extends('adminlte::page')

@section('title', 'Cierre de Caja')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-lock mr-2"></i>
                Cierre de Caja
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item"><a href="#">Caja</a></li>
                <li class="breadcrumb-item active">Cierre</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.caja.cierre')
@stop

@section('css')
    <style>
        .card-danger.card-outline {
            border-top: 3px solid #dc3545;
        }
        .bg-success input,
        .bg-danger input {
            color: white !important;
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script>
        // Livewire events
        window.addEventListener('livewire:init', () => {
            Livewire.on('cajaCerrada', () => {
                console.log('Caja cerrada exitosamente');
            });
        });
    </script>
@stop
