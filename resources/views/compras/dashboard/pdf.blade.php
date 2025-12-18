<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Compras - {{ $fecha }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
        h2 {
            font-size: 14px;
            color: #666;
            margin: 15px 0 10px 0;
            background-color: #f0f0f0;
            padding: 8px;
            border-left: 4px solid #007bff;
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
            margin-bottom: 20px;
        }
        th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .bg-warning {
            background-color: #fff3cd;
        }
        .bg-success {
            background-color: #d4edda;
        }
        .bg-danger {
            background-color: #f8d7da;
        }
        .totales {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .section-title {
            color: #007bff;
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
            <h1>Dashboard Compras</h1>
            <p class="fecha">Generado: {{ $fecha }}</p>
        </div>
    </div>

    {{-- PEDIDOS DE COMPRA --}}
    <h2 class="section-title">📋 Pedidos de Compra</h2>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Total (Gs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-warning">
                <td>Pendientes</td>
                <td class="text-center">{{ $datos['pedidos']['pendientes']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['pedidos']['pendientes']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-success">
                <td>Aprobados</td>
                <td class="text-center">{{ $datos['pedidos']['aprobados']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['pedidos']['aprobados']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-danger">
                <td>Rechazados</td>
                <td class="text-center">{{ $datos['pedidos']['rechazados']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['pedidos']['rechazados']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="totales">
                <td>TOTAL</td>
                <td class="text-center">
                    {{ $datos['pedidos']['pendientes']['cantidad'] + $datos['pedidos']['aprobados']['cantidad'] + $datos['pedidos']['rechazados']['cantidad'] }}
                </td>
                <td class="text-right">
                    {{ number_format($datos['pedidos']['pendientes']['total'] + $datos['pedidos']['aprobados']['total'] + $datos['pedidos']['rechazados']['total'], 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- PRESUPUESTOS --}}
    <h2 class="section-title">💰 Presupuestos</h2>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Total (Gs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-warning">
                <td>Pendientes</td>
                <td class="text-center">{{ $datos['presupuestos']['pendientes']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['presupuestos']['pendientes']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-success">
                <td>Aprobados</td>
                <td class="text-center">{{ $datos['presupuestos']['aprobados']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['presupuestos']['aprobados']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-danger">
                <td>Rechazados</td>
                <td class="text-center">{{ $datos['presupuestos']['rechazados']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['presupuestos']['rechazados']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="totales">
                <td>TOTAL</td>
                <td class="text-center">
                    {{ $datos['presupuestos']['pendientes']['cantidad'] + $datos['presupuestos']['aprobados']['cantidad'] + $datos['presupuestos']['rechazados']['cantidad'] }}
                </td>
                <td class="text-right">
                    {{ number_format($datos['presupuestos']['pendientes']['total'] + $datos['presupuestos']['aprobados']['total'] + $datos['presupuestos']['rechazados']['total'], 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ÓRDENES DE COMPRA --}}
    <h2 class="section-title">📝 Órdenes de Compra</h2>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Total (Gs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-warning">
                <td>Pendientes</td>
                <td class="text-center">{{ $datos['ordenes']['pendientes']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['ordenes']['pendientes']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-success">
                <td>Aprobados</td>
                <td class="text-center">{{ $datos['ordenes']['aprobados']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['ordenes']['aprobados']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-danger">
                <td>Rechazados</td>
                <td class="text-center">{{ $datos['ordenes']['rechazados']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['ordenes']['rechazados']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="totales">
                <td>TOTAL</td>
                <td class="text-center">
                    {{ $datos['ordenes']['pendientes']['cantidad'] + $datos['ordenes']['aprobados']['cantidad'] + $datos['ordenes']['rechazados']['cantidad'] }}
                </td>
                <td class="text-right">
                    {{ number_format($datos['ordenes']['pendientes']['total'] + $datos['ordenes']['aprobados']['total'] + $datos['ordenes']['rechazados']['total'], 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- COMPRAS/FACTURAS --}}
    <h2 class="section-title">🧾 Compras/Facturas</h2>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Total (Gs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-warning">
                <td>Pendientes</td>
                <td class="text-center">{{ $datos['compras']['pendientes']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['compras']['pendientes']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-success">
                <td>Aprobados</td>
                <td class="text-center">{{ $datos['compras']['aprobados']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['compras']['aprobados']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-danger">
                <td>Rechazados</td>
                <td class="text-center">{{ $datos['compras']['rechazados']['cantidad'] }}</td>
                <td class="text-right">{{ number_format($datos['compras']['rechazados']['total'], 0, ',', '.') }}</td>
            </tr>
            <tr class="totales">
                <td>TOTAL</td>
                <td class="text-center">
                    {{ $datos['compras']['pendientes']['cantidad'] + $datos['compras']['aprobados']['cantidad'] + $datos['compras']['rechazados']['cantidad'] }}
                </td>
                <td class="text-right">
                    {{ number_format($datos['compras']['pendientes']['total'] + $datos['compras']['aprobados']['total'] + $datos['compras']['rechazados']['total'], 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Documento generado por SIGEA - Sistema Integrado de Gestión Empresarial</p>
    </div>
</body>
</html>
