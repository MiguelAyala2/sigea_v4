@extends('adminlte::page')

@section('title', 'Registrar Pedido de Cliente')

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> Registrar Pedido de Cliente</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @livewire('ventas.pedido-cliente-form')
        </div>
    </div>
@stop

@section('css')
    <style>
        .position-relative {
            position: relative;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-ocultar mensajes de éxito después de 3 segundos
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 3000);
    </script>
@stop
