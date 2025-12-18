@extends('layouts.pdf.plantilla')

@section('titulo', 'Cuentas por Pagar')

@section('departamento')
    <strong style="font-size: 11px;">DEPARTAMENTO DE COMPRAS - PAGOS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 10px;">
        <h2 style="margin: 5px 0; font-size: 14px; text-transform: uppercase;">CUENTAS POR PAGAR / PAGOS</h2>
        <p style="margin: 3px 0; font-size: 9px; color: #666;">Facturas Aprobadas Pendientes de Pago</p>
    </div>

    <!-- Resumen General -->
    <div style="margin-bottom: 8px; padding: 6px 10px; background-color: #28a745; color: white;">
        <table style="width: 100%; font-size: 10px;">
            <tr>
                <td style="width: 70%; padding: 2px;">
                    <strong style="font-size: 11px;">Total de Facturas Aprobadas:</strong>
                    <span style="font-size: 9px;">{{ $compras->count() }} factura(s)</span>
                </td>
                <td style="width: 30%; text-align: right; padding: 2px;">
                    <strong style="font-size: 13px;">Gs. {{ number_format($totalGeneral, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabla de Facturas -->
    <table style="width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #343a40; color: white;">
                <th style="border: 1px solid #dee2e6; padding: 5px; text-align: left; width: 10%;">N° Factura</th>
                <th style="border: 1px solid #dee2e6; padding: 5px; text-align: left; width: 8%;">Timbrado</th>
                <th style="border: 1px solid #dee2e6; padding: 5px; text-align: left; width: 22%;">Proveedor</th>
                <th style="border: 1px solid #dee2e6; padding: 5px; text-align: center; width: 8%;">F. Emisión</th>
                <th style="border: 1px solid #dee2e6; padding: 5px; text-align: center; width: 8%;">F. Vencim.</th>
                <th style="border: 1px solid #dee2e6; padding: 5px; text-align: center; width: 7%;">Tipo</th>
                <th style="border: 1px solid #dee2e6; padding: 5px; text-align: center; width: 9%;">Condición</th>
                <th style="border: 1px solid #dee2e6; padding: 5px; text-align: right; width: 10%;">Subtotal</th>
                <th style="border: 1px solid #dee2e6; padding: 5px; text-align: right; width: 8%;">IVA</th>
                <th style="border: 1px solid #dee2e6; padding: 5px; text-align: right; width: 10%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($compras as $compra)
            <tr style="background-color: {{ $loop->even ? '#f8f9fa' : 'white' }};">
                <td style="border: 1px solid #dee2e6; padding: 4px;">
                    <strong>{{ $compra->numero_factura }}</strong>
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 8px;">
                    {{ $compra->timbrado ? substr($compra->timbrado, 0, 10) : 'N/A' }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px;">
                    {{ $compra->proveedor->razon_social ?? 'N/A' }}
                    @if($compra->proveedor)
                    <br><span style="font-size: 8px; color: #666;">{{ $compra->proveedor->ruc ?? '' }}</span>
                    @endif
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">
                    {{ $compra->fecha_emision ? $compra->fecha_emision->format('d/m/Y') : '-' }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">
                    {{ $compra->fecha_vencimiento ? $compra->fecha_vencimiento->format('d/m/Y') : '-' }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center; font-size: 8px;">
                    {{ $compra->tipo_factura == 'CONTADO' ? 'Contado' : 'Crédito' }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center; font-size: 8px;">
                    @switch($compra->condicion_pago)
                        @case('CONTADO') Contado @break
                        @case('7_DIAS') 7 Días @break
                        @case('15_DIAS') 15 Días @break
                        @case('30_DIAS') 30 Días @break
                        @case('60_DIAS') 60 Días @break
                        @case('90_DIAS') 90 Días @break
                        @default {{ $compra->condicion_pago }}
                    @endswitch
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">
                    Gs. {{ number_format($compra->subtotal ?? 0, 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">
                    Gs. {{ number_format(($compra->iva_10 ?? 0) + ($compra->iva_5 ?? 0), 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">
                    <strong>Gs. {{ number_format($compra->total ?? 0, 0, ',', '.') }}</strong>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="border: 1px solid #dee2e6; padding: 20px; text-align: center; color: #666;">
                    No se encontraron facturas aprobadas pendientes de pago
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($compras->count() > 0)
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="7" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">TOTALES:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">
                    Gs. {{ number_format($totalSubtotal, 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">
                    Gs. {{ number_format($totalIVA, 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">
                    <strong>Gs. {{ number_format($totalGeneral, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Resumen por Tipo de Factura -->
    @if($compras->count() > 0)
    <div style="margin-top: 20px;">
        <h4 style="margin: 10px 0; font-size: 12px; border-bottom: 2px solid #333; padding-bottom: 5px;">RESUMEN POR TIPO</h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #343a40; color: white;">
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: left;">Tipo de Factura</th>
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: center;">Cantidad</th>
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">Total (Gs.)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $contado = $compras->where('tipo_factura', 'CONTADO');
                    $credito = $compras->where('tipo_factura', 'CREDITO');
                @endphp
                @if($contado->count() > 0)
                <tr style="background-color: white;">
                    <td style="border: 1px solid #dee2e6; padding: 4px;">Contado</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">{{ $contado->count() }}</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">
                        Gs. {{ number_format($contado->sum('total'), 0, ',', '.') }}
                    </td>
                </tr>
                @endif
                @if($credito->count() > 0)
                <tr style="background-color: #f8f9fa;">
                    <td style="border: 1px solid #dee2e6; padding: 4px;">Crédito</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">{{ $credito->count() }}</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">
                        Gs. {{ number_format($credito->sum('total'), 0, ',', '.') }}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

    <!-- Información Adicional -->
    <div style="margin-top: 20px; font-size: 9px; color: #666;">
        <p style="margin: 3px 0;"><strong>Total de facturas:</strong> {{ $compras->count() }}</p>
        <p style="margin: 3px 0;"><strong>Facturas al contado:</strong> {{ $compras->where('tipo_factura', 'CONTADO')->count() }}</p>
        <p style="margin: 3px 0;"><strong>Facturas a crédito:</strong> {{ $compras->where('tipo_factura', 'CREDITO')->count() }}</p>
    </div>

    <!-- Nota -->
    <div style="margin-top: 30px; padding: 10px; background-color: #fff3cd; border-left: 3px solid #ffc107;">
        <p style="margin: 0; font-size: 9px; color: #856404;">
            <strong>Nota:</strong> Este reporte muestra únicamente las facturas de compra con estado APROBADO que están pendientes de pago.
        </p>
    </div>
@endsection
