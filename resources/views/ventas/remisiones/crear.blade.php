@extends('adminlte::page')

@section('title', 'Nueva Remisión')

@section('content_header')
    <h1><i class="fas fa-truck"></i> Nueva Remisión</h1>
@stop

@section('content')
    @livewire('ventas.remision-form')
@stop

@section('css')
    <style>
        .table-sm td, .table-sm th {
            font-size: 0.875rem;
        }

        /* Estilos para el autocompletado de clientes */
        .list-group-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .list-group-item:active {
            background-color: #e9ecef;
        }

        /* Animación suave para los resultados */
        .list-group {
            animation: slideDown 0.2s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mejorar visualización del cliente seleccionado */
        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
@stop
