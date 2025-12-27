@extends('layouts.pdf.plantilla')

@section('titulo', 'Contrato de Servicio')

@section('departamento')
    <strong style="font-size: 16px;">DEPARTAMENTO DE SERVICIOS TÉCNICOS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 10px 0; font-size: 18px; text-transform: uppercase;">CONTRATO DE SERVICIO TÉCNICO</h2>
        <h3 style="margin: 5px 0; font-size: 14px;">Orden N°: {{ $orden->codigo }}</h3>
    </div>

    <!-- Partes del Contrato -->
    <div style="margin-bottom: 15px; font-size: 11px;">
        <p style="text-align: justify; line-height: 1.6;">
            Entre <strong>{{ $empresa->razon_social }}</strong>, con RUC N° <strong>{{ $empresa->ruc }}</strong>,
            con domicilio en <strong>{{ $empresa->direccion ?? 'N/A' }}</strong>, en adelante denominado
            <strong>"EL PRESTADOR"</strong>, y <strong>{{ $orden->presupuesto->diagnostico->recepcion->solicitud->cliente->nombre }}</strong>,
            con RUC/CI N° <strong>{{ $orden->presupuesto->diagnostico->recepcion->solicitud->cliente->documento ?? 'N/A' }}</strong>,
            en adelante denominado <strong>"EL CLIENTE"</strong>, se acuerda celebrar el presente contrato de servicio técnico
            sujeto a las siguientes cláusulas:
        </p>
    </div>

    <!-- Equipo a Reparar -->
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
                    <tr>
                        <td style="padding: 3px 5px;"><strong>Fecha Recepción:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->presupuesto->diagnostico->recepcion->fecha_recepcion->format('d/m/Y') }}</td>
                        <td style="padding: 3px 5px;"><strong>Fecha Contrato:</strong></td>
                        <td style="padding: 3px 5px;">{{ $orden->fecha_orden->format('d/m/Y') }}</td>
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

    <!-- Cláusula 1: Servicios a Realizar -->
    <div style="margin-bottom: 15px;">
        <h4 style="margin: 10px 0; font-size: 12px; font-weight: bold;">CLÁUSULA PRIMERA: SERVICIOS A REALIZAR</h4>
        <p style="font-size: 11px; text-align: justify; line-height: 1.5;">
            EL PRESTADOR se compromete a realizar los siguientes servicios técnicos:
        </p>

        <table style="width: 100%; margin-top: 10px; border-collapse: collapse; border: 1px solid #dee2e6;">
            <thead>
                <tr style="background-color: #28a745; color: white;">
                    <th style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6; text-align: left; width: 80px;">CÓDIGO</th>
                    <th style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6; text-align: left;">TIPO DE SERVICIO</th>
                    <th style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6; text-align: center; width: 60px;">CANT.</th>
                    <th style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6; text-align: right; width: 100px;">P. UNIT.</th>
                    <th style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6; text-align: right; width: 100px;">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orden->presupuesto->diagnostico->tiposServicio as $servicio)
                <tr>
                    <td style="padding: 4px; font-size: 10px; border: 1px solid #dee2e6;">{{ $servicio->tipoServicio->codigo ?? 'N/A' }}</td>
                    <td style="padding: 4px; font-size: 10px; border: 1px solid #dee2e6;">{{ $servicio->tipoServicio->nombre ?? 'N/A' }}</td>
                    <td style="padding: 4px; font-size: 10px; text-align: center; border: 1px solid #dee2e6;">{{ $servicio->cantidad }}</td>
                    <td style="padding: 4px; font-size: 10px; text-align: right; border: 1px solid #dee2e6;">Gs. {{ number_format($servicio->costo_unitario, 0, ',', '.') }}</td>
                    <td style="padding: 4px; font-size: 10px; text-align: right; border: 1px solid #dee2e6;">Gs. {{ number_format($servicio->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="padding: 4px; font-size: 10px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">SUBTOTAL SERVICIOS:</td>
                    <td style="padding: 4px; font-size: 10px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">Gs. {{ number_format($orden->presupuesto->subtotal_servicios, 0, ',', '.') }}</td>
                </tr>
                @if($orden->presupuesto->descuento_promocion > 0)
                <tr>
                    <td colspan="4" style="padding: 4px; font-size: 10px; text-align: right; border: 1px solid #dee2e6;">Descuento Promoción:</td>
                    <td style="padding: 4px; font-size: 10px; text-align: right; color: #dc3545; border: 1px solid #dee2e6;">- Gs. {{ number_format($orden->presupuesto->descuento_promocion, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr style="background-color: #f8f9fa;">
                    <td colspan="4" style="padding: 4px; font-size: 11px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">TOTAL SERVICIOS:</td>
                    <td style="padding: 4px; font-size: 11px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">Gs. {{ number_format($orden->presupuesto->total_servicios, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Cláusula 2: Repuestos -->
    @if($orden->presupuesto->diagnostico->repuestos->count() > 0)
    <div style="margin-bottom: 15px;">
        <h4 style="margin: 10px 0; font-size: 12px; font-weight: bold;">CLÁUSULA SEGUNDA: REPUESTOS A UTILIZAR</h4>
        <p style="font-size: 11px; text-align: justify; line-height: 1.5;">
            Para la realización de los servicios, se utilizarán los siguientes repuestos, que forman parte integral de este contrato:
        </p>

        <table style="width: 100%; margin-top: 10px; border-collapse: collapse; border: 1px solid #dee2e6;">
            <thead>
                <tr style="background-color: #ffc107; color: #000;">
                    <th style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6; text-align: left; width: 80px;">CÓDIGO</th>
                    <th style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6; text-align: left;">REPUESTO</th>
                    <th style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6; text-align: center; width: 60px;">CANT.</th>
                    <th style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6; text-align: right; width: 100px;">P. UNIT.</th>
                    <th style="padding: 5px; font-size: 10px; border: 1px solid #dee2e6; text-align: right; width: 100px;">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orden->presupuesto->diagnostico->repuestos as $repuesto)
                <tr>
                    <td style="padding: 4px; font-size: 10px; border: 1px solid #dee2e6;">{{ $repuesto->producto->codigo }}</td>
                    <td style="padding: 4px; font-size: 10px; border: 1px solid #dee2e6;">{{ $repuesto->producto->nombre }}</td>
                    <td style="padding: 4px; font-size: 10px; text-align: center; border: 1px solid #dee2e6;">{{ $repuesto->cantidad }}</td>
                    <td style="padding: 4px; font-size: 10px; text-align: right; border: 1px solid #dee2e6;">Gs. {{ number_format($repuesto->costo, 0, ',', '.') }}</td>
                    <td style="padding: 4px; font-size: 10px; text-align: right; border: 1px solid #dee2e6;">Gs. {{ number_format($repuesto->cantidad * $repuesto->costo, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="padding: 4px; font-size: 10px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">SUBTOTAL REPUESTOS:</td>
                    <td style="padding: 4px; font-size: 10px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">Gs. {{ number_format($orden->presupuesto->subtotal_repuestos, 0, ',', '.') }}</td>
                </tr>
                @if($orden->presupuesto->descuento_descuento > 0)
                <tr>
                    <td colspan="4" style="padding: 4px; font-size: 10px; text-align: right; border: 1px solid #dee2e6;">Descuento Aplicado:</td>
                    <td style="padding: 4px; font-size: 10px; text-align: right; color: #dc3545; border: 1px solid #dee2e6;">- Gs. {{ number_format($orden->presupuesto->descuento_descuento, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr style="background-color: #f8f9fa;">
                    <td colspan="4" style="padding: 4px; font-size: 11px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">TOTAL REPUESTOS:</td>
                    <td style="padding: 4px; font-size: 11px; text-align: right; font-weight: bold; border: 1px solid #dee2e6;">Gs. {{ number_format($orden->presupuesto->total_repuestos, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Cláusula 3: Precio -->
    <div style="margin-bottom: 15px;">
        <h4 style="margin: 10px 0; font-size: 12px; font-weight: bold;">CLÁUSULA {{ $orden->presupuesto->diagnostico->repuestos->count() > 0 ? 'TERCERA' : 'SEGUNDA' }}: PRECIO Y FORMA DE PAGO</h4>
        <p style="font-size: 11px; text-align: justify; line-height: 1.5;">
            El monto total por los servicios y repuestos detallados en este contrato es de:
        </p>

        <!-- Total General -->
        <table style="width: 100%; margin-top: 10px; border-collapse: collapse; border: 2px solid #333;">
            <tr style="background-color: #28a745; color: white;">
                <td style="padding: 10px; font-size: 14px; text-align: right; font-weight: bold;">TOTAL GENERAL:</td>
                <td style="padding: 10px; font-size: 16px; text-align: right; font-weight: bold; width: 150px;">Gs. {{ number_format($orden->presupuesto->monto_total, 0, ',', '.') }}</td>
            </tr>
        </table>

        <p style="font-size: 11px; text-align: justify; line-height: 1.5; margin-top: 10px;">
            El pago deberá realizarse al momento de la entrega del equipo.
        </p>
    </div>

    <!-- Cláusula 4: Garantía -->
    <div style="margin-bottom: 15px;">
        <h4 style="margin: 10px 0; font-size: 12px; font-weight: bold;">CLÁUSULA {{ $orden->presupuesto->diagnostico->repuestos->count() > 0 ? 'CUARTA' : 'TERCERA' }}: GARANTÍA</h4>
        <p style="font-size: 11px; text-align: justify; line-height: 1.5;">
            EL PRESTADOR garantiza los trabajos realizados por un período de <strong>treinta (30) días calendario</strong> a partir de la
            fecha de entrega del equipo. Esta garantía cubre únicamente la mano de obra del servicio prestado.
        </p>
        <p style="font-size: 11px; text-align: justify; line-height: 1.5; margin-top: 5px;">
            La garantía <strong>NO cubre</strong>:
        </p>
        <ul style="font-size: 11px; margin: 5px 0; padding-left: 20px; line-height: 1.5;">
            <li>Daños causados por mal uso, negligencia o accidentes.</li>
            <li>Modificaciones realizadas por terceros no autorizados.</li>
            <li>Daños por condiciones ambientales adversas (humedad, temperatura, etc.).</li>
            <li>Desgaste natural de los componentes.</li>
        </ul>
    </div>

    <!-- Cláusula 5: Retiro del Equipo -->
    <div style="margin-bottom: 15px;">
        <h4 style="margin: 10px 0; font-size: 12px; font-weight: bold;">CLÁUSULA {{ $orden->presupuesto->diagnostico->repuestos->count() > 0 ? 'QUINTA' : 'CUARTA' }}: RETIRO DEL EQUIPO</h4>
        <p style="font-size: 11px; text-align: justify; line-height: 1.5;">
            EL CLIENTE se compromete a retirar el equipo dentro de los <strong>quince (15) días hábiles</strong> posteriores a la
            notificación de finalización del servicio. Pasado este plazo, EL PRESTADOR se reserva el derecho de aplicar un cargo por
            almacenamiento equivalente al <strong>5% del valor del servicio por cada día de retraso</strong>.
        </p>
        <p style="font-size: 11px; text-align: justify; line-height: 1.5; margin-top: 5px;">
            Después de <strong>sesenta (60) días</strong> sin que el equipo sea retirado, EL PRESTADOR podrá disponer del mismo
            conforme a la legislación vigente.
        </p>
    </div>

    <!-- Cláusula 6: Aceptación -->
    <div style="margin-bottom: 20px;">
        <h4 style="margin: 10px 0; font-size: 12px; font-weight: bold;">CLÁUSULA {{ $orden->presupuesto->diagnostico->repuestos->count() > 0 ? 'SEXTA' : 'QUINTA' }}: ACEPTACIÓN</h4>
        <p style="font-size: 11px; text-align: justify; line-height: 1.5;">
            Ambas partes declaran haber leído y aceptado todas las cláusulas del presente contrato, firmando en señal de conformidad.
        </p>
    </div>

    <!-- Lugar y Fecha -->
    <div style="margin-top: 30px; margin-bottom: 40px; font-size: 11px;">
        <p>{{ $empresa->ciudad ?? 'Asunción' }}, {{ $orden->fecha_orden->format('d') }} de {{ $orden->fecha_orden->locale('es')->translatedFormat('F') }} de {{ $orden->fecha_orden->format('Y') }}</p>
    </div>

    <!-- Firmas -->
    <table style="width: 100%; margin-top: 50px;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                <div style="border-top: 1px solid #000; padding-top: 5px; margin: 0 30px;">
                    <strong style="font-size: 11px;">EL PRESTADOR</strong><br>
                    <span style="font-size: 10px;">{{ $empresa->razon_social }}</span><br>
                    <span style="font-size: 10px;">RUC: {{ $empresa->ruc }}</span>
                </div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                <div style="border-top: 1px solid #000; padding-top: 5px; margin: 0 30px;">
                    <strong style="font-size: 11px;">EL CLIENTE</strong><br>
                    <span style="font-size: 10px;">{{ $orden->presupuesto->diagnostico->recepcion->solicitud->cliente->nombre }}</span><br>
                    <span style="font-size: 10px;">RUC/CI: {{ $orden->presupuesto->diagnostico->recepcion->solicitud->cliente->documento ?? 'N/A' }}</span>
                </div>
            </td>
        </tr>
    </table>
@endsection
