<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Remisión {{ $remision->numero_remision }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            padding: 15px;
        }

        .header-company {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }

        .logo-container {
            display: inline-block;
            vertical-align: middle;
            margin-right: 15px;
        }

        .logo-img {
            width: 70px;
            height: 70px;
        }

        .company-info {
            display: inline-block;
            vertical-align: middle;
            text-align: center;
        }

        .company-info h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
            color: #000;
        }

        .company-info p {
            font-size: 8pt;
            margin: 2px 0;
            color: #333;
        }

        .document-header {
            text-align: center;
            margin: 20px 0;
        }

        .document-type {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .document-number {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .estado-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
            color: white;
            font-size: 10pt;
        }

        .estado-borrador { background-color: #9e9e9e; }
        .estado-emitida { background-color: #4caf50; }
        .estado-transito { background-color: #ff9800; }
        .estado-entregada { background-color: #2196f3; }
        .estado-anulada { background-color: #f44336; }

        .info-boxes {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }

        .box {
            border: 2px solid #e0e0e0;
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 10px;
        }

        .box-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #bdbdbd;
        }

        .box-content {
            font-size: 9pt;
        }

        .box-row {
            margin: 5px 0;
        }

        .box-label {
            display: inline-block;
            width: 40%;
            font-weight: bold;
        }

        .box-value {
            display: inline-block;
            width: 58%;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .products-table thead {
            background-color: #424242;
            color: white;
        }

        .products-table thead th {
            padding: 10px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #424242;
        }

        .products-table tbody td {
            padding: 8px 5px;
            border: 1px solid #e0e0e0;
            font-size: 9pt;
        }

        .products-table tfoot {
            background-color: #424242;
            color: white;
            font-weight: bold;
        }

        .products-table tfoot td {
            padding: 10px 5px;
            border: 1px solid #424242;
            font-size: 10pt;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        .observations {
            margin: 20px 0;
            padding: 10px;
            background-color: #fff9e6;
            border-left: 4px solid #ffa000;
        }

        .delivery-info {
            margin: 20px 0;
            padding: 10px;
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
        }

        .signatures {
            margin-top: 50px;
            width: 100%;
        }

        .signature-box {
            display: inline-block;
            width: 30%;
            text-align: center;
            vertical-align: top;
            margin: 0 1.5%;
        }

        .signature-line {
            border-top: 2px solid #000;
            margin-top: 60px;
            padding-top: 5px;
            font-weight: bold;
            font-size: 9pt;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }

        .footer-title {
            font-weight: bold;
            font-size: 9pt;
            color: #000;
        }
    </style>
</head>
<body>
    {{-- Encabezado de la empresa --}}
    <div class="header-company">
        <div class="logo-container">
            <img src="{{ public_path('vendor/adminlte/dist/img/logo_redondo.png') }}" alt="Logo SIGEA" class="logo-img">
        </div>
        <div class="company-info">
            <h1>AGUATERÍA Y PLOMERÍA SIGEA SOCIEDAD ANÓNIMA</h1>
            <p>ventas@empresa.com.py | www.empresa.com.py | Contacto: (021) 555-1234 | Av. Eusebio Ayala Km 4.5</p>
            <p style="margin-top: 5px; font-weight: bold; font-size: 9pt;">DEPARTAMENTO DE VENTAS</p>
        </div>
    </div>

    {{-- Información del documento --}}
    <div class="document-header">
        <div style="font-size: 8pt; color: #666; margin-bottom: 5px;">
            Generado el: {{ now()->format('d/m/Y H:i') }} Hs
        </div>
        <div class="document-type">REMISIÓN</div>
        <div class="document-number">N° {{ $remision->numero_remision }}</div>
        @php
            $estado_class = match($remision->estado) {
                'BORRADOR' => 'estado-borrador',
                'EMITIDA' => 'estado-emitida',
                'EN_TRANSITO' => 'estado-transito',
                'ENTREGADA' => 'estado-entregada',
                'ANULADA' => 'estado-anulada',
                default => 'estado-borrador',
            };
            $estado_texto = match($remision->estado) {
                'BORRADOR' => 'BORRADOR',
                'EMITIDA' => 'EMITIDA',
                'EN_TRANSITO' => 'EN TRÁNSITO',
                'ENTREGADA' => 'ENTREGADA',
                'ANULADA' => 'ANULADA',
                default => $remision->estado,
            };
        @endphp
        <span class="estado-badge {{ $estado_class }}">{{ $estado_texto }}</span>
    </div>

    {{-- Información en dos columnas --}}
    <div class="info-boxes">
        <div class="info-box">
            <div class="box">
                <div class="box-title">CLIENTE</div>
                <div class="box-content">
                    <div class="box-row">
                        <span class="box-label">Nombre:</span>
                        <span class="box-value">{{ $remision->cliente->nombre_razon_social ?? 'N/A' }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">RUC/CI:</span>
                        <span class="box-value">{{ $remision->cliente->ruc_ci ?? 'N/A' }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">Dirección:</span>
                        <span class="box-value">{{ $remision->cliente->direccion ?? 'N/A' }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">Teléfono:</span>
                        <span class="box-value">{{ $remision->cliente->telefono ?? $remision->cliente->celular ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-box">
            <div class="box">
                <div class="box-title">DATOS DE LA REMISIÓN</div>
                <div class="box-content">
                    <div class="box-row">
                        <span class="box-label">Fecha Emisión:</span>
                        <span class="box-value">{{ $remision->fecha_emision->format('d/m/Y') }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">Fecha Entrega:</span>
                        <span class="box-value">{{ $remision->fecha_entrega ? $remision->fecha_entrega->format('d/m/Y') : 'No especificada' }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">Sucursal:</span>
                        <span class="box-value">{{ $remision->sucursal->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">Responsable:</span>
                        <span class="box-value">{{ $remision->responsable->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($remision->direccion_entrega)
    <div class="box" style="margin-bottom: 15px;">
        <div class="box-row">
            <span class="box-label">Dirección de Entrega:</span>
            <span class="box-value">{{ $remision->direccion_entrega }}</span>
        </div>
        @if($remision->factura)
        <div class="box-row">
            <span class="box-label">Factura Origen:</span>
            <span class="box-value">{{ $remision->factura->numero_factura }}</span>
        </div>
        @endif
    </div>
    @endif

    {{-- Tabla de productos --}}
    <table class="products-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 40%">Descripción</th>
                <th style="width: 12%" class="text-center">Cant.</th>
                <th style="width: 13%" class="text-center">P. Unit.</th>
                <th style="width: 15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($remision->detalles as $index => $detalle)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        {{ $detalle->producto_descripcion }}
                        @if($detalle->observaciones)
                            <br><small style="color: #666;">{{ $detalle->observaciones }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($detalle->cantidad, 0, ',', '.') }}</td>
                    <td class="text-right">Gs. {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                    <td class="text-right">Gs. {{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">TOTAL:</td>
                <td class="text-right">Gs. {{ number_format($remision->detalles->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Observaciones --}}
    @if($remision->observaciones)
        <div class="observations">
            <strong>OBSERVACIONES:</strong><br>
            {{ $remision->observaciones }}
        </div>
    @endif

    {{-- Información de entrega --}}
    @if($remision->estado === 'ENTREGADA')
        <div class="delivery-info">
            <strong>INFORMACIÓN DE ENTREGA</strong><br>
            <strong>Recibido por:</strong> {{ $remision->receptor_nombre }}<br>
            <strong>CI:</strong> {{ $remision->receptor_ci }}<br>
            <strong>Fecha de Recepción:</strong> {{ $remision->fecha_recepcion ? $remision->fecha_recepcion->format('d/m/Y H:i') : 'N/A' }}
        </div>
    @endif

    {{-- Información de anulación --}}
    @if($remision->estado === 'ANULADA' && $remision->motivo_anulacion)
        <div style="background-color: #ffebee; border-left: 4px solid #f44336; padding: 10px; margin: 20px 0;">
            <strong style="color: #f44336;">REMISIÓN ANULADA</strong><br>
            <strong>Motivo:</strong> {{ $remision->motivo_anulacion }}<br>
            <strong>Anulada por:</strong> {{ $remision->anuladoPor->name ?? 'N/A' }}<br>
            <strong>Fecha:</strong> {{ $remision->anulado_en ? $remision->anulado_en->format('d/m/Y H:i') : 'N/A' }}
        </div>
    @endif

    {{-- Firmas --}}
    @if($remision->estado !== 'ANULADA')
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">
                    Entregado por<br>
                    {{ $remision->emitidoPor->name ?? $remision->responsable->name ?? '' }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Transportista
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Recibido por<br>
                    {{ $remision->receptor_nombre ?? '' }}
                </div>
            </div>
        </div>
    @endif

    {{-- Pie de página --}}
    <div class="footer">
        <div class="footer-title">SIGEA - Sistema Integral de Gestión Empresarial y Administrativa</div>
        <div>Documento generado: {{ now()->format('d/m/Y H:i:s') }} | Emitida: {{ $remision->emitido_en ? $remision->emitido_en->format('d/m/Y H:i:s') : 'Pendiente' }}</div>
        <div style="font-size: 7pt; margin-top: 3px;">Este documento es válido sin firma ni sello según normativa vigente</div>
    </div>
</body>
</html>
