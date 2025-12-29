@extends("adminlte::page")

@section("title", "Nueva Factura")

@section("content_header")
    <h1>
        <i class="fas fa-file-invoice-dollar mr-2"></i>
        Nueva Factura
    </h1>
@stop

@section("content")
    @livewire('ventas.factura-form', [
        'facturaId' => null,
        'pedidoId' => $pedidoId ?? null,
        'cotizacionId' => $cotizacionId ?? null
    ])
@stop
