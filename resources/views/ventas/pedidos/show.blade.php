@extends('adminlte::page')

@section('title', 'Detalle del Pedido')

@section('content_header')
    <h1><i class="fas fa-file-invoice"></i> Detalle del Pedido {{ $pedido->numero_pedido }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Información del Pedido -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                    <div class="card-tools">
                        @switch($pedido->estado)
                            @case('BORRADOR')
                                <span class="badge badge-secondary badge-lg">Borrador</span>
                                @break
                            @case('PENDIENTE')
                                <span class="badge badge-warning badge-lg">Pendiente</span>
                                @break
                            @case('CONFIRMADO')
                                <span class="badge badge-info badge-lg">Confirmado</span>
                                @break
                            @case('EN_PREPARACION')
                                <span class="badge badge-primary badge-lg">En Preparación</span>
                                @break
                            @case('LISTO_ENTREGAR')
                                <span class="badge badge-success badge-lg">Listo para Entregar</span>
                                @break
                            @case('PARCIALMENTE_ENTREGADO')
                                <span class="badge badge-warning badge-lg">Parcialmente Entregado</span>
                                @break
                            @case('COMPLETAMENTE_ENTREGADO')
                                <span class="badge badge-success badge-lg">Completamente Entregado</span>
                                @break
                            @case('FACTURADO')
                                <span class="badge badge-success badge-lg">Facturado</span>
                                @break
                            @case('CANCELADO')
                                <span class="badge badge-danger badge-lg">Cancelado</span>
                                @break
                            @case('ANULADO')
                                <span class="badge badge-dark badge-lg">Anulado</span>
                                @break
                            @default
                                <span class="badge badge-secondary badge-lg">{{ $pedido->estado }}</span>
                        @endswitch
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">N° Pedido:</th>
                                    <td><strong>{{ $pedido->numero_pedido }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Fecha Pedido:</th>
                                    <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Cliente:</th>
                                    <td>{{ $pedido->cliente->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Documento:</th>
                                    <td>{{ $pedido->cliente->documento ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Tipo de Entrega:</th>
                                    <td>
                                        @switch($pedido->tipo_entrega)
                                            @case('RETIRO_LOCAL')
                                                Retiro en Local
                                                @break
                                            @case('DELIVERY')
                                                Delivery
                                                @break
                                            @case('ENVIO_TRANSPORTE')
                                                Envío por Transporte
                                                @break
                                            @default
                                                {{ $pedido->tipo_entrega }}
                                        @endswitch
                                    </td>
                                </tr>
                                @if($pedido->direccion_entrega)
                                <tr>
                                    <th>Dirección de Entrega:</th>
                                    <td>{{ $pedido->direccion_entrega }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Vendedor:</th>
                                    <td>{{ $pedido->vendedor->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Sucursal:</th>
                                    <td>{{ $pedido->sucursal_id }}</td>
                                </tr>
                                <tr>
                                    <th>Depósito:</th>
                                    <td>{{ $pedido->deposito_id }}</td>
                                </tr>
                                <tr>
                                    <th>Registrado por:</th>
                                    <td>{{ $pedido->creador->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha de Registro:</th>
                                    <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($pedido->observaciones)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <strong>Observaciones:</strong>
                            <p>{{ $pedido->observaciones }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Detalle de Productos -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list"></i> Detalle de Productos</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">Precio Unit.</th>
                                <th class="text-center">IVA %</th>
                                <th class="text-right">Subtotal</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedido->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->codigo ?? 'N/A' }}</td>
                                <td>{{ $detalle->producto->nombre ?? 'Producto #' . $detalle->producto_id }}</td>
                                <td class="text-right">{{ number_format($detalle->cantidad_solicitada, 2) }}</td>
                                <td class="text-right">₲ {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $detalle->iva_porcentaje }}%</td>
                                <td class="text-right">₲ {{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                                <td class="text-right">₲ {{ number_format($detalle->total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="6" class="text-right"><strong>SUBTOTAL:</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($pedido->subtotal, 0, ',', '.') }}</strong></td>
                            </tr>
                            @if($pedido->iva_10 > 0)
                            <tr>
                                <td colspan="6" class="text-right"><small class="text-muted">IVA 10% Incluido:</small></td>
                                <td class="text-right"><small class="text-muted">₲ {{ number_format($pedido->iva_10, 0, ',', '.') }}</small></td>
                            </tr>
                            @endif
                            @if($pedido->iva_5 > 0)
                            <tr>
                                <td colspan="6" class="text-right"><small class="text-muted">IVA 5% Incluido:</small></td>
                                <td class="text-right"><small class="text-muted">₲ {{ number_format($pedido->iva_5, 0, ',', '.') }}</small></td>
                            </tr>
                            @endif
                            <tr class="table-success">
                                <td colspan="6" class="text-right"><strong>TOTAL GENERAL:</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($pedido->total, 0, ',', '.') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('ventas.pedidos.historial') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al Historial
                            </a>

                            @if($pedido->estado === 'BORRADOR')
                                <a href="{{ route('ventas.pedidos.edit', $pedido) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>

                                <form action="{{ route('ventas.pedidos.confirmar', $pedido) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('¿Confirmar este pedido?')">
                                        <i class="fas fa-check"></i> Confirmar Pedido
                                    </button>
                                </form>

                                <form action="{{ route('ventas.pedidos.cancelar', $pedido) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Cancelar este pedido?')">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </form>
                            @endif

                            @if($pedido->estado === 'CONFIRMADO')
                                <form action="{{ route('ventas.pedidos.preparar', $pedido) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-box"></i> Pasar a Preparación
                                    </button>
                                </form>

                                <form action="{{ route('ventas.pedidos.cancelar', $pedido) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Cancelar este pedido?')">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </form>
                            @endif

                            @if($pedido->estado === 'EN_PREPARACION')
                                <form action="{{ route('ventas.pedidos.listoEntregar', $pedido) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle"></i> Marcar Listo para Entregar
                                    </button>
                                </form>
                            @endif

                            @if(in_array($pedido->estado, ['LISTO_ENTREGAR', 'COMPLETAMENTE_ENTREGADO']))
                                <button type="button" class="btn btn-success" disabled>
                                    <i class="fas fa-truck"></i> Generar Remisión (Próximamente)
                                </button>
                            @endif

                            @if($pedido->estado !== 'ANULADO' && $pedido->estado !== 'FACTURADO')
                                <form action="{{ route('ventas.pedidos.anular', $pedido) }}" method="POST" style="display: inline-block; float: right;">
                                    @csrf
                                    <button type="submit" class="btn btn-dark" onclick="return confirm('¿ANULAR este pedido? Esta acción no se puede deshacer.')">
                                        <i class="fas fa-ban"></i> Anular Pedido
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .badge-lg {
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
        }
    </style>
@stop
