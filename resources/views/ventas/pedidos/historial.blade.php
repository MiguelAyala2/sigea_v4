@extends('adminlte::page')

@section('title', 'Historial de Pedidos')

@section('content_header')
    <h1><i class="fas fa-list"></i> Historial de Pedidos de Clientes</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('ventas.pedidos.registrar') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Pedido
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if($pedidos->count() > 0)
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>N° Pedido</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                        <tr>
                            <td>{{ $pedido->numero_pedido }}</td>
                            <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                            <td>{{ $pedido->cliente->nombre ?? 'N/A' }}</td>
                            <td>₲ {{ number_format($pedido->total, 0, ',', '.') }}</td>
                            <td>
                                @switch($pedido->estado)
                                    @case('BORRADOR')
                                        <span class="badge badge-secondary">Borrador</span>
                                        @break
                                    @case('PENDIENTE')
                                        <span class="badge badge-warning">Pendiente</span>
                                        @break
                                    @case('CONFIRMADO')
                                        <span class="badge badge-info">Confirmado</span>
                                        @break
                                    @case('EN_PREPARACION')
                                        <span class="badge badge-primary">En Preparación</span>
                                        @break
                                    @case('LISTO_ENTREGAR')
                                        <span class="badge badge-success">Listo para Entregar</span>
                                        @break
                                    @case('COMPLETAMENTE_ENTREGADO')
                                        <span class="badge badge-success">Entregado</span>
                                        @break
                                    @case('CANCELADO')
                                        <span class="badge badge-danger">Cancelado</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $pedido->estado }}</span>
                                @endswitch
                            </td>
                            <td>
                                <a href="{{ route('ventas.pedidos.show', $pedido) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($pedido->estado === 'BORRADOR')
                                    <a href="{{ route('ventas.pedidos.edit', $pedido) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $pedidos->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay pedidos registrados aún.
                </div>
            @endif
        </div>
    </div>
@stop

