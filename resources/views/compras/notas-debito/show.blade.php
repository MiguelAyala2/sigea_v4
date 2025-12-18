@extends('adminlte::page')

@section('title', 'Detalle Nota de Débito')

@section('content_header')
    <h1>Nota de Débito: {{ $notaDebito->numero }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Información General</h3>
            <div class="card-tools">
                @if($notaDebito->estado === 'borrador')
                    <a href="{{ route('compras.notas-debito.edit', $notaDebito) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <form action="{{ route('compras.notas-debito.aplicar', $notaDebito) }}" method="POST" style="display: inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Está seguro de aplicar esta nota de débito?')">
                            <i class="fas fa-check"></i> Aplicar
                        </button>
                    </form>
                @endif
                @if($notaDebito->estado !== 'anulada')
                    <form action="{{ route('compras.notas-debito.anular', $notaDebito) }}" method="POST" style="display: inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de anular esta nota de débito?')">
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
                            <td>{{ $notaDebito->numero }}</td>
                        </tr>
                        <tr>
                            <th>Fecha:</th>
                            <td>{{ $notaDebito->fecha->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Proveedor:</th>
                            <td>{{ $notaDebito->proveedor->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Factura Afectada:</th>
                            <td>{{ $notaDebito->numero_factura_afectada ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($notaDebito->estado === 'borrador')
                                    <span class="badge badge-secondary">Borrador</span>
                                @elseif($notaDebito->estado === 'aplicada')
                                    <span class="badge badge-success">Aplicada</span>
                                @else
                                    <span class="badge badge-danger">Anulada</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Creado por:</th>
                            <td>{{ $notaDebito->creador->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha Creación:</th>
                            <td>{{ $notaDebito->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h5>Motivo:</h5>
                    <p>{{ $notaDebito->motivo }}</p>
                </div>
            </div>
            @if($notaDebito->observaciones)
                <div class="row">
                    <div class="col-md-12">
                        <h5>Observaciones:</h5>
                        <p>{{ $notaDebito->observaciones }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalle de Items</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Descuento</th>
                        <th>Subtotal</th>
                        <th>Impuesto</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notaDebito->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre ?? 'N/A' }}</td>
                            <td>{{ $detalle->descripcion }}</td>
                            <td>{{ number_format($detalle->cantidad, 2) }}</td>
                            <td>$ {{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td>$ {{ number_format($detalle->descuento, 2) }}</td>
                            <td>$ {{ number_format($detalle->subtotal, 2) }}</td>
                            <td>$ {{ number_format($detalle->impuesto, 2) }}</td>
                            <td>$ {{ number_format($detalle->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right">Totales:</th>
                        <th>$ {{ number_format($notaDebito->subtotal, 2) }}</th>
                        <th>$ {{ number_format($notaDebito->impuesto, 2) }}</th>
                        <th>$ {{ number_format($notaDebito->total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('compras.notas-debito.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
@stop
