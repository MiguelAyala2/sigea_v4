@extends('layouts.pdf.plantilla')

@section('titulo', 'Informe de Presupuestos')

@section('departamento')
    <strong style="font-size: 16px;">DEPARTAMENTO DE SERVICIOS TÉCNICOS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 15px;">
        <h2 style="margin: 10px 0; font-size: 18px;">INFORME DE PRESUPUESTOS</h2>
        <p style="margin: 5px 0; font-size: 12px;">
            Período: {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}
        </p>
    </div>

    @if($presupuestos->isEmpty())
        <div style="text-align: center; padding: 30px; background-color: #f8f9fa; border-radius: 5px;">
            <p style="font-size: 12px; color: #6c757d; margin: 0;">No se encontraron presupuestos en el período seleccionado.</p>
        </div>
    @else
        <!-- Resumen -->
        <div style="background-color: #e3f2fd; padding: 10px; margin-bottom: 15px; border-left: 4px solid #2196F3;">
            <table style="width: 100%; font-size: 11px;">
                <tr>
                    <td><strong>Total de Presupuestos:</strong> {{ $presupuestos->count() }}</td>
                    <td><strong>Pendientes:</strong> {{ $presupuestos->where('estado', 'pendiente_aprobacion')->count() }}</td>
                    <td><strong>Aprobados:</strong> {{ $presupuestos->where('estado', 'aprobado')->count() }}</td>
                    <td><strong>Rechazados:</strong> {{ $presupuestos->where('estado', 'rechazado')->count() }}</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <strong>Monto Total:</strong> ₲ {{ number_format($presupuestos->sum('monto_total'), 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabla de Presupuestos -->
        <table style="width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #2196F3; color: white;">
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Código</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Fecha</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: left;">Cliente</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: right;">Servicios</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: right;">Repuestos</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: right;">Total</th>
                    <th style="padding: 6px; border: 1px solid #ddd; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($presupuestos as $presupuesto)
                    <tr>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $presupuesto->codigo }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $presupuesto->fecha_presupuesto->format('d/m/Y') }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd;">{{ $presupuesto->cliente ?? 'N/A' }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd; text-align: right;">₲ {{ number_format($presupuesto->total_servicios ?? 0, 0, ',', '.') }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd; text-align: right;">₲ {{ number_format($presupuesto->total_repuestos ?? 0, 0, ',', '.') }}</td>
                        <td style="padding: 5px; border: 1px solid #ddd; text-align: right;"><strong>₲ {{ number_format($presupuesto->monto_total ?? 0, 0, ',', '.') }}</strong></td>
                        <td style="padding: 5px; border: 1px solid #ddd; text-align: center;">
                            <span style="padding: 2px 6px; border-radius: 3px; font-size: 8px;
                                background-color: {{ $presupuesto->estado == 'aprobado' ? '#28a745' : ($presupuesto->estado == 'pendiente_aprobacion' ? '#ffc107' : '#dc3545') }};
                                color: white;">
                                {{ strtoupper(str_replace('_', ' ', $presupuesto->estado)) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="5" style="padding: 6px; border: 1px solid #ddd; text-align: right;">TOTAL GENERAL:</td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">₲ {{ number_format($presupuestos->sum('monto_total'), 0, ',', '.') }}</td>
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
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
