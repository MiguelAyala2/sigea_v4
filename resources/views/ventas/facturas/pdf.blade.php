<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura {{ $factura->numero_timbrado }}-{{ $factura->numero_factura }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            padding: 15px;
            line-height: 1.3;
        }

        .encabezado {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
        }

        .logo-sigea {
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: 3px;
            color: #000;
            margin-bottom: 3px;
        }

        .logo-box {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: inline-block;
            border: 3px solid #3498db;
            margin-bottom: 5px;
        }

        .empresa-nombre {
            font-size: 12pt;
            font-weight: bold;
            margin: 5px 0;
        }

        .empresa-info {
            font-size: 8pt;
            color: #333;
        }

        .titulo-documento {
            text-align: center;
            margin: 10px 0;
        }

        .titulo-documento h2 {
            font-size: 14pt;
            margin: 5px 0;
            text-transform: uppercase;
        }

        .titulo-documento h3 {
            font-size: 12pt;
            margin: 3px 0;
        }

        .info-tabla {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .info-tabla td {
            padding: 3px 5px;
            vertical-align: top;
        }

        .seccion-header {
            background-color: #f0f0f0;
            font-weight: bold;
            border-bottom: 1px solid #333;
            padding: 3px 5px !important;
        }

        .tabla-detalle {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin: 10px 0;
        }

        .tabla-detalle th {
            background-color: #333;
            color: white;
            padding: 5px 3px;
            text-align: left;
            border: 1px solid #000;
            font-size: 8pt;
        }

        .tabla-detalle td {
            border: 1px solid #ddd;
            padding: 4px 3px;
        }

        .tabla-detalle tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totales {
            margin-top: 10px;
            float: right;
            width: 40%;
            font-size: 9pt;
        }

        .totales table {
            width: 100%;
            border-collapse: collapse;
        }

        .totales td {
            padding: 3px 5px;
            border: none;
        }

        .total-final {
            background-color: #333;
            color: white;
            font-weight: bold;
            font-size: 10pt;
            padding: 5px !important;
        }

        .firmas {
            clear: both;
            margin-top: 40px;
            width: 100%;
            font-size: 9pt;
        }

        .firma-box {
            display: inline-block;
            width: 48%;
            text-align: center;
            vertical-align: bottom;
        }

        .firma-linea {
            border-top: 1px solid #000;
            margin: 0 20px;
            padding-top: 5px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 7pt;
            text-align: center;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-danger { background-color: #dc3545; color: white; }

        .timbrado-box {
            background-color: #f8f9fa;
            padding: 5px;
            border: 1px solid #ccc;
            font-size: 8pt;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    {{-- Encabezado --}}
    <div class="encabezado">
        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <td style="width: 15%; text-align: left; vertical-align: top; padding: 0;">
                    <img src="{{ public_path('vendor/adminlte/dist/img/logo_redondo.png') }}" alt="Logo" style="width: 70px; height: 70px;">
                </td>
                <td style="width: 70%; text-align: center; vertical-align: top; padding: 0;">
                    <div style="font-size: 11pt; font-weight: bold; margin: 0;">
                        {{ $factura->puntoExpedicion->sucursal->empresa->razon_social ?? 'EMPRESA S.A.' }}
                    </div>
                    <div style="font-size: 7pt; margin: 2px 0; line-height: 1.2;">
                        {{ $factura->puntoExpedicion->sucursal->empresa->correo ?? 'ventas@empresa.com.py' }} |
                        {{ $factura->puntoExpedicion->sucursal->empresa->web ?? 'www.empresa.com.py' }} |
                        Contacto: {{ $factura->puntoExpedicion->sucursal->telefono ?? '021 555-124' }} |
                        {{ $factura->puntoExpedicion->sucursal->direccion ?? 'Av. Dirección' }}
                    </div>
                    <div style="font-size: 10pt; font-weight: bold; margin: 3px 0; padding: 2px 0; border-top: 1px solid #000; border-bottom: 1px solid #000;">
                        DEPARTAMENTO DE VENTAS
                    </div>
                    <div style="font-size: 7pt; color: #666; margin-top: 2px;">
                        Generado el: {{ now()->format('d/m/Y H:i') }} Hs
                    </div>
                </td>
                <td style="width: 15%; text-align: right; vertical-align: top; padding: 0;">
                    <img src="{{ public_path('vendor/adminlte/dist/img/logo_redondo.png') }}" alt="Logo" style="width: 70px; height: 70px;">
                </td>
            </tr>
        </table>
    </div>

    {{-- Título del Documento --}}
    <div class="titulo-documento">
        <h2>FACTURA</h2>
        <h3>N° {{ $factura->numero_timbrado }}-{{ $factura->numero_factura }}</h3>
        @if($factura->estado === 'BORRADOR')
            <span class="badge badge-warning">BORRADOR</span>
        @elseif($factura->estado === 'EMITIDA')
            <span class="badge badge-success">EMITIDA</span>
        @elseif($factura->estado === 'ANULADA')
            <span class="badge badge-danger">ANULADA</span>
        @endif
    </div>

    {{-- Información Principal --}}
    <table class="info-tabla">
        <tr>
            <td style="width: 50%;">
                <table style="width: 100%; font-size: 8pt;">
                    <tr>
                        <td colspan="2" class="seccion-header">CLIENTE</td>
                    </tr>
                    <tr>
                        <td style="width: 30%;"><strong>Nombre:</strong></td>
                        <td>{{ $factura->cliente->nombre_razon_social ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>RUC/CI:</strong></td>
                        <td>{{ $factura->cliente->ruc_ci ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Dirección:</strong></td>
                        <td>{{ $factura->cliente->direccion ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Teléfono:</strong></td>
                        <td>{{ $factura->cliente->telefono ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%;">
                <table style="width: 100%; font-size: 8pt;">
                    <tr>
                        <td colspan="2" class="seccion-header">DATOS DE LA FACTURA</td>
                    </tr>
                    <tr>
                        <td style="width: 40%;"><strong>Fecha Emisión:</strong></td>
                        <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Condición:</strong></td>
                        <td>
                            @if($factura->condicion_pago === 'CONTADO')
                                <strong>CONTADO</strong>
                            @else
                                CRÉDITO - {{ str_replace('_', ' ', $factura->condicion_pago) }}
                            @endif
                        </td>
                    </tr>
                    @if($factura->condicion_pago !== 'CONTADO' && $factura->fecha_vencimiento)
                    <tr>
                        <td><strong>Vencimiento:</strong></td>
                        <td>{{ $factura->fecha_vencimiento->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Vendedor:</strong></td>
                        <td>{{ $factura->vendedor->name ?? ($factura->creadoPor->name ?? 'N/A') }}</td>
                    </tr>
                </table>
                <div class="timbrado-box">
                    <strong>Timbrado:</strong> {{ $factura->numero_timbrado }}<br>
                    <strong>Vigencia:</strong> {{ $factura->timbrado->fecha_inicio_vigencia->format('d/m/Y') }} - {{ $factura->timbrado->fecha_fin_vigencia->format('d/m/Y') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Detalle de Productos --}}
    @if($factura->detalles->count() > 0)
    <table class="tabla-detalle">
        <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th style="width: 14%;">Código</th>
                <th style="width: 40%;">Descripción</th>
                <th style="width: 10%;">Cant.</th>
                <th style="width: 15%;">P. Unit.</th>
                <th style="width: 8%;">IVA</th>
                <th style="width: 17%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->detalles as $index => $detalle)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $detalle->producto->codigo ?? 'N/A' }}</td>
                <td>
                    {{ $detalle->producto->nombre ?? 'N/A' }}
                    @if($detalle->descripcion_adicional)
                        <br><small>{{ $detalle->descripcion_adicional }}</small>
                    @endif
                </td>
                <td class="text-center">{{ number_format($detalle->cantidad, 0) }}</td>
                <td class="text-right">{{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($detalle->iva_porcentaje == 10)10%
                    @elseif($detalle->iva_porcentaje == 5)5%
                    @else Exe.
                    @endif
                </td>
                <td class="text-right"><strong>{{ number_format($detalle->subtotal, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Detalle de Servicios --}}
    @if($factura->servicios->count() > 0)
    <h4 style="font-size: 10pt; margin: 10px 0 5px 0;">SERVICIOS</h4>
    <table class="tabla-detalle">
        <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th style="width: 15%;">Código</th>
                <th style="width: 45%;">Descripción</th>
                <th style="width: 10%;">Cantidad</th>
                <th style="width: 12%;">Precio Unit.</th>
                <th style="width: 12%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->servicios as $index => $servicio)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $servicio->codigo }}</td>
                <td>{{ $servicio->descripcion }}</td>
                <td class="text-center">{{ number_format($servicio->cantidad, 0) }}</td>
                <td class="text-right">{{ number_format($servicio->precio_unitario, 0, ',', '.') }}</td>
                <td class="text-right"><strong>{{ number_format($servicio->total, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Totales --}}
    <div class="totales">
        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-right">Gs. {{ number_format($factura->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($factura->iva_10 > 0)
            <tr>
                <td><strong>IVA 10%:</strong></td>
                <td class="text-right">Gs. {{ number_format($factura->iva_10, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($factura->iva_5 > 0)
            <tr>
                <td><strong>IVA 5%:</strong></td>
                <td class="text-right">Gs. {{ number_format($factura->iva_5, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($factura->exenta > 0)
            <tr>
                <td><strong>Exentas:</strong></td>
                <td class="text-right">Gs. {{ number_format($factura->exenta, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($factura->descuento_global > 0)
            <tr>
                <td><strong>Descuento:</strong></td>
                <td class="text-right" style="color: red;">- Gs. {{ number_format($factura->descuento_global, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($factura->flete > 0)
            <tr>
                <td><strong>Flete:</strong></td>
                <td class="text-right">Gs. {{ number_format($factura->flete, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="2" class="total-final text-right">
                    TOTAL: Gs. {{ number_format($factura->total, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    {{-- Formas de Pago --}}
    @if($factura->formasPago->count() > 0)
    <div style="margin-top: 10px; padding: 5px; background-color: #f8f9fa; border: 1px solid #ddd; font-size: 8pt;">
        <strong>Formas de Pago:</strong>
        @foreach($factura->formasPago as $fp)
            {{ $fp->forma_pago }}: Gs. {{ number_format($fp->monto, 0, ',', '.') }}@if(!$loop->last) | @endif
        @endforeach
    </div>
    @endif

    @if($factura->observaciones)
    <div style="margin-top: 8px; padding: 5px; background-color: #fff3cd; border-left: 2px solid #ffc107; font-size: 8pt;">
        <strong>Observaciones:</strong> {{ $factura->observaciones }}
    </div>
    @endif

    {{-- Firma del Cajero/Vendedor --}}
    <div class="firmas">
        <div style="text-align: center; margin: 0 auto; width: 50%;">
            <div class="firma-linea">
                <strong>Cajero / Vendedor</strong><br>
                <small>{{ $factura->emitidoPor->name ?? ($factura->creadoPor->name ?? 'Autorizado') }}</small>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <strong style="color: #000;">SIGEA - Sistema Integral de Gestión Empresarial y Administrativa</strong><br>
        Documento generado: {{ now()->format('d/m/Y H:i:s') }}
        @if($factura->emitido_en)
            | Emitida: {{ $factura->emitido_en->format('d/m/Y H:i:s') }}
        @endif
        <br>
        <small>Este documento es válido sin firma ni sello según normativa vigente</small>
    </div>
</body>
</html>
