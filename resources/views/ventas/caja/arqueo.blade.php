@extends('adminlte::page')

@section('title', 'Arqueo de Caja')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-coins mr-2"></i>
                Arqueo de Caja
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item"><a href="#">Caja</a></li>
                <li class="breadcrumb-item active">Arqueo</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.caja.arqueo')
@stop

@section('css')
    <style>
        .card-warning.card-outline {
            border-top: 3px solid #ffc107;
        }
        .card-primary .card-header,
        .card-success .card-header {
            font-weight: bold;
        }
        input[type="number"].text-center {
            text-align: center;
        }
    </style>
@stop

@section('js')
    <script>
        // Livewire events
        window.addEventListener('livewire:init', () => {
            Livewire.on('arqueoGuardado', () => {
                console.log('Arqueo guardado exitosamente');
            });
        });
    </script>
@stop
