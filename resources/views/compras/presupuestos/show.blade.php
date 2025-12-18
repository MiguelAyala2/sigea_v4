@extends('adminlte::page')

@section('title', 'Ver Presupuesto')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Presupuesto {{ $presupuesto->numero_presupuesto }}</h1>
        <div>
            <a href="{{ route('compras.presupuestos.imprimir-pdf', $presupuesto->id) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-file-pdf"></i> Imprimir PDF
            </a>

            @if(in_array($presupuesto->estado, ['PENDIENTE', 'RECIBIDO', 'EN_EVALUACION']))
                <a href="{{ route('compras.presupuestos.edit', $presupuesto->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif

            @if(in_array($presupuesto->estado, ['PENDIENTE', 'RECIBIDO', 'EN_EVALUACION']))
                <form action="{{ route('compras.presupuestos.seleccionar', $presupuesto->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('¿Aprobar este presupuesto?')">
                        <i class="fas fa-check"></i> Aprobar
                    </button>
                </form>

                <button type="button" class="btn btn-danger" onclick="if(confirm('¿Rechazar este presupuesto?')) { document.getElementById('form-rechazar-{{ $presupuesto->id }}').submit(); }">
                    <i class="fas fa-times"></i> Rechazar
                </button>

                <form id="form-rechazar-{{ $presupuesto->id }}" action="{{ route('compras.presupuestos.seleccionar', $presupuesto->id) }}" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="rechazar" value="1">
                </form>
            @endif

            <a href="{{ route('compras.presupuestos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Información del Presupuesto -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del Presupuesto</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Número:</strong> {{ $presupuesto->numero_presupuesto }}</p>
                            <p><strong>Proveedor:</strong> {{ $presupuesto->proveedor->nombre_fantasia }}</p>
                            <p><small class="text-muted">{{ $presupuesto->proveedor->razon_social }} - RUC: {{ $presupuesto->proveedor->ruc }}</small></p>

                            @if($presupuesto->pedidoCompra)
                            <p>
                                <strong>Pedido de Compra:</strong>
                                <a href="{{ route('compras.pedidos.show', $presupuesto->pedido_compra_id) }}">
                                    {{ $presupuesto->pedidoCompra->numero_pedido }}
                                </a>
                            </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha Solicitud:</strong> {{ $presupuesto->fecha_solicitud->format('d/m/Y') }}</p>
                            @if($presupuesto->fecha_recepcion)
                            <p><strong>Fecha Recepción:</strong> {{ $presupuesto->fecha_recepcion->format('d/m/Y') }}</p>
                            @endif
                            @if($presupuesto->fecha_vencimiento)
                            <p><strong>Fecha Vencimiento:</strong> {{ $presupuesto->fecha_vencimiento->format('d/m/Y') }}</p>
                            @endif
                            <p><strong>Estado:</strong>
                                @switch($presupuesto->estado)
                                    @case('PENDIENTE')
                                        <span class="badge badge-warning">Pendiente</span>
                                        @break
                                    @case('RECIBIDO')
                                        <span class="badge badge-info">Recibido</span>
                                        @break
                                    @case('EN_EVALUACION')
                                        <span class="badge badge-primary">En Evaluación</span>
                                        @break
                                    @case('SELECCIONADO')
                                    @case('APROBADO')
                                        <span class="badge badge-success">Aprobado</span>
                                        @break
                                    @case('RECHAZADO')
                                        <span class="badge badge-danger">Rechazado</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $presupuesto->estado }}</span>
                                        @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Condiciones Comerciales</h3>
                </div>
                <div class="card-body">
                    <p><strong>Condición de Pago:</strong>
                        @switch($presupuesto->condicion_pago)
                            @case('CONTADO') Contado @break
                            @case('7_DIAS') 7 Días @break
                            @case('15_DIAS') 15 Días @break
                            @case('30_DIAS') 30 Días @break
                            @case('60_DIAS') 60 Días @break
                            @case('90_DIAS') 90 Días @break
                        @endswitch
                    </p>
                    <p><strong>Días de Entrega:</strong> {{ $presupuesto->dias_entrega }} días</p>
                    @if($presupuesto->descuento_general > 0)
                    <p><strong>Descuento General:</strong> ₲ {{ number_format($presupuesto->descuento_general, 0, ',', '.') }}</p>
                    @endif
                    @if($presupuesto->flete > 0)
                    <p><strong>Flete:</strong> ₲ {{ number_format($presupuesto->flete, 0, ',', '.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles del Presupuesto -->
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
                        @foreach($presupuesto->detalles as $detalle)
                        @php
                            $producto = DB::table('stock.PRODUCTOS')
                                ->leftJoin('stock.MARCAS', 'stock.PRODUCTOS.marca_id', '=', 'stock.MARCAS.id')
                                ->where('stock.PRODUCTOS.id', $detalle->producto_id)
                                ->select('stock.PRODUCTOS.*', 'stock.MARCAS.nombre as marca_nombre')
                                ->first();
                            $total += $detalle->total;
                        @endphp
                        <tr>
                            <td>{{ $producto->codigo ?? '-' }}</td>
                            <td>{{ $producto->nombre ?? 'Producto #' . $detalle->producto_id }}</td>
                            <td>{{ $producto->marca_nombre ?? ($detalle->marca_ofrecida ?? '-') }}</td>
                            <td class="text-right">{{ number_format($detalle->cantidad_cotizada, 2) }}</td>
                            <td class="text-right">₲ {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $detalle->iva_porcentaje }}%</td>
                            <td class="text-right">₲ {{ number_format($detalle->total, 0, ',', '.') }}</td>
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

    @if($presupuesto->observaciones)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Observaciones</h3>
        </div>
        <div class="card-body">
            {{ $presupuesto->observaciones }}
        </div>
    </div>
    @endif
@stop

@section('css')
@stop

@section('js')
@stop
