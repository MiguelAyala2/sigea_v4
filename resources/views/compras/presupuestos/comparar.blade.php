@extends('adminlte::page')

@section('title', 'Comparar Presupuestos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Comparación de Presupuestos</h1>
        <a href="{{ route('compras.presupuestos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <!-- Información del Pedido -->
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Pedido de Compra: {{ $pedido->numero_pedido }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Tipo:</strong> {{ $pedido->tipo_pedido }}</p>
                    <p><strong>Fecha:</strong> {{ $pedido->fecha_pedido->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Estado:</strong>
                        <span class="badge badge-success">{{ $pedido->estado }}</span>
                    </p>
                    <p><strong>Presupuestos Recibidos:</strong> {{ count($presupuestos) }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total Estimado:</strong> ₲ {{ number_format($pedido->total_estimado, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(count($presupuestos) > 0)
    <!-- Tabla Comparativa -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Comparación de Ofertas</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Proveedor</th>
                            @foreach($presupuestos as $presupuesto)
                            <th class="text-center">
                                {{ $presupuesto->proveedor->nombre_fantasia }}
                                <br>
                                <small>{{ $presupuesto->numero_presupuesto }}</small>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Estado</strong></td>
                            @foreach($presupuestos as $presupuesto)
                            <td class="text-center">
                                @switch($presupuesto->estado)
                                    @case('PENDIENTE')
                                        <span class="badge badge-warning">Pendiente</span>
                                        @break
                                    @case('RECIBIDO')
                                        <span class="badge badge-info">Recibido</span>
                                        @break
                                    @case('SELECCIONADO')
                                        <span class="badge badge-success">SELECCIONADO</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $presupuesto->estado }}</span>
                                @endswitch
                            </td>
                            @endforeach
                        </tr>
                        <tr class="table-success">
                            <td><strong>Total</strong></td>
                            @foreach($presupuestos as $presupuesto)
                            <td class="text-center">
                                <strong class="{{ $presupuesto->es_mejor_precio ? 'text-success' : '' }}">
                                    ₲ {{ number_format($presupuesto->total, 0, ',', '.') }}
                                    @if($presupuesto->es_mejor_precio)
                                        <i class="fas fa-star text-warning" title="Mejor Precio"></i>
                                    @endif
                                </strong>
                            </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>Condición de Pago</strong></td>
                            @foreach($presupuestos as $presupuesto)
                            <td class="text-center {{ $presupuesto->es_mejor_plazo ? 'text-success' : '' }}">
                                @switch($presupuesto->condicion_pago)
                                    @case('CONTADO') Contado @break
                                    @case('7_DIAS') 7 Días @break
                                    @case('15_DIAS') 15 Días @break
                                    @case('30_DIAS') 30 Días @break
                                    @case('60_DIAS') 60 Días @break
                                    @case('90_DIAS') 90 Días @break
                                @endswitch
                                @if($presupuesto->es_mejor_plazo)
                                    <i class="fas fa-star text-warning" title="Mejor Plazo"></i>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>Días de Entrega</strong></td>
                            @foreach($presupuestos as $presupuesto)
                            <td class="text-center">{{ $presupuesto->dias_entrega }} días</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>Descuento</strong></td>
                            @foreach($presupuestos as $presupuesto)
                            <td class="text-center">₲ {{ number_format($presupuesto->descuento_general, 0, ',', '.') }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>Flete</strong></td>
                            @foreach($presupuestos as $presupuesto)
                            <td class="text-center">₲ {{ number_format($presupuesto->flete, 0, ',', '.') }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>Acciones</strong></td>
                            @foreach($presupuestos as $presupuesto)
                            <td class="text-center">
                                <a href="{{ route('compras.presupuestos.show', $presupuesto->id) }}" class="btn btn-sm btn-info" title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($presupuesto->estado !== 'SELECCIONADO')
                                <form action="{{ route('compras.presupuestos.seleccionar', $presupuesto->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Seleccionar" onclick="return confirm('¿Seleccionar este presupuesto?')">
                                        <i class="fas fa-check"></i> Seleccionar
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Detalle por Producto -->
            <h4 class="mt-4">Comparación por Producto</h4>
            @foreach($pedido->detalles as $detallePedido)
                @php
                    $producto = DB::table('stock.PRODUCTOS')->where('id', $detallePedido->producto_id)->first();
                @endphp
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <strong>{{ $producto->nombre ?? 'Producto #' . $detallePedido->producto_id }}</strong>
                        <small class="text-muted">(Código: {{ $producto->codigo ?? '-' }})</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Proveedor</th>
                                        <th class="text-right">Cantidad</th>
                                        <th class="text-right">Precio Unit.</th>
                                        <th class="text-center">IVA</th>
                                        <th class="text-right">Subtotal</th>
                                        <th>Marca</th>
                                        <th class="text-center">Días Entrega</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($presupuestos as $presupuesto)
                                        @php
                                            $detallePresupuesto = $presupuesto->detalles->where('producto_id', $detallePedido->producto_id)->first();
                                        @endphp
                                        @if($detallePresupuesto)
                                        <tr>
                                            <td>{{ $presupuesto->proveedor->nombre_fantasia }}</td>
                                            <td class="text-right">{{ number_format($detallePresupuesto->cantidad_cotizada, 2) }}</td>
                                            <td class="text-right">₲ {{ number_format($detallePresupuesto->precio_unitario, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $detallePresupuesto->iva_porcentaje }}%</td>
                                            <td class="text-right">₲ {{ number_format($detallePresupuesto->total, 0, ',', '.') }}</td>
                                            <td>{{ $detallePresupuesto->marca_ofrecida ?? '-' }}</td>
                                            <td class="text-center">{{ $detallePresupuesto->dias_entrega_item ?? '-' }}</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td>{{ $presupuesto->proveedor->nombre_fantasia }}</td>
                                            <td colspan="6" class="text-center text-muted">No cotizado</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> No hay presupuestos para este pedido de compra.
        <a href="{{ route('compras.presupuestos.create', ['pedido_compra_id' => $pedido->id]) }}" class="btn btn-sm btn-primary ml-2">
            <i class="fas fa-plus"></i> Solicitar Presupuesto
        </a>
    </div>
    @endif
@stop

@section('css')
<style>
.table th {
    vertical-align: middle;
}
</style>
@stop

@section('js')
@stop
