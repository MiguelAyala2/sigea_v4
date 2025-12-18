@extends('adminlte::page')

@section('title', 'Notas de Crédito')

@section('content_header')
    <h1>Notas de Crédito</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Notas de Crédito</h3>
            <div class="card-tools">
                <a href="{{ route('compras.notas-credito.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nueva Nota de Crédito
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Factura Afectada</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notasCredito as $nota)
                        <tr>
                            <td>{{ $nota->numero }}</td>
                            <td>{{ $nota->fecha->format('d/m/Y') }}</td>
                            <td>{{ $nota->proveedor->nombre }}</td>
                            <td>{{ $nota->numero_factura_afectada ?? 'N/A' }}</td>
                            <td>$ {{ number_format($nota->total, 2) }}</td>
                            <td>
                                @if($nota->estado === 'borrador')
                                    <span class="badge badge-secondary">Borrador</span>
                                @elseif($nota->estado === 'aplicada')
                                    <span class="badge badge-success">Aplicada</span>
                                @else
                                    <span class="badge badge-danger">Anulada</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('compras.notas-credito.show', $nota) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($nota->estado === 'borrador')
                                    <a href="{{ route('compras.notas-credito.edit', $nota) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay notas de crédito registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $notasCredito->links() }}
        </div>
    </div>
@stop
