@extends('adminlte::page')

@section('title', 'Movimientos de Caja')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-exchange-alt mr-2"></i>
                Movimientos de Caja
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item"><a href="#">Caja</a></li>
                <li class="breadcrumb-item active">Movimientos</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.caja.movimientos')
@stop

@section('css')
    <style>
        .card-success.card-outline {
            border-top: 3px solid #28a745;
        }
        .info-box {
            min-height: 90px;
        }
    </style>
@stop

@section('js')
    <script>
        // Livewire events
        window.addEventListener('livewire:init', () => {
            Livewire.on('movimientoRegistrado', () => {
                console.log('Movimiento registrado exitosamente');
            });
        });
    </script>
@stop
