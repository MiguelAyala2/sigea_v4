@extends('layouts.pdf.plantilla')

@section('titulo', 'Presupuesto')

@section('departamento')
    <strong style="font-size: 16px;">DEPARTAMENTO DE COMPRAS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 10px 0; font-size: 20px; text-transform: uppercase;">PRESUPUESTO</h2>
        <h3 style="margin: 5px 0; font-size: 16px;">{{ $presupuesto->numero_presupuesto }}</h3>
    </div>

    <!-- Información del Presupuesto y Proveedor -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #f8f9fa; font-weight: bold; border-bottom: 2px solid #333;">DATOS DEL PROVEEDOR</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Proveedor:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->proveedor->nombre_fantasia }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Razón Social:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->proveedor->razon_social }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>RUC:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->proveedor->ruc }}</td>
                    </tr>
                    @if($presupuesto->proveedor->telefono)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Teléfono:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->proveedor->telefono }}</td>
                    </tr>
                    @endif
                    @if($presupuesto->proveedor->email)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Email:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->proveedor->email }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #f8f9fa; font-weight: bold; border-bottom: 2px solid #333;">DATOS DEL PRESUPUESTO</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha Solicitud:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->fecha_solicitud->format('d/m/Y') }}</td>
                    </tr>
                    @if($presupuesto->fecha_recepcion)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha Recepción:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->fecha_recepcion->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    @if($presupuesto->fecha_vencimiento)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha Vencimiento:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->fecha_vencimiento->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Estado:</strong></td>
                        <td style="padding: 3px 5px;">
                            <span style="font-weight: bold; color: {{ in_array($presupuesto->estado, ['APROBADO', 'SELECCIONADO']) ? '#28a745' : ($presupuesto->estado == 'RECHAZADO' ? '#dc3545' : '#ffc107') }}">
                                {{ $presupuesto->estado }}
                            </span>
                        </td>
                    </tr>
                    @if($presupuesto->pedidoCompra)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Pedido de Compra:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->pedidoCompra->numero_pedido }}</td>
                    </tr>
                    @endif
                    @if($presupuesto->solicitadoPorUsuario)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Solicitado por:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->solicitadoPorUsuario->name }}</td>
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
                            @switch($presupuesto->condicion_pago)
                                @case('CONTADO') Contado @break
                                @case('7_DIAS') 7 Días @break
                                @case('15_DIAS') 15 Días @break
                                @case('30_DIAS') 30 Días @break
                                @case('60_DIAS') 60 Días @break
                                @case('90_DIAS') 90 Días @break
                                @default {{ $presupuesto->condicion_pago }} @break
                            @endswitch
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Plazo de Entrega:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->dias_entrega }} días</td>
                    </tr>
                    @if($presupuesto->flete > 0)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Flete:</strong></td>
                        <td style="padding: 3px 5px;">Gs. {{ number_format($presupuesto->flete, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($presupuesto->descuento_general > 0)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Descuento:</strong></td>
                        <td style="padding: 3px 5px;">Gs. {{ number_format($presupuesto->descuento_general, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #fff3cd; font-weight: bold; border-bottom: 2px solid #ffc107;">DOCUMENTOS RELACIONADOS</td>
                    </tr>
                    @if($presupuesto->pedidoCompra)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>N° de Pedido:</strong></td>
                        <td style="padding: 3px 5px;">{{ $presupuesto->pedidoCompra->numero_pedido }}</td>
                    </tr>
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
            @foreach($presupuesto->detalles as $detalle)
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
                <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $producto->marca_nombre ?? ($detalle->marca_ofrecida ?? '-') }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">{{ number_format($detalle->cantidad_cotizada, 2) }}</td>
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
            @if($presupuesto->iva_10 > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">IVA 10%:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($presupuesto->iva_10, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($presupuesto->iva_5 > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">IVA 5%:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($presupuesto->iva_5, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($presupuesto->exenta > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Exenta:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($presupuesto->exenta, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($presupuesto->flete > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Flete:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($presupuesto->flete, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($presupuesto->descuento_general > 0)
            <tr style="background-color: #e9ecef;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Descuento General:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">- Gs. {{ number_format($presupuesto->descuento_general, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr style="background-color: #343a40; color: white; font-weight: bold; font-size: 11px;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">TOTAL GENERAL:</td>
                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">Gs. {{ number_format($presupuesto->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if($presupuesto->observaciones)
    <div style="margin-bottom: 15px; padding: 8px; background-color: #fff3cd; border-left: 3px solid #ffc107;">
        <p style="margin: 0; font-size: 10px;"><strong>Observaciones:</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 10px;">{{ $presupuesto->observaciones }}</p>
    </div>
    @endif

    @if($presupuesto->observaciones_evaluacion)
    <div style="margin-bottom: 15px; padding: 8px; background-color: #d1ecf1; border-left: 3px solid #0c5460;">
        <p style="margin: 0; font-size: 10px;"><strong>Observaciones de Evaluación:</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 10px;">{{ $presupuesto->observaciones_evaluacion }}</p>
    </div>
    @endif

    <!-- Información adicional -->
    <div style="margin-top: 30px; font-size: 9px; color: #666;">
        <p style="margin: 3px 0;"><strong>Total de ítems:</strong> {{ $presupuesto->detalles->count() }}</p>
        @if($presupuesto->puntuacion)
        <p style="margin: 3px 0;"><strong>Puntuación:</strong> {{ $presupuesto->puntuacion }}/100</p>
        @endif
        @if($presupuesto->evaluadoPorUsuario)
        <p style="margin: 3px 0;"><strong>Evaluado por:</strong> {{ $presupuesto->evaluadoPorUsuario->name }}</p>
        @endif
    </div>
@endsection
