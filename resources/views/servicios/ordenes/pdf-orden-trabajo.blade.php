@extends('layouts.pdf.plantilla')

@section('titulo', 'Orden de Trabajo')

@section('departamento')
    <strong style="font-size: 16px;">DEPARTAMENTO DE SERVICIOS TÉCNICOS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 10px 0; font-size: 20px; text-transform: uppercase;">ORDEN DE TRABAJO</h2>
        <h3 style="margin: 5px 0; font-size: 16px;">{{ $orden->codigo }}</h3>
    </div>

    <!-- Información del Cliente y Equipo -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #f8f9fa; font-weight: bold; border-bottom: 2px solid #333;">DATOS DEL CLIENTE</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Cliente:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->presupuesto->diagnostico->recepcion->solicitud->cliente->nombre }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>RUC/CI:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->presupuesto->diagnostico->recepcion->solicitud->cliente->documento ?? 'N/A' }}</td>
                    </tr>
                    @if($orden->presupuesto->diagnostico->recepcion->solicitud->cliente->telefono)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Teléfono:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->presupuesto->diagnostico->recepcion->solicitud->cliente->telefono }}</td>
                    </tr>
                    @endif
                    @if($orden->presupuesto->diagnostico->recepcion->solicitud->cliente->email)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Email:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->presupuesto->diagnostico->recepcion->solicitud->cliente->email }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; background-color: #f8f9fa; font-weight: bold; border-bottom: 2px solid #333;">DATOS DE LA ORDEN</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha Orden:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->fecha_orden->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Presupuesto:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->presupuesto->codigo }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Estado:</strong></td>
                        <td style="padding: 3px 5px;">
                            <span style="font-weight: bold; color: {{ $orden->estado == 'finalizada' ? '#28a745' : ($orden->estado == 'en_proceso' ? '#ffc107' : '#6c757d') }}">
                                {{ strtoupper(str_replace('_', ' ', $orden->estado)) }}
                            </span>
                        </td>
                    </tr>
                    @if($orden->tecnico_id)
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Técnico Asignado:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->tecnico->name }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Información del Equipo -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="padding: 5px;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td colspan="4" style="padding: 3px 5px; background-color: #e3f2fd; font-weight: bold; border-bottom: 2px solid #2196f3;">EQUIPO A REPARAR</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 5px; width: 20%;"><strong>Equipo:</strong></td>
                        <td style="padding: 3px 5px; width: 30%;">{{ $orden->presupuesto->diagnostico->recepcion->producto->nombre }}</td>
                        <td style="padding: 3px 5px; width: 20%;"><strong>Nº Serie:</strong></td>
                        <td style="padding: 3px 5px; width: 30%;">{{ $orden->presupuesto->diagnostico->recepcion->numero_serie ?? 'N/A' }}</td>
                    </tr>
                    @if($orden->presupuesto->diagnostico->descripcion_problema)
                    <tr>
                        <td style="padding: 3px 5px;" colspan="4">
                            <strong>Problema Reportado:</strong><br>
                            {{ $orden->presupuesto->diagnostico->descripcion_problema }}
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Servicios a Realizar -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse; border: 1px solid #dee2e6;">
        <thead>
            <tr style="background-color: #28a745; color: white;">
                <th style="padding: 8px 5px; text-align: left; font-size: 11px; border: 1px solid #dee2e6; width: 80px;">CÓDIGO</th>
                <th style="padding: 8px 5px; text-align: left; font-size: 11px; border: 1px solid #dee2e6;">TIPO DE SERVICIO</th>
                <th style="padding: 8px 5px; text-align: center; font-size: 11px; border: 1px solid #dee2e6; width: 60px;">CANT.</th>
                <th style="padding: 8px 5px; text-align: right; font-size: 11px; border: 1px solid #dee2e6; width: 100px;">P. UNIT.</th>
                <th style="padding: 8px 5px; text-align: right; font-size: 11px; border: 1px solid #dee2e6; width: 100px;">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orden->presupuesto->diagnostico->tiposServicio as $servicio)
            <tr>
                <td style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6;">{{ $servicio->tipoServicio->codigo ?? 'N/A' }}</td>
                <td style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6;">{{ $servicio->tipoServicio->nombre ?? 'N/A' }}</td>
                <td style="padding: 5px; font-size: 10px; text-align: center; border: 1px solid #dee2e6;">{{ $servicio->cantidad }}</td>
                <td style="padding: 5px; font-size: 10px; text-align: right; border: 1px solid #dee2e6;">Gs. {{ number_format($servicio->costo_unitario, 0, ',', '.') }}</td>
                <td style="padding: 5px; font-size: 10px; text-align: right; border: 1px solid #dee2e6;">Gs. {{ number_format($servicio->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="padding: 5px; font-size: 11px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">SUBTOTAL SERVICIOS:</td>
                <td style="padding: 5px; font-size: 11px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">Gs. {{ number_format($orden->presupuesto->subtotal_servicios, 0, ',', '.') }}</td>
            </tr>
            @if($orden->presupuesto->descuento_promocion > 0)
            <tr>
                <td colspan="4" style="padding: 5px; font-size: 11px; text-align: right; border: 1px solid #dee2e6;">Descuento Promoción:</td>
                <td style="padding: 5px; font-size: 11px; text-align: right; color: #dc3545; border: 1px solid #dee2e6;">- Gs. {{ number_format($orden->presupuesto->descuento_promocion, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr style="background-color: #f8f9fa;">
                <td colspan="4" style="padding: 5px; font-size: 12px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">TOTAL SERVICIOS:</td>
                <td style="padding: 5px; font-size: 12px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">Gs. {{ number_format($orden->presupuesto->total_servicios, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Repuestos Necesarios -->
    @if($orden->presupuesto->diagnostico->repuestos->count() > 0)
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse; border: 1px solid #dee2e6;">
        <thead>
            <tr style="background-color: #ffc107; color: #000;">
                <th style="padding: 8px 5px; text-align: left; font-size: 11px; border: 1px solid #dee2e6; width: 80px;">CÓDIGO</th>
                <th style="padding: 8px 5px; text-align: left; font-size: 11px; border: 1px solid #dee2e6;">REPUESTO</th>
                <th style="padding: 8px 5px; text-align: center; font-size: 11px; border: 1px solid #dee2e6; width: 60px;">CANT.</th>
                <th style="padding: 8px 5px; text-align: right; font-size: 11px; border: 1px solid #dee2e6; width: 100px;">P. UNIT.</th>
                <th style="padding: 8px 5px; text-align: right; font-size: 11px; border: 1px solid #dee2e6; width: 100px;">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orden->presupuesto->diagnostico->repuestos as $repuesto)
            <tr>
                <td style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6;">{{ $repuesto->producto->codigo }}</td>
                <td style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6;">{{ $repuesto->producto->nombre }}</td>
                <td style="padding: 5px; font-size: 10px; text-align: center; border: 1px solid #dee2e6;">{{ $repuesto->cantidad }}</td>
                <td style="padding: 5px; font-size: 10px; text-align: right; border: 1px solid #dee2e6;">Gs. {{ number_format($repuesto->costo, 0, ',', '.') }}</td>
                <td style="padding: 5px; font-size: 10px; text-align: right; border: 1px solid #dee2e6;">Gs. {{ number_format($repuesto->cantidad * $repuesto->costo, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="padding: 5px; font-size: 11px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">SUBTOTAL REPUESTOS:</td>
                <td style="padding: 5px; font-size: 11px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">Gs. {{ number_format($orden->presupuesto->subtotal_repuestos, 0, ',', '.') }}</td>
            </tr>
            @if($orden->presupuesto->descuento_descuento > 0)
            <tr>
                <td colspan="4" style="padding: 5px; font-size: 11px; text-align: right; border: 1px solid #dee2e6;">Descuento Aplicado:</td>
                <td style="padding: 5px; font-size: 11px; text-align: right; color: #dc3545; border: 1px solid #dee2e6;">- Gs. {{ number_format($orden->presupuesto->descuento_descuento, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr style="background-color: #f8f9fa;">
                <td colspan="4" style="padding: 5px; font-size: 12px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">TOTAL REPUESTOS:</td>
                <td style="padding: 5px; font-size: 12px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">Gs. {{ number_format($orden->presupuesto->total_repuestos, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- Total General -->
    <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse; border: 2px solid #333;">
        <tr style="background-color: #28a745; color: white;">
            <td style="padding: 10px; font-size: 14px; text-align: right; font-weight: bold;">TOTAL GENERAL:</td>
            <td style="padding: 10px; font-size: 16px; text-align: right; font-weight: bold; width: 150px;">Gs. {{ number_format($orden->presupuesto->monto_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Términos y Condiciones -->
    <div style="margin-top: 20px; font-size: 10px;">
        <p style="margin: 5px 0;"><strong>TÉRMINOS Y CONDICIONES:</strong></p>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li>Los trabajos realizados tienen garantía de 30 días a partir de la fecha de entrega.</li>
            <li>La garantía cubre únicamente mano de obra, no incluye repuestos ni daños por mal uso.</li>
            <li>El equipo será almacenado por un máximo de 15 días después de la notificación de finalización.</li>
            <li>Pasado este plazo, se aplicarán cargos de almacenamiento.</li>
        </ul>
    </div>
@endsection
