@extends('layouts.pdf.plantilla')

@section('titulo', 'Factura de Compra')

@section('departamento')
    <strong style="font-size: 16px;">DEPARTAMENTO DE COMPRAS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 10px 0; font-size: 20px; text-transform: uppercase;">FACTURA DE COMPRA</h2>
        <h3 style="margin: 5px 0; font-size: 16px;">{{ $compra->numero_factura }}</h3>
    </div>

    <!-- Información de la Compra y Proveedor -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #f8f9fa; font-weight: bold; border-bottom: 2px solid #333;">DATOS DEL PROVEEDOR</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Proveedor:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->proveedor->nombre_fantasia }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Razón Social:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->proveedor->razon_social }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>RUC:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->proveedor->ruc }}</td>
                    </tr>
                    @if($compra->proveedor->telefono)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Teléfono:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->proveedor->telefono }}</td>
                    </tr>
                    @endif
                    @if($compra->proveedor->email)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Email:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->proveedor->email }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #f8f9fa; font-weight: bold; border-bottom: 2px solid #333;">DATOS DE LA FACTURA</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha de Emisión:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->fecha_emision->format('d/m/Y') }}</td>
                    </tr>
                    @if($compra->fecha_vencimiento)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha Vencimiento:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->fecha_vencimiento->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    @if($compra->timbrado)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Timbrado:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->timbrado }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Tipo de Factura:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->tipo_factura }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Tipo de Documento:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->tipo_documento }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Estado:</strong></td>
                        <td style="padding: 3px 5px;">
                            <span style="font-weight: bold; color: {{ $compra->estado == 'APROBADO' ? '#28a745' : ($compra->estado == 'RECHAZADO' ? '#dc3545' : '#ffc107') }}">
                                {{ $compra->estado }}
                            </span>
                        </td>
                    </tr>
                    @if($compra->creadoPorUsuario)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Creado por:</strong></td>
                        <td style="padding: 3px 5px;">{{ $compra->creadoPorUsuario->name }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Condiciones Comerciales y Documentos Relacionados -->
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
                            @switch($compra->condicion_pago)
                                @case('CONTADO') Contado @break
                                @case('7_DIAS') 7 Días @break
                                @case('15_DIAS') 15 Días @break
                                @case('30_DIAS') 30 Días @break
                                @case('60_DIAS') 60 Días @break
                                @case('90_DIAS') 90 Días @break
                                @default {{ $compra->condicion_pago }} @break
                            @endswitch
                        </td>
                    </tr>
                    @if($compra->es_electronica)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Factura Electrónica:</strong></td>
                        <td style="padding: 3px 5px;">Sí</td>
                    </tr>
                    @endif
                    @if($compra->cdc)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>CDC:</strong></td>
                        <td style="padding: 3px 5px; font-size: 9px;">{{ substr($compra->cdc, 0, 30) }}...</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #fff3cd; font-weight: bold; border-bottom: 2px solid #ffc107;">DOCUMENTOS RELACIONADOS</td>
                    </tr>
                    @php
                        // Obtener Presupuesto y Pedido a través de la Orden de Compra
                        $ordenCompra = $compra->ordenCompra ?? null;
                        $presupuesto = $ordenCompra?->presupuesto ?? null;
                        $pedido = $ordenCompra?->pedidoCompra ?? ($presupuesto?->pedidoCompra ?? null);

                        $hayDocumentos = $ordenCompra || $presupuesto || $pedido;
                    @endphp

                    @if($hayDocumentos)
                        @if($pedido)
                        <tr>
                            <td style="padding: 3px 5px;"><strong>N° de Pedido:</strong></td>
                            <td style="padding: 3px 5px;">{{ $pedido->numero_pedido }}</td>
                        </tr>
                        @endif
                        @if($presupuesto)
                        <tr>
                            <td style="padding: 3px 5px;"><strong>N° de Presupuesto:</strong></td>
                            <td style="padding: 3px 5px;">{{ $presupuesto->numero_presupuesto }}</td>
                        </tr>
                        @endif
                        @if($ordenCompra)
                        <tr>
                            <td style="padding: 3px 5px;"><strong>N° de Orden de Compra:</strong></td>
                            <td style="padding: 3px 5px;">{{ $ordenCompra->numero_orden }}</td>
                        </tr>
                        @endif
                    @else
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

    <!-- Detalle de Productos -->
    <h4 style="margin: 20px 0 10px 0; font-size: 14px; border-bottom: 2px solid #333; padding-bottom: 5px;">DETALLE DE PRODUCTOS</h4>

    <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #343a40; color: white;">
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: left; width: 8%;">Código</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: left; width: 37%;">Producto</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: left; width: 12%;">Marca</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: right; width: 10%;">Cantidad</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: right; width: 12%;">Precio Unit.</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: center; width: 8%;">IVA %</th>
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: right; width: 13%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subtotalGeneral = 0;
                $totalIVA = 0;
            @endphp
            @foreach($compra->detalles as $detalle)
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
                <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $producto->marca_nombre ?? '-' }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">{{ number_format($detalle->cantidad, 2) }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">Gs. {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: center;">{{ $detalle->iva_porcentaje }}%</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">Gs. {{ number_format($detalle->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">SUBTOTAL:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($compra->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($compra->iva_10 > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">IVA 10%:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($compra->iva_10, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($compra->iva_5 > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">IVA 5%:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($compra->iva_5, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($compra->exenta > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Exenta:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($compra->exenta, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr style="background-color: #343a40; color: white; font-weight: bold; font-size: 11px;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">TOTAL GENERAL:</td>
                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">Gs. {{ number_format($compra->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if($compra->observaciones)
    <div style="margin-bottom: 15px; padding: 8px; background-color: #fff3cd; border-left: 3px solid #ffc107;">
        <p style="margin: 0; font-size: 10px;"><strong>Observaciones:</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 10px;">{{ $compra->observaciones }}</p>
    </div>
    @endif

    <!-- Información adicional -->
    <div style="margin-top: 30px; font-size: 9px; color: #666;">
        <p style="margin: 3px 0;"><strong>Total de ítems:</strong> {{ $compra->detalles->count() }}</p>
        <p style="margin: 3px 0;"><strong>Fecha de impresión:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Firmas -->
    <div style="margin-top: 60px;">
        <table style="width: 100%; font-size: 10px;">
            <tr>
                <td style="width: 50%; text-align: center; padding: 10px; vertical-align: bottom;">
                    <div style="border-top: 1px solid #333; padding-top: 5px; margin: 0 30px;">
                        <strong>Firma y Sello del Proveedor</strong>
                    </div>
                </td>
                <td style="width: 50%; text-align: center; padding: 10px; vertical-align: bottom;">
                    <div style="border-top: 1px solid #333; padding-top: 5px; margin: 0 30px;">
                        <strong>Firma Autorizada - Compras</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
