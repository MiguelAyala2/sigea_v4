@extends('adminlte::page')

@section('title', 'Detalle de Factura')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-file-invoice"></i> Factura {{ $factura->numero_timbrado }}-{{ $factura->numero_factura }}</h1>
        <div>
            <a href="{{ route('ventas.facturas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            @if($factura->estado === 'BORRADOR')
                <a href="{{ route('ventas.facturas.edit', $factura) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif
        </div>
    </div>
@stop

@section('content')
    {{-- Estado y acciones --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>
                                Estado:
                                @if($factura->estado === 'BORRADOR')
                                    <span class="badge badge-warning"><i class="fas fa-edit"></i> BORRADOR</span>
                                @elseif($factura->estado === 'EMITIDA')
                                    <span class="badge badge-success"><i class="fas fa-check"></i> EMITIDA</span>
                                @elseif($factura->estado === 'ANULADA')
                                    <span class="badge badge-danger"><i class="fas fa-ban"></i> ANULADA</span>
                                @endif
                            </h4>
                        </div>
                        <div class="col-md-4 text-right">
                            @if($factura->estado === 'BORRADOR')
                                <form action="{{ route('ventas.facturas.emitir', $factura) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Está seguro que desea emitir esta factura? Esta acción descuenta stock y genera movimientos de caja.');">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane"></i> Emitir Factura
                                    </button>
                                </form>
                            @endif

                            @if($factura->estado === 'EMITIDA' || $factura->estado === 'PAGADA')
                                <a href="{{ route('ventas.facturas.pdf', $factura) }}" target="_blank" class="btn btn-danger">
                                    <i class="fas fa-file-pdf"></i> Ver PDF
                                </a>
                                <a href="{{ route('ventas.facturas.pdf', ['factura' => $factura, 'download' => 1]) }}" class="btn btn-secondary">
                                    <i class="fas fa-download"></i> Descargar PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Cabecera de la factura --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Información de la Factura</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-hashtag"></i> Número de Factura:</strong>
                            <p class="mb-2">{{ $factura->numero_timbrado }}-{{ $factura->numero_factura }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar"></i> Fecha de Emisión:</strong>
                            <p class="mb-2">{{ $factura->fecha_emision->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-user"></i> Cliente:</strong>
                            <p class="mb-2">{{ $factura->cliente->nombre_razon_social ?? 'N/A' }}</p>
                            @if($factura->cliente && $factura->cliente->ruc_ci)
                                <small class="text-muted">RUC/CI: {{ $factura->cliente->ruc_ci }}</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-credit-card"></i> Condición de Pago:</strong>
                            <p class="mb-2">
                                @if($factura->condicion_pago === 'CONTADO')
                                    <span class="badge badge-success">CONTADO</span>
                                @else
                                    <span class="badge badge-info">CRÉDITO ({{ $factura->condicion_pago }})</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($factura->condicion_pago !== 'CONTADO')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar-check"></i> Fecha de Vencimiento:</strong>
                                <p class="mb-2">{{ $factura->fecha_vencimiento ? $factura->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-building"></i> Sucursal:</strong>
                            <p class="mb-2">{{ $factura->sucursal->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-warehouse"></i> Depósito:</strong>
                            <p class="mb-2">{{ $factura->deposito->nombre ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($factura->observaciones)
                        <div class="row">
                            <div class="col-12">
                                <strong><i class="fas fa-comment"></i> Observaciones:</strong>
                                <p class="mb-0">{{ $factura->observaciones }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Resumen de totales --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-calculator"></i> Resumen de Totales</h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td class="text-right">₲ {{ number_format($factura->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>IVA 10%:</strong></td>
                            <td class="text-right">₲ {{ number_format($factura->iva_10, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>IVA 5%:</strong></td>
                            <td class="text-right">₲ {{ number_format($factura->iva_5, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Exentas:</strong></td>
                            <td class="text-right">₲ {{ number_format($factura->exenta, 0, ',', '.') }}</td>
                        </tr>
                        @if($factura->descuento_global > 0)
                            <tr class="text-danger">
                                <td><strong>Descuento:</strong></td>
                                <td class="text-right">- ₲ {{ number_format($factura->descuento_global, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($factura->flete > 0)
                            <tr>
                                <td><strong>Flete:</strong></td>
                                <td class="text-right">₲ {{ number_format($factura->flete, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr class="border-top">
                            <td><h4><strong>TOTAL:</strong></h4></td>
                            <td class="text-right"><h4><strong class="text-success">₲ {{ number_format($factura->total, 0, ',', '.') }}</strong></h4></td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Formas de pago --}}
            @if($factura->formasPago->count() > 0)
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> Formas de Pago</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Forma de Pago</th>
                                    <th class="text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($factura->formasPago as $fp)
                                    <tr>
                                        <td>{{ $fp->forma_pago }}</td>
                                        <td class="text-right">₲ {{ number_format($fp->monto, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Detalle de productos --}}
    @if($factura->detalles->count() > 0)
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fas fa-box"></i> Detalle de Productos</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-right">Precio Unit.</th>
                                <th class="text-center">IVA</th>
                                <th class="text-right">Subtotal</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($factura->detalles as $index => $detalle)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $detalle->producto->codigo ?? 'N/A' }}</td>
                                    <td>
                                        <strong>{{ $detalle->producto->nombre ?? 'N/A' }}</strong>
                                        @if($detalle->descripcion_adicional)
                                            <br><small class="text-muted">{{ $detalle->descripcion_adicional }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($detalle->cantidad, 0) }}</td>
                                    <td class="text-right">₲ {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($detalle->iva_porcentaje == 10)
                                            <span class="badge badge-success">10%</span>
                                        @elseif($detalle->iva_porcentaje == 5)
                                            <span class="badge badge-info">5%</span>
                                        @else
                                            <span class="badge badge-secondary">Exenta</span>
                                        @endif
                                    </td>
                                    <td class="text-right">₲ {{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                                    <td class="text-right"><strong>₲ {{ number_format($detalle->total, 0, ',', '.') }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <td colspan="6" class="text-right"><strong>TOTALES:</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($factura->detalles->sum('subtotal'), 0, ',', '.') }}</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($factura->detalles->sum('total'), 0, ',', '.') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Detalle de servicios --}}
    @if($factura->servicios->count() > 0)
        <div class="card">
            <div class="card-header bg-secondary">
                <h3 class="card-title"><i class="fas fa-tools"></i> Detalle de Servicios</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-right">Precio Unit.</th>
                                <th class="text-right">Subtotal</th>
                                <th class="text-right">IVA</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($factura->servicios as $index => $servicio)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $servicio->codigo }}</td>
                                    <td>{{ $servicio->descripcion }}</td>
                                    <td class="text-center">{{ number_format($servicio->cantidad, 0) }}</td>
                                    <td class="text-right">₲ {{ number_format($servicio->precio_unitario, 0, ',', '.') }}</td>
                                    <td class="text-right">₲ {{ number_format($servicio->subtotal, 0, ',', '.') }}</td>
                                    <td class="text-right">₲ {{ number_format($servicio->iva_monto, 0, ',', '.') }}</td>
                                    <td class="text-right"><strong>₲ {{ number_format($servicio->total, 0, ',', '.') }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <td colspan="5" class="text-right"><strong>TOTALES:</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($factura->servicios->sum('subtotal'), 0, ',', '.') }}</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($factura->servicios->sum('iva_monto'), 0, ',', '.') }}</strong></td>
                                <td class="text-right"><strong>₲ {{ number_format($factura->servicios->sum('total'), 0, ',', '.') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Auditoría --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history"></i> Información de Auditoría</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Creado por:</strong> {{ $factura->creadoPor->name ?? 'N/A' }}</p>
                    <p><strong>Fecha de creación:</strong> {{ $factura->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                <div class="col-md-6">
                    @if($factura->actualizado_por)
                        <p><strong>Actualizado por:</strong> {{ $factura->actualizadoPor->name ?? 'N/A' }}</p>
                        <p><strong>Última actualización:</strong> {{ $factura->updated_at->format('d/m/Y H:i:s') }}</p>
                    @endif
                    @if($factura->emitido_por)
                        <p><strong>Emitido por:</strong> {{ $factura->emitidoPor->name ?? 'N/A' }}</p>
                        <p><strong>Fecha de emisión:</strong> {{ $factura->emitido_en ? $factura->emitido_en->format('d/m/Y H:i:s') : 'N/A' }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-header.bg-primary,
        .card-header.bg-success,
        .card-header.bg-info,
        .card-header.bg-secondary {
            color: white;
        }
        .table-borderless td {
            border: none;
            padding: 0.5rem 0;
        }
    </style>
@stop
