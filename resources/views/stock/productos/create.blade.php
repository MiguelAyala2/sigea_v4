@extends('layouts.app')

@section('subtitle', 'Stock')
@section('content_header_title', 'Productos')
@section('content_header_subtitle', 'Nuevo')

@section('content_body')
    @livewire('stock.productos.create')
@stop
