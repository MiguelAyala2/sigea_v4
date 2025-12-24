@extends('layouts.pdf.plantilla')

@section('titulo', 'Solicitudes de Servicio')

@section('contenido')
    <div class="subtitulo">Reporte de Solicitudes de Servicio</div>

    <table class="tabla">
        <thead class="tabla-thead">
            <tr>
                <th>N° Solicitud</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Equipo/Producto</th>
                <th>Tipo Servicio</th>
                <th>Prioridad</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody class="tabla-tbody">
            @forelse ($datos as $solicitud)
                <tr>
                    <td>{{ $solicitud->numero_solicitud ?? 'S/D' }}</td>
                    <td>{{ $solicitud->fecha->format('d/m/Y') ?? 'S/D' }}</td>
                    <td>{{ $solicitud->cliente->nombre ?? 'S/D' }}</td>
                    <td>{{ $solicitud->producto->nombre ?? 'S/D' }}</td>
                    <td>
                        @if($solicitud->tipo_servicio === 'mantenimiento')
                            Mantenimiento
                        @elseif($solicitud->tipo_servicio === 'reparacion')
                            Reparación
                        @else
                            Diagnóstico
                        @endif
                    </td>
                    <td>
                        @if($solicitud->prioridad === 'alta')
                            Alta
                        @elseif($solicitud->prioridad === 'media')
                            Media
                        @else
                            Baja
                        @endif
                    </td>
                    <td>
                        @if($solicitud->estado === 'completado')
                            Completado
                        @elseif($solicitud->estado === 'en_proceso')
                            En Proceso
                        @else
                            Pendiente
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="100%" style="font-style: italic; text-align: center">SIN REGISTROS</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection

@push('styles')
@endpush
