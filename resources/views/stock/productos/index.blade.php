@extends('layouts.app')

@section('subtitle', 'Stock')
@section('content_header_title', 'Productos')
@section('content_header_subtitle', 'Listado')

@section('content_body')
    @livewire('stock.productos.index')
@stop
