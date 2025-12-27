@extends('layouts.pdf.plantilla')

@section('titulo', 'Informe de Reclamos de Clientes')

@section('departamento')
    <strong style="font-size: 16px;">DEPARTAMENTO DE SERVICIOS TÉCNICOS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 15px;">
        <h2 style="margin: 10px 0; font-size: 18px;">INFORME DE RECLAMOS DE CLIENTES</h2>
        <p style="margin: 5px 0; font-size: 12px;">
            Período: {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}
        </p>
    </div>

    @if($reclamos->isEmpty())
        <div style="text-align: center; padding: 30px; background-color: #f8f9fa; border-radius: 5px;">
            <p style="font-size: 12px; color: #6c757d; margin: 0;">No se encontraron reclamos en el período seleccionado.</p>
        </div>
    @else
        <!-- Resumen -->
        <div style="background-color: #ffebee; padding: 10px; margin-bottom: 15px; border-left: 4px solid #F44336;">
            <table style="width: 100%; font-size: 11px;">
                <tr>
                    <td><strong>Total de Reclamos:</strong> {{ $reclamos->count() }}</td>
                    <td><strong>Pendientes:</strong> {{ $reclamos->where('estado', 'pendiente')->count() }}</td>
                    <td><strong>En Proceso:</strong> {{ $reclamos->where('estado', 'en_proceso')->count() }}</td>
                    <td><strong>Resueltos:</strong> {{ $reclamos->where('estado', 'resuelto')->count() }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Alta Prioridad:</strong> {{ $reclamos->where('prioridad', 'alta')->count() + $reclamos->where('prioridad', 'urgente')->count() }}</td>
                    <td colspan="2"><strong>Cerrados:</strong> {{ $reclamos->where('estado', 'cerrado')->count() }}</td>
                </tr>
            </table>
        </div>

        <!-- Tabla de Reclamos -->
        <table style="width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #F44336; color: white;">
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Código</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Fecha</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Cliente</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Tipo</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: center;">Prioridad</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: center;">Estado</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Responsable</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reclamos as $reclamo)
                    <tr>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $reclamo->codigo }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $reclamo->fecha_reclamo->format('d/m/Y') }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $reclamo->cliente->nombre ?? 'N/A' }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $reclamo->tipo_reclamo_text }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd; text-align: center;">
                            <span style="padding: 2px 6px; border-radius: 3px; font-size: 8px;
                                background-color: {{ $reclamo->prioridad == 'urgente' ? '#dc3545' : ($reclamo->prioridad == 'alta' ? '#ff6347' : ($reclamo->prioridad == 'media' ? '#ffc107' : '#6c757d')) }};
                                color: white;">
                                {{ strtoupper($reclamo->prioridad) }}
                            </span>
                        </td>
                        <td style="padding: 5px; border: 1px solid #ddd; text-align: center;">
                            <span style="padding: 2px 6px; border-radius: 3px; font-size: 8px;
                                background-color: {{ $reclamo->estado == 'resuelto' ? '#28a745' : ($reclamo->estado == 'cerrado' ? '#007bff' : ($reclamo->estado == 'en_proceso' ? '#ffc107' : '#6c757d')) }};
                                color: white;">
                                {{ strtoupper(str_replace('_', ' ', $reclamo->estado)) }}
                            </span>
                        </td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $reclamo->responsable_nombre }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Análisis por Tipo de Reclamo -->
        <div style="margin-top: 15px; padding: 10px; background-color: #f8f9fa;">
            <h3 style="margin: 0 0 10px 0; font-size: 12px;">Distribución por Tipo de Reclamo:</h3>
            <table style="width: 100%; font-size: 10px;">
                @php
                    $tiposReclamo = $reclamos->groupBy('tipo_reclamo')->map->count()->sortDesc();
                @endphp
                @foreach($tiposReclamo as $tipo => $cantidad)
                    <tr>
                        <td style="padding: 3px 0;">{{ ucfirst(str_replace('_', ' ', $tipo)) }}:</td>
                        <td style="padding: 3px 0; text-align: right;"><strong>{{ $cantidad }}</strong> ({{ round($cantidad / $reclamos->count() * 100, 1) }}%)</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <!-- Pie de página con estadísticas -->
        <div style="margin-top: 20px; padding-top: 10px; border-top: 2px solid #333; font-size: 10px;">
            <p style="margin: 3px 0;"><strong>Reporte generado:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            <p style="margin: 3px 0;"><strong>Usuario:</strong> {{ auth()->user()->name }}</p>
        </div>
    @endif
@endsection
