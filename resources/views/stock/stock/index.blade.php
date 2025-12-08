@extends('adminlte::page')

@section('title', 'Gestión de Stock')

@section('content_header')
    <h1>
        <i class="fas fa-boxes"></i> Gestión de Stock
    </h1>
@stop

@section('content')
    @livewire('stock.stock-index')
@stop
