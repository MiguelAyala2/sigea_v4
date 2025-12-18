@extends('adminlte::page')

@section('title', 'Detalle Remisión')

@section('content_header')
    <h1>Remisión: {{ $remision->numero }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Información General</h3>
            <div class="card-tools">
                @if($remision->estado === 'pendiente' || $remision->estado === 'parcial')
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalRecibir">
                        <i class="fas fa-check"></i> Registrar Recepción
                    </button>
                @endif
                @if($remision->estado !== 'anulada')
                    <form action="{{ route('compras.remisiones.anular', $remision) }}" method="POST" style="display: inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de anular esta remisión?')">
                            <i class="fas fa-ban"></i> Anular
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th>Número:</th>
                            <td>{{ $remision->numero }}</td>
                        </tr>
                        <tr>
                            <th>Fecha:</th>
                            <td>{{ $remision->fecha->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Proveedor:</th>
                            <td>{{ $remision->proveedor->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Guía Proveedor:</th>
                            <td>{{ $remision->numero_guia_proveedor ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Transportista:</th>
                            <td>{{ $remision->transportista ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($remision->estado === 'pendiente')
                                    <span class="badge badge-warning">Pendiente</span>
                                @elseif($remision->estado === 'recibida')
                                    <span class="badge badge-success">Recibida</span>
                                @elseif($remision->estado === 'parcial')
                                    <span class="badge badge-info">Parcial</span>
                                @else
                                    <span class="badge badge-danger">Anulada</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Placa Vehículo:</th>
                            <td>{{ $remision->placa_vehiculo ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Dirección Entrega:</th>
                            <td>{{ $remision->direccion_entrega ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Recibido por:</th>
                            <td>{{ $remision->recibidoPor->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha Recepción:</th>
                            <td>{{ $remision->fecha_recepcion ? $remision->fecha_recepcion->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @if($remision->observaciones)
                <div class="row">
                    <div class="col-md-12">
                        <h5>Observaciones:</h5>
                        <p>{{ $remision->observaciones }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalle de Productos</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Cantidad Enviada</th>
                        <th>Cantidad Recibida</th>
                        <th>Cantidad Rechazada</th>
                        <th>Pendiente</th>
                        <th>Unidad</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($remision->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre }}</td>
                            <td>{{ $detalle->descripcion }}</td>
                            <td>{{ number_format($detalle->cantidad_enviada, 2) }}</td>
                            <td>{{ number_format($detalle->cantidad_recibida, 2) }}</td>
                            <td>{{ number_format($detalle->cantidad_rechazada, 2) }}</td>
                            <td>{{ number_format($detalle->cantidad_pendiente, 2) }}</td>
                            <td>{{ $detalle->unidad_medida ?? 'UND' }}</td>
                            <td>{{ $detalle->observaciones ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('compras.remisiones.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <!-- Modal para registrar recepción -->
    <div class="modal fade" id="modalRecibir" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('compras.remisiones.recibir', $remision) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Recepción de Mercancía</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Enviado</th>
                                    <th>Recibido</th>
                                    <th>Rechazado</th>
                                    <th>Motivo Rechazo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($remision->detalles as $detalle)
                                    <tr>
                                        <td>{{ $detalle->producto->nombre }}</td>
                                        <td>{{ number_format($detalle->cantidad_enviada, 2) }}</td>
                                        <td>
                                            <input type="hidden" name="detalles[{{ $loop->index }}][id]" value="{{ $detalle->id }}">
                                            <input type="number" name="detalles[{{ $loop->index }}][cantidad_recibida]"
                                                   class="form-control" step="0.01" min="0"
                                                   value="{{ $detalle->cantidad_recibida }}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="detalles[{{ $loop->index }}][cantidad_rechazada]"
                                                   class="form-control" step="0.01" min="0"
                                                   value="{{ $detalle->cantidad_rechazada }}">
                                        </td>
                                        <td>
                                            <input type="text" name="detalles[{{ $loop->index }}][motivo_rechazo]"
                                                   class="form-control" value="{{ $detalle->motivo_rechazo }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar Recepción</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
