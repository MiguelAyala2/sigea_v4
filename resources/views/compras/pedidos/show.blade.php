@extends('adminlte::page')

@section('title', 'Detalle Pedido de Compra')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Pedido de Compra - {{ $pedido->numero_pedido }}</h1>
        <div>
            <a href="{{ route('compras.pedidos.imprimir-pdf', $pedido->id) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-file-pdf"></i> Imprimir PDF
            </a>

            @if(in_array($pedido->estado, ['PENDIENTE', 'BORRADOR', 'PENDIENTE_APROBACION', 'EN_COTIZACION']))
                <a href="{{ route('compras.pedidos.edit', $pedido->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif

            @if(in_array($pedido->estado, ['PENDIENTE', 'BORRADOR', 'PENDIENTE_APROBACION', 'EN_COTIZACION']))
                <form action="{{ route('compras.pedidos.aprobar', $pedido->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('¿Aprobar este pedido?')">
                        <i class="fas fa-check"></i> Aprobar
                    </button>
                </form>

                <form action="{{ route('compras.pedidos.rechazar', $pedido->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Rechazar este pedido?')">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                </form>
            @endif

            <a href="{{ route('compras.pedidos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Información General -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información General</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Número:</strong> {{ $pedido->numero_pedido }}</p>
                            <p><strong>Fecha Pedido:</strong> {{ $pedido->fecha_pedido->format('d/m/Y') }}</p>
                            <p><strong>Fecha Necesaria:</strong> {{ $pedido->fecha_necesaria?->format('d/m/Y') ?? 'N/A' }}</p>
                            <p><strong>Tipo:</strong> {{ $pedido->tipo_pedido }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Estado:</strong>
                                <span class="badge badge-{{ $pedido->estado == 'APROBADO' ? 'success' : ($pedido->estado == 'RECHAZADO' ? 'danger' : 'warning') }}">
                                    {{ $pedido->estado }}
                                </span>
                            </p>
                            <p><strong>Prioridad:</strong>
                                <span class="badge badge-{{ $pedido->prioridad == 'CRITICA' ? 'danger' : ($pedido->prioridad == 'URGENTE' ? 'warning' : 'info') }}">
                                    {{ $pedido->prioridad }}
                                </span>
                            </p>
                            <p><strong>Solicitante:</strong> {{ $pedido->usuarioSolicitante->name }}</p>
                            <p><strong>Total Estimado:</strong> ₲ {{ number_format($pedido->total_estimado, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if($pedido->justificacion)
                        <hr>
                        <p><strong>Justificación:</strong></p>
                        <p>{{ $pedido->justificacion }}</p>
                    @endif

                    @if($pedido->observaciones)
                        <p><strong>Observaciones:</strong></p>
                        <p>{{ $pedido->observaciones }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estado del Pedido</h3>
                </div>
                <div class="card-body">
                    <p><strong>% Ordenado:</strong></p>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $pedido->porcentaje_ordenado }}%">
                            {{ number_format($pedido->porcentaje_ordenado, 1) }}%
                        </div>
                    </div>

                    <p><strong>Presupuestos:</strong> {{ $pedido->presupuestos->count() }}</p>
                    <p><strong>Órdenes Generadas:</strong> {{ $pedido->ordenesCompra->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de Productos -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalle de Productos</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Marca</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">Precio Unit.</th>
                            <th class="text-center">IVA %</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                        @endphp
                        @foreach($pedido->detalles as $detalle)
                        @php
                            $producto = DB::table('stock.PRODUCTOS')
                                ->leftJoin('stock.MARCAS', 'stock.PRODUCTOS.marca_id', '=', 'stock.MARCAS.id')
                                ->where('stock.PRODUCTOS.id', $detalle->producto_id)
                                ->select('stock.PRODUCTOS.*', 'stock.MARCAS.nombre as marca_nombre')
                                ->first();
                            $total += $detalle->subtotal_estimado;
                        @endphp
                        <tr>
                            <td>{{ $producto->codigo ?? '-' }}</td>
                            <td>{{ $producto->nombre ?? 'Producto #' . $detalle->producto_id }}</td>
                            <td>{{ $producto->marca_nombre ?? ($detalle->marca_solicitada ?? '-') }}</td>
                            <td class="text-right">{{ number_format($detalle->cantidad_solicitada, 2) }}</td>
                            <td class="text-right">₲ {{ number_format($detalle->precio_estimado, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $detalle->iva_porcentaje ?? 10 }}%</td>
                            <td class="text-right">₲ {{ number_format($detalle->subtotal_estimado, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="6" class="text-right">TOTAL:</td>
                            <td class="text-right">₲ {{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
