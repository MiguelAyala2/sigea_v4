@extends('adminlte::page')

@section('title', 'Orden de Compra')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Orden de Compra #{{ $orden->numero_orden }}</h1>
        <div>
            <a href="{{ route('compras.ordenes.imprimir-pdf', $orden->id) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-file-pdf"></i> Imprimir PDF
            </a>

            @if(in_array($orden->estado, ['PENDIENTE', 'BORRADOR']))
                <a href="{{ route('compras.ordenes.edit', $orden->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif

            @if(in_array($orden->estado, ['PENDIENTE', 'BORRADOR']))
                <form action="{{ route('compras.ordenes.emitir', $orden->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('¿Aprobar esta orden de compra?')">
                        <i class="fas fa-check"></i> Aprobar
                    </button>
                </form>

                <form action="{{ route('compras.ordenes.cancelar', $orden->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Rechazar esta orden de compra?')">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                </form>
            @endif

            <a href="{{ route('compras.ordenes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Información de la Orden -->
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Información General</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Número de Orden:</strong> {{ $orden->numero_orden }}</p>
                    <p><strong>Fecha de Orden:</strong> {{ $orden->fecha_orden->format('d/m/Y') }}</p>
                    <p><strong>Fecha Entrega Esperada:</strong> {{ $orden->fecha_entrega_esperada ? $orden->fecha_entrega_esperada->format('d/m/Y') : '-' }}</p>
                    <p><strong>Estado:</strong>
                        @switch($orden->estado)
                            @case('PENDIENTE')
                            @case('BORRADOR')
                                <span class="badge badge-warning">Pendiente</span>
                                @break
                            @case('APROBADO')
                            @case('EMITIDA')
                                <span class="badge badge-success">Aprobado</span>
                                @break
                            @case('RECHAZADO')
                            @case('CANCELADA')
                                <span class="badge badge-danger">Rechazado</span>
                                @break
                            @default
                                <span class="badge badge-secondary">{{ $orden->estado }}</span>
                                @break
                        @endswitch
                    </p>
                </div>
                <div class="col-md-4">
                    <p><strong>Proveedor:</strong> {{ $orden->proveedor->nombre_fantasia ?? '-' }}</p>
                    <p><strong>RUC:</strong> {{ $orden->proveedor->ruc ?? '-' }}</p>
                    <p><strong>Condición de Pago:</strong>
                        @switch($orden->condicion_pago)
                            @case('CONTADO') Contado @break
                            @case('7_DIAS') 7 Días @break
                            @case('15_DIAS') 15 Días @break
                            @case('30_DIAS') 30 Días @break
                            @case('60_DIAS') 60 Días @break
                            @case('90_DIAS') 90 Días @break
                        @endswitch
                    </p>
                    <p><strong>Tipo de Orden:</strong> {{ $orden->tipo_orden }}</p>
                </div>
                <div class="col-md-4">
                    @if($orden->presupuesto_id)
                    <p><strong>Presupuesto:</strong>
                        <a href="{{ route('compras.presupuestos.show', $orden->presupuesto_id) }}">
                            {{ $orden->presupuesto->numero_presupuesto ?? '-' }}
                        </a>
                    </p>
                    @endif
                    <p><strong>Dirección de Entrega:</strong> {{ $orden->direccion_entrega ?? '-' }}</p>
                    <p><strong>Contacto:</strong> {{ $orden->contacto_recepcion ?? '-' }}</p>
                    <p><strong>Teléfono:</strong> {{ $orden->telefono_recepcion ?? '-' }}</p>
                </div>
            </div>

            @if($orden->observaciones)
            <div class="row mt-3">
                <div class="col-md-12">
                    <p><strong>Observaciones:</strong></p>
                    <p class="text-muted">{{ $orden->observaciones }}</p>
                </div>
            </div>
            @endif

            @if($orden->condiciones_especiales)
            <div class="row">
                <div class="col-md-12">
                    <p><strong>Condiciones Especiales:</strong></p>
                    <p class="text-muted">{{ $orden->condiciones_especiales }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Detalle de Productos -->
    <div class="card">
        <div class="card-header bg-secondary">
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
                        @foreach($orden->detalles as $detalle)
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
                            <td>{{ $producto->marca_nombre ?? ($detalle->marca ?? '-') }}</td>
                            <td class="text-right">{{ number_format($detalle->cantidad_ordenada, 2) }}</td>
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

    <!-- Auditoría -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Información de Auditoría</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Creado por:</strong> {{ $orden->creador->name ?? '-' }}</p>
                    <p><strong>Fecha de creación:</strong> {{ $orden->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    @if($orden->actualizadoPor)
                    <p><strong>Actualizado por:</strong> {{ $orden->actualizador->name ?? '-' }}</p>
                    <p><strong>Última actualización:</strong> {{ $orden->updated_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
