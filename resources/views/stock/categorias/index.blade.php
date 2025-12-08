@extends('layouts.app')

@section('subtitle', 'Stock')
@section('content_header_title', 'Categorías')
@section('content_header_subtitle', 'Listado')

@section('content_body')
    @livewire('stock.categorias.index')
@stop
