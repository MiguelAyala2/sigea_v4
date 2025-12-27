@extends('layouts.pdf.plantilla')

@section('titulo', 'Informe de Solicitudes de Servicio')

@section('departamento')
    <strong style="font-size: 16px;">DEPARTAMENTO DE SERVICIOS TÉCNICOS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 15px;">
        <h2 style="margin: 10px 0; font-size: 18px;">INFORME DE SOLICITUDES DE SERVICIO</h2>
        <p style="margin: 5px 0; font-size: 12px;">
            Período: {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}
        </p>
    </div>

    @if($solicitudes->isEmpty())
        <div style="text-align: center; padding: 30px; background-color: #f8f9fa; border-radius: 5px;">
            <p style="font-size: 12px; color: #6c757d; margin: 0;">No se encontraron solicitudes en el período seleccionado.</p>
        </div>
    @else
        <!-- Resumen -->
        <div style="background-color: #e8f5e9; padding: 10px; margin-bottom: 15px; border-left: 4px solid #4CAF50;">
            <table style="width: 100%; font-size: 11px;">
                <tr>
                    <td><strong>Total de Solicitudes:</strong> {{ $solicitudes->count() }}</td>
                    <td><strong>Pendientes:</strong> {{ $solicitudes->where('estado', 'pendiente')->count() }}</td>
                    <td><strong>En Proceso:</strong> {{ $solicitudes->where('estado', 'en_proceso')->count() }}</td>
                    <td><strong>Completadas:</strong> {{ $solicitudes->where('estado', 'completado')->count() }}</td>
                </tr>
            </table>
        </div>

        <!-- Tabla de Solicitudes -->
        <table style="width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #4CAF50; color: white;">
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Código</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Fecha</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Cliente</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Producto</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Tipo</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: center;">Estado</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: center;">Prioridad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitudes as $solicitud)
                    <tr>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $solicitud->numero_solicitud }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $solicitud->fecha->format('d/m/Y') }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $solicitud->cliente->nombre ?? 'N/A' }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $solicitud->producto->nombre ?? 'N/A' }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ ucfirst(str_replace('_', ' ', $solicitud->tipo_servicio)) }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd; text-align: center;">
                            <span style="padding: 2px 6px; border-radius: 3px; font-size: 8px;
                                background-color: {{ $solicitud->estado == 'completado' ? '#28a745' : ($solicitud->estado == 'en_proceso' ? '#ffc107' : '#6c757d') }};
                                color: white;">
                                {{ strtoupper(str_replace('_', ' ', $solicitud->estado)) }}
                            </span>
                        </td>
                        <td style="padding: 5px; border: 1px solid #ddd; text-align: center;">
                            <span style="padding: 2px 6px; border-radius: 3px; font-size: 8px;
                                background-color: {{ $solicitud->prioridad == 'alta' ? '#dc3545' : ($solicitud->prioridad == 'media' ? '#ffc107' : '#6c757d') }};
                                color: white;">
                                {{ strtoupper($solicitud->prioridad) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pie de página con estadísticas -->
        <div style="margin-top: 20px; padding-top: 10px; border-top: 2px solid #333; font-size: 10px;">
            <p style="margin: 3px 0;"><strong>Reporte generado:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            <p style="margin: 3px 0;"><strong>Usuario:</strong> {{ auth()->user()->name }}</p>
        </div>
    @endif
@endsection
