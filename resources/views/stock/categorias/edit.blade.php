@extends('layouts.app')

@section('subtitle', 'Stock')
@section('content_header_title', 'Categorías')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    @livewire('stock.categorias.edit', ['categoria' => $categoria])
@stop
