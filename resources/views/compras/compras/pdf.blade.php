<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Compras/Facturas - {{ $fecha }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            padding: 20px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        h1 {
            font-size: 18px;
            color: #333;
            margin-bottom: 5px;
        }
        .empresa-info {
            font-size: 9px;
            color: #666;
        }
        .fecha {
            font-size: 9px;
            color: #999;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #17a2b8;
            color: white;
            border: 1px solid #138496;
            padding: 8px 4px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .estado-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            display: inline-block;
        }
        .estado-borrador {
            background-color: #6c757d;
            color: white;
        }
        .estado-pendiente {
            background-color: #ffc107;
            color: #000;
        }
        .estado-aprobada {
            background-color: #28a745;
            color: white;
        }
        .estado-rechazada {
            background-color: #dc3545;
            color: white;
        }
        .estado-anulada {
            background-color: #343a40;
            color: white;
        }
        .estado-pagada {
            background-color: #007bff;
            color: white;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    {{-- Header con logo y datos de empresa --}}
    <div class="header">
        <div class="header-left">
            @if($empresa && $empresa->logo_path)
                <img src="{{ public_path('storage/' . $empresa->logo_path) }}" alt="Logo" class="logo">
            @else
                <h1>SIGEA</h1>
            @endif
            @if($empresa)
                <div class="empresa-info">
                    <strong>{{ $empresa->razon_social }}</strong><br>
                    RUC: {{ $empresa->ruc }}<br>
                    {{ $empresa->direccion }}
                </div>
            @endif
        </div>
        <div class="header-right">
            <h1>Lista de Compras/Facturas</h1>
            <p class="fecha">Generado: {{ $fecha }}</p>
            <p class="fecha">Total de registros: {{ $compras->count() }}</p>
        </div>
    </div>

    {{-- Tabla de Compras --}}
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">N° Factura</th>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 20%;">Proveedor</th>
                <th style="width: 12%;">Tipo</th>
                <th style="width: 12%;">Condición</th>
                <th style="width: 15%;" class="text-right">Total (Gs.)</th>
                <th style="width: 12%;" class="text-center">Estado</th>
                <th style="width: 9%;">Creado Por</th>
            </tr>
        </thead>
        <tbody>
            @forelse($compras as $compra)
                <tr>
                    <td>{{ $compra->numero_factura }}</td>
                    <td>{{ $compra->fecha_emision ? $compra->fecha_emision->format('d/m/Y') : '-' }}</td>
                    <td>{{ $compra->proveedor->razon_social ?? 'N/A' }}</td>
                    <td>
                        @if($compra->tipo_factura == 'CONTADO') Contado
                        @elseif($compra->tipo_factura == 'CREDITO') Crédito
                        @else {{ $compra->tipo_factura }}
                        @endif
                    </td>
                    <td>
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
                    <td class="text-right">{{ number_format($compra->total ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="estado-badge
                            @if($compra->estado == 'BORRADOR') estado-borrador
                            @elseif($compra->estado == 'PENDIENTE') estado-pendiente
                            @elseif($compra->estado == 'APROBADA') estado-aprobada
                            @elseif($compra->estado == 'RECHAZADA') estado-rechazada
                            @elseif($compra->estado == 'ANULADA') estado-anulada
                            @elseif($compra->estado == 'PAGADA') estado-pagada
                            @endif
                        ">
                            {{ ucfirst(strtolower($compra->estado)) }}
                        </span>
                    </td>
                    <td>{{ $compra->creadoPorUsuario->name ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay compras registradas</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL GENERAL:</td>
                <td class="text-right">{{ number_format($compras->sum('total'), 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Documento generado por SIGEA - Sistema Integrado de Gestión Empresarial</p>
    </div>
</body>
</html>
