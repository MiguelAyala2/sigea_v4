@extends('adminlte::page')

@section('title', 'Libro de Ventas IVA')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-book mr-2"></i>
                Libro de Ventas IVA
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Libro de Ventas</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('ventas.libro-ventas')
@stop

@section('css')
    <style>
        .card-primary.card-outline {
            border-top: 3px solid #007bff;
        }
        .info-box-number {
            font-size: 1.2rem;
        }
        .table td, .table th {
            vertical-align: middle;
        }
    </style>
@stop

@section('js')
    <script>
        // Livewire events
        window.addEventListener('livewire:init', () => {
            Livewire.on('libroVentasActualizado', () => {
                console.log('Libro de ventas actualizado');
            });
        });
    </script>
@stop
