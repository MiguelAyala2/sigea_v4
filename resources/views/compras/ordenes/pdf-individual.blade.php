@extends('layouts.pdf.plantilla')

@section('titulo', 'Orden de Compra')

@section('departamento')
    <strong style="font-size: 16px;">DEPARTAMENTO DE COMPRAS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 10px 0; font-size: 20px; text-transform: uppercase;">ORDEN DE COMPRA</h2>
        <h3 style="margin: 5px 0; font-size: 16px;">{{ $orden->numero_orden }}</h3>
    </div>

    <!-- Información de la Orden y Proveedor -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #f8f9fa; font-weight: bold; border-bottom: 2px solid #333;">PROVEEDOR</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Nombre:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->proveedor->nombre_fantasia }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Razón Social:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->proveedor->razon_social }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>RUC:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->proveedor->ruc }}</td>
                    </tr>
                    @if($orden->proveedor->direccion)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Dirección:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->proveedor->direccion }}</td>
                    </tr>
                    @endif
                    @if($orden->proveedor->telefono)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Teléfono:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->proveedor->telefono }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #f8f9fa; font-weight: bold; border-bottom: 2px solid #333;">DATOS DE LA ORDEN</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha de Orden:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->fecha_orden->format('d/m/Y') }}</td>
                    </tr>
                    @if($orden->fecha_entrega_esperada)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha Entrega:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->fecha_entrega_esperada->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Estado:</strong></td>
                        <td style="padding: 3px 5px;">
                            <span style="font-weight: bold; color: {{ in_array($orden->estado, ['APROBADO', 'EMITIDA']) ? '#28a745' : ($orden->estado == 'RECHAZADO' ? '#dc3545' : '#ffc107') }}">
                                {{ $orden->estado }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Tipo de Orden:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->tipo_orden }}</td>
                    </tr>
                    @if($orden->creadoPorUsuario)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Creado por:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->creadoPorUsuario->name }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Condiciones Comerciales y Entrega -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #e3f2fd; font-weight: bold; border-bottom: 2px solid #2196f3;">CONDICIONES COMERCIALES</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Condición de Pago:</strong></td>
                        <td style="padding: 3px 5px;">
                            @switch($orden->condicion_pago)
                                @case('CONTADO') Contado @break
                                @case('7_DIAS') 7 Días @break
                                @case('15_DIAS') 15 Días @break
                                @case('30_DIAS') 30 Días @break
                                @case('60_DIAS') 60 Días @break
                                @case('90_DIAS') 90 Días @break
                                @default {{ $orden->condicion_pago }} @break
                            @endswitch
                        </td>
                    </tr>
                    @if($orden->flete > 0)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Flete:</strong></td>
                        <td style="padding: 3px 5px;">Gs. {{ number_format($orden->flete, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($orden->descuento_global > 0)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Descuento:</strong></td>
                        <td style="padding: 3px 5px;">Gs. {{ number_format($orden->descuento_global, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #fff3cd; font-weight: bold; border-bottom: 2px solid #ffc107;">DOCUMENTOS RELACIONADOS</td>
                    </tr>
                    @if($orden->presupuesto)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>N° de Presupuesto:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->presupuesto->numero_presupuesto }}</td>
                    </tr>
                    @endif
                    @php
                        // Obtener el pedido directamente de la orden o del presupuesto
                        $pedido = $orden->pedidoCompra ?? ($orden->presupuesto?->pedidoCompra ?? null);
                    @endphp
                    @if($pedido)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>N° de Pedido:</strong></td>
                        <td style="padding: 3px 5px;">{{ $pedido->numero_pedido }}</td>
                    </tr>
                    @endif
                    @if(!$pedido && !$orden->presupuesto)
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; text-align: center; color: #666;">
                            Sin documentos relacionados
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    @if($orden->observaciones)
    <div style="margin-bottom: 15px; padding: 8px; background-color: #fff3cd; border-left: 3px solid #ffc107;">
        <p style="margin: 0; font-size: 10px;"><strong>Observaciones:</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 10px;">{{ $orden->observaciones }}</p>
    </div>
    @endif

    @if($orden->condiciones_especiales)
    <div style="margin-bottom: 15px; padding: 8px; background-color: #d1ecf1; border-left: 3px solid #0c5460;">
        <p style="margin: 0; font-size: 10px;"><strong>Condiciones Especiales:</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 10px;">{{ $orden->condiciones_especiales }}</p>
    </div>
    @endif

    <!-- Detalle de Productos -->
    <h4 style="margin: 20px 0 10px 0; font-size: 14px; border-bottom: 2px solid #333; padding-bottom: 5px;">DETALLE DE PRODUCTOS</h4>

    <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #343a40; color: white;">
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: left; width: 8%;">Código</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: left; width: 32%;">Producto</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: left; width: 15%;">Marca</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: right; width: 10%;">Cantidad</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: right; width: 12%;">Precio Unit.</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: center; width: 8%;">IVA %</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: right; width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subtotalGeneral = 0;
                $totalIVA = 0;
            @endphp
            @foreach($orden->detalles as $detalle)
            @php
                $producto = DB::table('stock.PRODUCTOS')
                    ->leftJoin('stock.MARCAS', 'stock.PRODUCTOS.marca_id', '=', 'stock.MARCAS.id')
                    ->where('stock.PRODUCTOS.id', $detalle->producto_id)
                    ->select('stock.PRODUCTOS.*', 'stock.MARCAS.nombre as marca_nombre')
                    ->first();

                $subtotalGeneral += $detalle->subtotal;
                $totalIVA += $detalle->iva_monto;
            @endphp
            <tr style="background-color: {{ $loop->even ? '#f8f9fa' : 'white' }};">
                <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $producto->codigo ?? '-' }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $producto->nombre ?? 'Producto #' . $detalle->producto_id }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $producto->marca_nombre ?? ($detalle->marca ?? '-') }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">{{ number_format($detalle->cantidad_ordenada, 2) }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">Gs. {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: center;">{{ $detalle->iva_porcentaje }}%</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">Gs. {{ number_format($detalle->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">SUBTOTAL:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($subtotalGeneral, 0, ',', '.') }}</td>
            </tr>
            @if($orden->iva_10 > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">IVA 10%:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($orden->iva_10, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($orden->iva_5 > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">IVA 5%:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($orden->iva_5, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($orden->exenta > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Exenta:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($orden->exenta, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($orden->flete > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Flete:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($orden->flete, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($orden->descuento_global > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Descuento Global:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">- Gs. {{ number_format($orden->descuento_global, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr style="background-color: #343a40; color: white; font-weight: bold; font-size: 11px;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">TOTAL GENERAL:</td>
                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">Gs. {{ number_format($orden->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Información adicional -->
    <div style="margin-top: 30px; font-size: 9px; color: #666;">
        <p style="margin: 3px 0;"><strong>Total de ítems:</strong> {{ $orden->detalles->count() }}</p>
        @if($orden->porcentaje_recibido > 0)
        <p style="margin: 3px 0;"><strong>Porcentaje recibido:</strong> {{ number_format($orden->porcentaje_recibido, 1) }}%</p>
        @endif
        @if($orden->presupuesto)
        <p style="margin: 3px 0;"><strong>Presupuesto de referencia:</strong> {{ $orden->presupuesto->numero_presupuesto }}</p>
        @endif
        @if($orden->pedidoCompra)
        <p style="margin: 3px 0;"><strong>Pedido de compra:</strong> {{ $orden->pedidoCompra->numero_pedido }}</p>
        @endif
    </div>

    <!-- Firmas -->
    <div style="margin-top: 50px;">
        <table style="width: 100%; font-size: 10px;">
            <tr>
                <td style="width: 50%; text-align: center; padding: 20px 10px;">
                    <div style="border-top: 1px solid #333; padding-top: 5px; margin: 0 20px;">
                        <strong>Firma y Sello del Proveedor</strong><br>
                        {{ $orden->proveedor->nombre_fantasia }}
                    </div>
                </td>
                <td style="width: 50%; text-align: center; padding: 20px 10px;">
                    <div style="border-top: 1px solid #333; padding-top: 5px; margin: 0 20px;">
                        <strong>Firma Autorizada SIGEA S.A.</strong><br>
                        @if($orden->aprobadoPorUsuario)
                        {{ $orden->aprobadoPorUsuario->name }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
