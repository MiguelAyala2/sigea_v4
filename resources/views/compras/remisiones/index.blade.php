@extends('adminlte::page')

@section('title', 'Remisiones')

@section('content_header')
    <h1>Remisiones</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Remisiones</h3>
            <div class="card-tools">
                <a href="{{ route('compras.remisiones.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nueva Remisión
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Destinatario</th>
                        <th>Transportista</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($remisiones as $remision)
                        <tr>
                            <td>
                                @if($remision->tipo === 'INTERNA')
                                    <span class="badge badge-primary">
                                        <i class="fas fa-building"></i> Interna
                                    </span>
                                @else
                                    <span class="badge badge-info">
                                        <i class="fas fa-user"></i> Externa
                                    </span>
                                @endif
                            </td>
                            <td>{{ $remision->numero }}</td>
                            <td>{{ $remision->fecha->format('d/m/Y') }}</td>
                            <td>
                                @if($remision->tipo === 'INTERNA')
                                    <strong>{{ $remision->sucursalDestino->nombre ?? 'N/A' }}</strong>
                                    @if($remision->depositoDestino)
                                        <br><small class="text-muted">Depósito: {{ $remision->depositoDestino->nombre }}</small>
                                    @endif
                                @else
                                    <strong>{{ $remision->cliente_nombre ?? 'N/A' }}</strong>
                                    @if($remision->cliente_ruc)
                                        <br><small class="text-muted">RUC/CI: {{ $remision->cliente_ruc }}</small>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $remision->transportista ?? 'N/A' }}</td>
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
                            <td>
                                <a href="{{ route('compras.remisiones.show', $remision) }}" class="btn btn-info btn-sm" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($remision->estado === 'pendiente')
                                    <a href="{{ route('compras.remisiones.edit', $remision) }}" class="btn btn-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay remisiones registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $remisiones->links() }}
        </div>
    </div>
@stop
