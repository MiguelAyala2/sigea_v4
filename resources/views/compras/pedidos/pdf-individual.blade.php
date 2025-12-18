@extends('layouts.pdf.plantilla')

@section('titulo', 'Pedido de Compra')

@section('departamento')
    <strong style="font-size: 16px;">DEPARTAMENTO DE COMPRAS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 10px 0; font-size: 20px; text-transform: uppercase;">PEDIDO DE COMPRA</h2>
        <h3 style="margin: 5px 0; font-size: 16px;">{{ $pedido->numero_pedido }}</h3>
    </div>

    <!-- Información del Pedido -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha del Pedido:</strong></td>
                        <td style="padding: 3px 5px;">{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha Necesaria:</strong></td>
                        <td style="padding: 3px 5px;">{{ $pedido->fecha_necesaria?->format('d/m/Y') ?? 'No especificada' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Tipo de Pedido:</strong></td>
                        <td style="padding: 3px 5px;">{{ $pedido->tipo_pedido }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Prioridad:</strong></td>
                        <td style="padding: 3px 5px;">
                            <span style="font-weight: bold; color: {{ $pedido->prioridad == 'CRITICA' ? '#dc3545' : ($pedido->prioridad == 'URGENTE' ? '#ffc107' : '#17a2b8') }}">
                                {{ $pedido->prioridad }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Estado:</strong></td>
                        <td style="padding: 3px 5px;">
                            <span style="font-weight: bold; color: {{ $pedido->estado == 'APROBADO' ? '#28a745' : ($pedido->estado == 'RECHAZADO' ? '#dc3545' : '#ffc107') }}">
                                {{ $pedido->estado }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Solicitante:</strong></td>
                        <td style="padding: 3px 5px;">{{ $pedido->usuarioSolicitante->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Creado por:</strong></td>
                        <td style="padding: 3px 5px;">{{ $pedido->creadoPorUsuario->name ?? 'N/A' }}</td>
                    </tr>
                    @if($pedido->aprobadoPor)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Aprobado por:</strong></td>
                        <td style="padding: 3px 5px;">{{ $pedido->aprobadoPorUsuario->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha Aprobación:</strong></td>
                        <td style="padding: 3px 5px;">{{ $pedido->aprobado_en?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    @if($pedido->justificacion)
    <div style="margin-bottom: 15px; padding: 8px; background-color: #f8f9fa; border-left: 3px solid #007bff;">
        <p style="margin: 0; font-size: 10px;"><strong>Justificación:</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 10px;">{{ $pedido->justificacion }}</p>
    </div>
    @endif

    @if($pedido->observaciones)
    <div style="margin-bottom: 15px; padding: 8px; background-color: #fff3cd; border-left: 3px solid #ffc107;">
        <p style="margin: 0; font-size: 10px;"><strong>Observaciones:</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 10px;">{{ $pedido->observaciones }}</p>
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
                <th style="border: 1px solid #dee2e6; padding: 6px; text-align: right; width: 15%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $totalIVA = 0;
            @endphp
            @foreach($pedido->detalles as $detalle)
            @php
                $producto = DB::table('stock.PRODUCTOS')
                    ->leftJoin('stock.MARCAS', 'stock.PRODUCTOS.marca_id', '=', 'stock.MARCAS.id')
                    ->where('stock.PRODUCTOS.id', $detalle->producto_id)
                    ->select('stock.PRODUCTOS.*', 'stock.MARCAS.nombre as marca_nombre')
                    ->first();

                $subtotal = $detalle->subtotal_estimado;
                $ivaPorcentaje = $detalle->iva_porcentaje ?? 10;
                $ivaLinea = $subtotal * ($ivaPorcentaje / 100);

                $total += $subtotal;
                $totalIVA += $ivaLinea;
            @endphp
            <tr style="background-color: {{ $loop->even ? '#f8f9fa' : 'white' }};">
                <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $producto->codigo ?? '-' }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $producto->nombre ?? 'Producto #' . $detalle->producto_id }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $producto->marca_nombre ?? ($detalle->marca_solicitada ?? '-') }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">{{ number_format($detalle->cantidad_solicitada, 2) }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">Gs. {{ number_format($detalle->precio_estimado, 0, ',', '.') }}</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: center;">{{ $ivaPorcentaje }}%</td>
                <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">Gs. {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">SUBTOTAL:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">IVA:</td>
                <td style="border: 1px solid #dee2e6; padding: 6px; text-align: right;">Gs. {{ number_format($totalIVA, 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #343a40; color: white; font-weight: bold; font-size: 11px;">
                <td colspan="6" style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">TOTAL GENERAL:</td>
                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">Gs. {{ number_format($total + $totalIVA, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Información adicional -->
    <div style="margin-top: 30px; font-size: 9px; color: #666;">
        <p style="margin: 3px 0;"><strong>Total de ítems:</strong> {{ $pedido->detalles->count() }}</p>
        <p style="margin: 3px 0;"><strong>Porcentaje ordenado:</strong> {{ number_format($pedido->porcentaje_ordenado, 1) }}%</p>
    </div>
@endsection
