@extends('layouts.pdf.plantilla')

@section('titulo', 'Libro de Compras')

@section('departamento')
    <strong style="font-size: 11px;">DEPARTAMENTO DE COMPRAS - LIBRO DE COMPRAS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 10px;">
        <h2 style="margin: 5px 0; font-size: 14px; text-transform: uppercase;">LIBRO DE COMPRAS</h2>
        <p style="margin: 3px 0; font-size: 9px; color: #666;">
            Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        </p>
        @if($proveedorNombre)
        <p style="margin: 3px 0; font-size: 9px; color: #666;">
            Proveedor: {{ $proveedorNombre }}
        </p>
        @endif
    </div>

    <!-- Resumen General -->
    <div style="margin-bottom: 8px; padding: 6px 10px; background-color: #17a2b8; color: white;">
        <table style="width: 100%; font-size: 10px;">
            <tr>
                <td style="width: 25%; padding: 2px;">
                    <strong style="font-size: 10px;">Total Transacciones:</strong>
                    <span style="font-size: 9px;">{{ $transacciones->count() }}</span>
                </td>
                <td style="width: 25%; text-align: center; padding: 2px;">
                    <strong style="font-size: 10px;">Facturas:</strong>
                    <span style="font-size: 9px;">{{ $transacciones->where('tipo', 'Factura')->count() }}</span>
                </td>
                <td style="width: 25%; text-align: center; padding: 2px;">
                    <strong style="font-size: 10px;">N/C:</strong>
                    <span style="font-size: 9px;">{{ $transacciones->where('tipo', 'Nota Crédito')->count() }}</span>
                </td>
                <td style="width: 25%; text-align: right; padding: 2px;">
                    <strong style="font-size: 10px;">N/D:</strong>
                    <span style="font-size: 9px;">{{ $transacciones->where('tipo', 'Nota Débito')->count() }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabla de Transacciones -->
    <table style="width: 100%; border-collapse: collapse; font-size: 8px; margin-bottom: 15px;">
        <thead>
            <tr style="background-color: #343a40; color: white;">
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center; width: 8%;">Tipo</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; width: 10%;">Número</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center; width: 8%;">Fecha</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; width: 27%;">Proveedor</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; width: 12%;">RUC/DNI</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: right; width: 11%;">Subtotal</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: right; width: 11%;">IVA</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: right; width: 13%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transacciones as $transaccion)
            <tr style="background-color: {{ $loop->even ? '#f8f9fa' : 'white' }};">
                <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center; font-size: 7px;">
                    @if($transaccion['tipo'] === 'Factura')
                        FAC
                    @elseif($transaccion['tipo'] === 'Nota Crédito')
                        N/C
                    @else
                        N/D
                    @endif
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; font-size: 7px;">
                    {{ $transaccion['numero'] }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center; font-size: 7px;">
                    {{ \Carbon\Carbon::parse($transaccion['fecha'])->format('d/m/Y') }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; font-size: 7px;">
                    {{ Str::limit($transaccion['proveedor'], 35) }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; font-size: 7px;">
                    {{ $transaccion['ruc'] }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; text-align: right; font-size: 7px;">
                    Gs. {{ number_format($transaccion['subtotal'], 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; text-align: right; font-size: 7px;">
                    Gs. {{ number_format($transaccion['impuesto'], 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; text-align: right; font-size: 7px;">
                    <strong>Gs. {{ number_format($transaccion['total'], 0, ',', '.') }}</strong>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="border: 1px solid #dee2e6; padding: 15px; text-align: center; color: #666;">
                    No hay transacciones en el período seleccionado
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($transacciones->count() > 0)
        <tfoot style="background-color: #e9ecef;">
            <tr>
                <th colspan="5" style="border: 1px solid #dee2e6; padding: 4px; text-align: right; font-size: 9px;">
                    TOTALES:
                </th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: right; font-size: 8px;">
                    Gs. {{ number_format($totales['subtotal'], 0, ',', '.') }}
                </th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: right; font-size: 8px;">
                    Gs. {{ number_format($totales['impuesto'], 0, ',', '.') }}
                </th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: right; font-size: 8px;">
                    <strong>Gs. {{ number_format($totales['total'], 0, ',', '.') }}</strong>
                </th>
            </tr>
        </tfoot>
        @endif
    </table>

    @if($transacciones->count() > 0)
    <!-- Resumen por Tipo de Documento -->
    <div style="margin-top: 15px;">
        <h4 style="margin: 8px 0; font-size: 11px; border-bottom: 2px solid #333; padding-bottom: 3px;">RESUMEN POR TIPO DE DOCUMENTO</h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 10px;">
            <thead>
                <tr style="background-color: #343a40; color: white;">
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left;">Tipo de Documento</th>
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">Cantidad</th>
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">Subtotal</th>
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">IVA</th>
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach(['Factura', 'Nota Crédito', 'Nota Débito'] as $tipo)
                    @php
                        $porTipo = $transacciones->where('tipo', $tipo);
                    @endphp
                    @if($porTipo->count() > 0)
                    <tr style="background-color: {{ $loop->even ? 'white' : '#f8f9fa' }};">
                        <td style="border: 1px solid #dee2e6; padding: 3px;">{{ $tipo }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center;">{{ $porTipo->count() }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 3px; text-align: right; font-size: 8px;">
                            Gs. {{ number_format($porTipo->sum('subtotal'), 0, ',', '.') }}
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 3px; text-align: right; font-size: 8px;">
                            Gs. {{ number_format($porTipo->sum('impuesto'), 0, ',', '.') }}
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 3px; text-align: right; font-size: 8px;">
                            <strong>Gs. {{ number_format($porTipo->sum('total'), 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Resumen Tributario -->
    <div style="margin-top: 15px; padding: 8px; background-color: #e9ecef; border: 1px solid #dee2e6;">
        <h4 style="margin: 5px 0 8px 0; font-size: 11px; font-weight: bold;">RESUMEN TRIBUTARIO</h4>
        <table style="width: 100%; font-size: 9px;">
            <tr>
                <td style="padding: 3px; width: 33%;">
                    <strong>Base Imponible:</strong><br>
                    <span style="font-size: 11px;">Gs. {{ number_format($totales['subtotal'], 0, ',', '.') }}</span>
                </td>
                <td style="padding: 3px; width: 33%;">
                    <strong>Total IVA:</strong><br>
                    <span style="font-size: 11px;">Gs. {{ number_format($totales['impuesto'], 0, ',', '.') }}</span>
                </td>
                <td style="padding: 3px; width: 34%; text-align: right;">
                    <strong>Total General:</strong><br>
                    <span style="font-size: 12px; font-weight: bold;">Gs. {{ number_format($totales['total'], 0, ',', '.') }}</span>
                </td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Información Adicional -->
    <div style="margin-top: 15px; font-size: 8px; color: #666;">
        <p style="margin: 2px 0;"><strong>Total de transacciones:</strong> {{ $transacciones->count() }}</p>
        <p style="margin: 2px 0;"><strong>Facturas de compra:</strong> {{ $transacciones->where('tipo', 'Factura')->count() }}</p>
        <p style="margin: 2px 0;"><strong>Notas de Crédito:</strong> {{ $transacciones->where('tipo', 'Nota Crédito')->count() }}</p>
        <p style="margin: 2px 0;"><strong>Notas de Débito:</strong> {{ $transacciones->where('tipo', 'Nota Débito')->count() }}</p>
    </div>

    <!-- Leyenda -->
    <div style="margin-top: 15px; padding: 6px; background-color: #e9ecef; border-left: 3px solid #6c757d;">
        <p style="margin: 0; font-size: 8px; color: #495057;">
            <strong>Leyenda:</strong> FAC = Factura | N/C = Nota de Crédito | N/D = Nota de Débito
        </p>
    </div>
@endsection
