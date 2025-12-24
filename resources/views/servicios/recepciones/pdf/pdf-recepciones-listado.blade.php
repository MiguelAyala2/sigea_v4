@extends('layouts.pdf.plantilla')

@section('titulo', 'Listado de Recepciones')

@section('contenido')
    <table class="table table-bordered table-sm">
        <thead class="table-header">
            <tr>
                @foreach ($encabezados as $encabezado)
                    <th>{{ $encabezado }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $recepcion)
                <tr>
                    <td>{{ $recepcion->numero_recepcion }}</td>
                    <td>{{ $recepcion->fecha_recepcion->format('d/m/Y') }}</td>
                    <td>{{ $recepcion->solicitud->numero_solicitud ?? 'S/D' }}</td>
                    <td>{{ $recepcion->cliente->nombre ?? 'S/D' }}</td>
                    <td>{{ $recepcion->producto->nombre ?? 'S/D' }}</td>
                    <td>{{ $recepcion->tipo_equipo ?? 'S/D' }}</td>
                    <td>{{ $recepcion->marca ?? 'S/D' }}</td>
                    <td>{{ $recepcion->modelo ?? 'S/D' }}</td>
                    <td>{{ $recepcion->numero_serie ?? 'S/D' }}</td>
                    <td>
                        @if($recepcion->estado_recepcion === 'bueno')
                            Bueno
                        @elseif($recepcion->estado_recepcion === 'regular')
                            Regular
                        @else
                            Malo
                        @endif
                    </td>
                    <td>
                        @if($recepcion->estado === 'pendiente')
                            Pendiente
                        @elseif($recepcion->estado === 'en_proceso')
                            En Proceso
                        @else
                            Completado
                        @endif
                    </td>
                    <td style="font-size: 8px;">{{ $recepcion->descripcion_problema }}</td>
                    <td>{{ $recepcion->activo ? 'Activo' : 'Inactivo' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
