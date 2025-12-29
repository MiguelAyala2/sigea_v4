@extends('adminlte::page')

@section('title', 'Recaudaciones a Depositar')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-hand-holding-usd mr-2"></i>
                Recaudaciones a Depositar
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item"><a href="#">Caja</a></li>
                <li class="breadcrumb-item active">Recaudaciones</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.caja.recaudaciones')
@stop

@section('css')
    <style>
        .card-info.card-outline {
            border-top: 3px solid #17a2b8;
        }
        .bg-warning {
            background-color: #fff3cd !important;
        }
        .badge {
            font-size: 0.9em;
        }
    </style>
@stop

@section('js')
    <script>
        // Livewire events
        window.addEventListener('livewire:init', () => {
            Livewire.on('recaudacionRegistrada', () => {
                console.log('Recaudación registrada exitosamente');
            });

            Livewire.on('recaudacionDepositada', () => {
                console.log('Recaudación marcada como depositada');
            });
        });
    </script>
@stop
