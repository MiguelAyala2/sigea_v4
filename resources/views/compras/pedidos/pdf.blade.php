<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pedidos de Compra - {{ $fecha }}</title>
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
            background-color: #007bff;
            color: white;
            border: 1px solid #0056b3;
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
        .estado-aprobado {
            background-color: #28a745;
            color: white;
        }
        .estado-rechazado {
            background-color: #dc3545;
            color: white;
        }
        .prioridad-alta {
            color: #dc3545;
            font-weight: bold;
        }
        .prioridad-media {
            color: #ffc107;
        }
        .prioridad-baja {
            color: #28a745;
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
            <h1>Lista de Pedidos de Compra</h1>
            <p class="fecha">Generado: {{ $fecha }}</p>
            <p class="fecha">Total de registros: {{ $pedidos->count() }}</p>
        </div>
    </div>

    {{-- Tabla de Pedidos --}}
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Número</th>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 18%;">Solicitante</th>
                <th style="width: 12%;">Tipo</th>
                <th style="width: 10%;">Prioridad</th>
                <th style="width: 15%;" class="text-right">Total Est. (Gs.)</th>
                <th style="width: 12%;" class="text-center">Estado</th>
                <th style="width: 15%;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pedidos as $pedido)
                <tr>
                    <td>{{ $pedido->numero_pedido }}</td>
                    <td>{{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y') : '-' }}</td>
                    <td>{{ $pedido->usuarioSolicitante->name ?? 'N/A' }}</td>
                    <td>
                        @switch($pedido->tipo_pedido)
                            @case('NORMAL') Normal @break
                            @case('URGENTE') Urgente @break
                            @case('SERVICIO') Servicio @break
                            @case('INSUMOS') Insumos @break
                            @default {{ $pedido->tipo_pedido }}
                        @endswitch
                    </td>
                    <td>
                        <span class="
                            @if($pedido->prioridad == 'ALTA') prioridad-alta
                            @elseif($pedido->prioridad == 'MEDIA') prioridad-media
                            @else prioridad-baja
                            @endif
                        ">
                            {{ ucfirst(strtolower($pedido->prioridad ?? 'Media')) }}
                        </span>
                    </td>
                    <td class="text-right">{{ number_format($pedido->total_estimado ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="estado-badge
                            @if($pedido->estado == 'BORRADOR') estado-borrador
                            @elseif(in_array($pedido->estado, ['PENDIENTE_APROBACION', 'PENDIENTE'])) estado-pendiente
                            @elseif($pedido->estado == 'APROBADO') estado-aprobado
                            @elseif($pedido->estado == 'RECHAZADO') estado-rechazado
                            @endif
                        ">
                            @if($pedido->estado == 'PENDIENTE_APROBACION')
                                Pendiente
                            @else
                                {{ ucfirst(strtolower($pedido->estado)) }}
                            @endif
                        </span>
                    </td>
                    <td>{{ Str::limit($pedido->observaciones ?? '-', 40) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay pedidos registrados</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL GENERAL:</td>
                <td class="text-right">{{ number_format($pedidos->sum('total_estimado'), 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Documento generado por SIGEA - Sistema Integrado de Gestión Empresarial</p>
    </div>
</body>
</html>
