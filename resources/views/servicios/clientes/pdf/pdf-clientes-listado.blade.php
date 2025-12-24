@extends('layouts.pdf.plantilla')

@section('titulo', 'Clientes')

@section('contenido')
    <div class="subtitulo">Reporte de Clientes</div>

    <table class="tabla">
        <thead class="tabla-thead">
            <tr>
                <th>Tipo</th>
                <th>Documento</th>
                <th>Nombre / Razón Social</th>
                <th>Teléfono</th>
                <th>Celular</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody class="tabla-tbody">
            @forelse ($datos as $cliente)
                <tr>
                    <td>{{ $cliente->tipo_cliente === 'fisica' ? 'Física' : 'Jurídica' }}</td>
                    <td>{{ $cliente->documento ?? 'S/D' }}</td>
                    <td>{{ $cliente->nombre ?? 'S/D' }}</td>
                    <td>{{ $cliente->telefono ?? 'S/D' }}</td>
                    <td>{{ $cliente->celular ?? 'S/D' }}</td>
                    <td>{{ $cliente->email ?? 'S/D' }}</td>
                    <td>{{ $cliente->direccion ?? 'S/D' }}</td>
                    <td>{{ $cliente->activo ? 'Activo' : 'Inactivo' }}</td>
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
