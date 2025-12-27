@extends('layouts.pdf.plantilla')

@section('titulo', 'Informe de Órdenes de Servicio')

@section('departamento')
    <strong style="font-size: 16px;">DEPARTAMENTO DE SERVICIOS TÉCNICOS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 15px;">
        <h2 style="margin: 10px 0; font-size: 18px;">INFORME DE ÓRDENES DE SERVICIO</h2>
        <p style="margin: 5px 0; font-size: 12px;">
            Período: {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}
        </p>
    </div>

    @if($ordenes->isEmpty())
        <div style="text-align: center; padding: 30px; background-color: #f8f9fa; border-radius: 5px;">
            <p style="font-size: 12px; color: #6c757d; margin: 0;">No se encontraron órdenes de servicio en el período seleccionado.</p>
        </div>
    @else
        <!-- Resumen -->
        <div style="background-color: #fff3e0; padding: 10px; margin-bottom: 15px; border-left: 4px solid #FF9800;">
            <table style="width: 100%; font-size: 11px;">
                <tr>
                    <td><strong>Total de Órdenes:</strong> {{ $ordenes->count() }}</td>
                    <td><strong>Pendientes:</strong> {{ $ordenes->where('estado', 'pendiente')->count() }}</td>
                    <td><strong>En Proceso:</strong> {{ $ordenes->where('estado', 'en_proceso')->count() }}</td>
                    <td><strong>Finalizadas:</strong> {{ $ordenes->where('estado', 'finalizada')->count() }}</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <strong>Monto Total:</strong> ₲ {{ number_format($ordenes->sum(function($orden) { return $orden->presupuesto->monto_total ?? 0; }), 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabla de Órdenes -->
        <table style="width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #FF9800; color: white;">
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Código</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Fecha</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Cliente</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Equipo</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: right;">Total</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: center;">Estado</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Técnico</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ordenes as $orden)
                    <tr>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $orden->codigo }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $orden->fecha_orden->format('d/m/Y') }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $orden->cliente }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $orden->equipo }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd; text-align: right;"><strong>₲ {{ number_format($orden->presupuesto->monto_total ?? 0, 0, ',', '.') }}</strong></td>
                        <td style="padding: 5px; border: 1px solid #ddd; text-align: center;">
                            <span style="padding: 2px 6px; border-radius: 3px; font-size: 8px;
                                background-color: {{ $orden->estado == 'finalizada' ? '#28a745' : ($orden->estado == 'en_proceso' ? '#ffc107' : '#6c757d') }};
                                color: white;">
                                {{ strtoupper(str_replace('_', ' ', $orden->estado)) }}
                            </span>
                        </td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $orden->tecnico_nombre }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="4" style="padding: 6px; border: 1px solid #ddd; text-align: right;">TOTAL GENERAL:</td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">₲ {{ number_format($ordenes->sum(function($orden) { return $orden->presupuesto->monto_total ?? 0; }), 0, ',', '.') }}</td>
                    <td colspan="2" style="padding: 6px; border: 1px solid #ddd;"></td>
                </tr>
            </tfoot>
        </table>

        <!-- Pie de página con estadísticas -->
        <div style="margin-top: 20px; padding-top: 10px; border-top: 2px solid #333; font-size: 10px;">
            <p style="margin: 3px 0;"><strong>Reporte generado:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            <p style="margin: 3px 0;"><strong>Usuario:</strong> {{ auth()->user()->name }}</p>
        </div>
    @endif
@endsection
