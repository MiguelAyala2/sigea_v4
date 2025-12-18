<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Presupuestos - {{ $fecha }}</title>
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
        .estado-pendiente {
            background-color: #ffc107;
            color: #000;
        }
        .estado-aprobado {
            background-color: #28a745;
            color: white;
        }
        .estado-rechazado {
            background-color: #dc3545;
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
            <h1>Lista de Presupuestos</h1>
            <p class="fecha">Generado: {{ $fecha }}</p>
            <p class="fecha">Total de registros: {{ $presupuestos->count() }}</p>
        </div>
    </div>

    {{-- Tabla de Presupuestos --}}
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">N° Presupuesto</th>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 20%;">Proveedor</th>
                <th style="width: 12%;">Condición</th>
                <th style="width: 8%;">Días Entrega</th>
                <th style="width: 15%;" class="text-right">Total (Gs.)</th>
                <th style="width: 12%;" class="text-center">Estado</th>
                <th style="width: 13%;">N° Pedido</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presupuestos as $presupuesto)
                <tr>
                    <td>{{ $presupuesto->numero_presupuesto }}</td>
                    <td>{{ $presupuesto->fecha_solicitud ? $presupuesto->fecha_solicitud->format('d/m/Y') : '-' }}</td>
                    <td>{{ $presupuesto->proveedor->razon_social ?? 'N/A' }}</td>
                    <td>
                        @switch($presupuesto->condicion_pago)
                            @case('CONTADO') Contado @break
                            @case('7_DIAS') 7 Días @break
                            @case('15_DIAS') 15 Días @break
                            @case('30_DIAS') 30 Días @break
                            @case('60_DIAS') 60 Días @break
                            @case('90_DIAS') 90 Días @break
                            @default {{ $presupuesto->condicion_pago }}
                        @endswitch
                    </td>
                    <td class="text-center">{{ $presupuesto->dias_entrega ?? '-' }}</td>
                    <td class="text-right">{{ number_format($presupuesto->total ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="estado-badge
                            @if($presupuesto->estado == 'PENDIENTE') estado-pendiente
                            @elseif($presupuesto->estado == 'APROBADO') estado-aprobado
                            @elseif($presupuesto->estado == 'RECHAZADO') estado-rechazado
                            @endif
                        ">
                            {{ ucfirst(strtolower($presupuesto->estado)) }}
                        </span>
                    </td>
                    <td>{{ $presupuesto->pedidoCompra->numero_pedido ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay presupuestos registrados</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL GENERAL:</td>
                <td class="text-right">{{ number_format($presupuestos->sum('total'), 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Documento generado por SIGEA - Sistema Integrado de Gestión Empresarial</p>
    </div>
</body>
</html>
