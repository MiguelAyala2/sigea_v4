@extends('layouts.app')

@section('subtitle', 'Stock')
@section('content_header_title', 'Productos')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    @livewire('stock.productos.edit', ['producto' => $producto])
@stop
