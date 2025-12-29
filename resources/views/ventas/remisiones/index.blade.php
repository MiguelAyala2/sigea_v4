@extends('adminlte::page')

@section('title', 'Remisiones')

@section('content_header')
    <h1><i class="fas fa-truck"></i> Remisiones</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Listado de Remisiones</h3>
            <div class="card-tools">
                <a href="{{ route('ventas.remisiones.crear') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nueva Remisión
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($remisiones->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay remisiones registradas.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Número</th>
                                <th>Fecha Emisión</th>
                                <th>Cliente</th>
                                <th>Factura Origen</th>
                                <th>Sucursal</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($remisiones as $remision)
                                <tr>
                                    <td>
                                        <a href="{{ route('ventas.remisiones.show', $remision) }}" class="text-primary">
                                            <strong>{{ $remision->numero_remision }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ $remision->fecha_emision->format('d/m/Y') }}</td>
                                    <td>{{ $remision->cliente->nombre_razon_social ?? 'N/A' }}</td>
                                    <td>
                                        @if($remision->factura)
                                            <a href="{{ route('ventas.facturas.show', $remision->factura) }}" target="_blank">
                                                {{ $remision->factura->numero_factura }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $remision->sucursal->nombre ?? 'N/A' }}</td>
                                    <td>
                                        @switch($remision->estado)
                                            @case('BORRADOR')
                                                <span class="badge badge-secondary">Borrador</span>
                                                @break
                                            @case('EMITIDA')
                                                <span class="badge badge-primary">Emitida</span>
                                                @break
                                            @case('EN_TRANSITO')
                                                <span class="badge badge-info">En Tránsito</span>
                                                @break
                                            @case('ENTREGADA')
                                                <span class="badge badge-success">Entregada</span>
                                                @break
                                            @case('ANULADA')
                                                <span class="badge badge-danger">Anulada</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('ventas.remisiones.show', $remision) }}"
                                           class="btn btn-info btn-xs"
                                           title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('ventas.remisiones.pdf', $remision) }}"
                                           class="btn btn-danger btn-xs"
                                           title="PDF"
                                           target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $remisiones->links() }}
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <style>
        .table-sm td, .table-sm th {
            font-size: 0.875rem;
        }
    </style>
@stop
