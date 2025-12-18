@extends('adminlte::page')

@section('title', 'Detalle de Compra')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Compra #{{ $compra->numero_factura }}</h1>
        <div>
            <a href="{{ route('compras.compras.imprimir-pdf', $compra->id) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-file-pdf"></i> Imprimir PDF
            </a>

            @if($compra->estado === 'BORRADOR' || $compra->estado === 'PENDIENTE')
                <a href="{{ route('compras.compras.edit', $compra->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif

            @if(in_array($compra->estado, ['BORRADOR', 'PENDIENTE']))
                <form action="{{ route('compras.compras.aprobar', $compra->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('¿Aprobar esta compra/factura?')">
                        <i class="fas fa-check"></i> Aprobar
                    </button>
                </form>

                <form action="{{ route('compras.compras.anular', $compra->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Anular esta compra/factura?')">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                </form>
            @endif

            <a href="{{ route('compras.compras.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Información General -->
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Información General</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Número de Factura:</strong> {{ $compra->numero_factura }}</p>
                    <p><strong>Timbrado:</strong> {{ $compra->timbrado ?? '-' }}</p>
                    <p><strong>Tipo de Documento:</strong> {{ $compra->tipo_documento }}</p>
                    <p><strong>Estado:</strong>
                        @switch($compra->estado)
                            @case('BORRADOR')
                                <span class="badge badge-secondary">Borrador</span>
                                @break
                            @case('PENDIENTE')
                                <span class="badge badge-warning">Pendiente</span>
                                @break
                            @case('APROBADA')
                                <span class="badge badge-info">Aprobada</span>
                                @break
                            @case('PAGADA')
                                <span class="badge badge-success">Pagada</span>
                                @break
                            @case('PARCIAL')
                                <span class="badge badge-primary">Pago Parcial</span>
                                @break
                            @case('ANULADA')
                                <span class="badge badge-danger">Anulada</span>
                                @break
                        @endswitch
                    </p>
                </div>
                <div class="col-md-4">
                    <p><strong>Proveedor:</strong> {{ $compra->proveedor->nombre_fantasia ?? '-' }}</p>
                    <p><strong>RUC:</strong> {{ $compra->proveedor->ruc ?? '-' }}</p>
                    <p><strong>Condición de Pago:</strong>
                        @switch($compra->condicion_pago)
                            @case('CONTADO') Contado @break
                            @case('7_DIAS') 7 Días @break
                            @case('15_DIAS') 15 Días @break
                            @case('30_DIAS') 30 Días @break
                            @case('60_DIAS') 60 Días @break
                            @case('90_DIAS') 90 Días @break
                        @endswitch
                    </p>
                    <p><strong>Tipo de Factura:</strong> {{ $compra->tipo_factura }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Fecha de Emisión:</strong> {{ $compra->fecha_emision->format('d/m/Y') }}</p>
                    <p><strong>Fecha de Vencimiento:</strong> {{ $compra->fecha_vencimiento ? $compra->fecha_vencimiento->format('d/m/Y') : '-' }}</p>
                    @if($compra->ordenCompra)
                    <p><strong>Orden de Compra:</strong>
                        <a href="{{ route('compras.ordenes.show', $compra->ordenCompra->id) }}">
                            {{ $compra->ordenCompra->numero_orden }}
                        </a>
                    </p>
                    @endif
                </div>
            </div>

            @if($compra->observaciones)
            <div class="row mt-3">
                <div class="col-md-12">
                    <p><strong>Observaciones:</strong></p>
                    <p class="text-muted">{{ $compra->observaciones }}</p>
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
                        @foreach($compra->detalles as $detalle)
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
                            <td>{{ $producto->marca_nombre ?? '-' }}</td>
                            <td class="text-right">{{ number_format($detalle->cantidad, 2) }}</td>
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
                    <p><strong>Creado por:</strong> {{ $compra->creador->name ?? '-' }}</p>
                    <p><strong>Fecha de creación:</strong> {{ $compra->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    @if($compra->actualizadoPor)
                    <p><strong>Actualizado por:</strong> {{ $compra->actualizador->name ?? '-' }}</p>
                    <p><strong>Última actualización:</strong> {{ $compra->updated_at->format('d/m/Y H:i') }}</p>
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
