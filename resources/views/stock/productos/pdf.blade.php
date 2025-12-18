@extends('layouts.pdf.plantilla')

@section('titulo', 'Listado de Productos')

@section('departamento')
    <strong style="font-size: 11px;">DEPARTAMENTO DE STOCK - PRODUCTOS</strong>
@endsection

@section('contenido')
    <div style="text-align: center; margin-bottom: 10px;">
        <h2 style="margin: 5px 0; font-size: 14px; text-transform: uppercase;">LISTADO DE PRODUCTOS</h2>
        <p style="margin: 3px 0; font-size: 9px; color: #666;">Catálogo Completo de Productos</p>
    </div>

    <!-- Resumen General -->
    <div style="margin-bottom: 8px; padding: 6px 10px; background-color: #17a2b8; color: white;">
        <table style="width: 100%; font-size: 10px;">
            <tr>
                <td style="width: 50%; padding: 2px;">
                    <strong style="font-size: 11px;">Total de Productos:</strong>
                    <span style="font-size: 9px;">{{ $totalProductos }} producto(s)</span>
                </td>
                <td style="width: 25%; text-align: center; padding: 2px;">
                    <strong style="font-size: 10px;">Activos:</strong>
                    <span style="font-size: 9px;">{{ $productosActivos }}</span>
                </td>
                <td style="width: 25%; text-align: right; padding: 2px;">
                    <strong style="font-size: 10px;">Inactivos:</strong>
                    <span style="font-size: 9px;">{{ $productosInactivos }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabla de Productos -->
    <table style="width: 100%; border-collapse: collapse; font-size: 8px; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #343a40; color: white;">
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; width: 8%;">Código</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; width: 25%;">Nombre</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; width: 10%;">Modelo</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; width: 12%;">Categoría</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; width: 10%;">Marca</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center; width: 6%;">U.M.</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center; width: 7%;">Tipo</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: right; width: 10%;">Precio</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center; width: 6%;">Stock</th>
                <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center; width: 6%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $producto)
            <tr style="background-color: {{ $loop->even ? '#f8f9fa' : 'white' }};">
                <td style="border: 1px solid #dee2e6; padding: 3px; font-size: 7px;">
                    {{ $producto->codigo }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px;">
                    <strong>{{ $producto->nombre }}</strong>
                    @if($producto->descripcion)
                    <br><span style="font-size: 7px; color: #666;">{{ Str::limit($producto->descripcion, 40) }}</span>
                    @endif
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; font-size: 7px;">
                    {{ $producto->modelo ?: '-' }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px;">
                    {{ $producto->categoria->nombre ?? 'N/A' }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px;">
                    {{ $producto->marca->nombre ?? 'N/A' }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center; font-size: 7px;">
                    {{ $producto->unidadMedida->simbolo ?? $producto->unidadMedida->nombre ?? '-' }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center; font-size: 7px;">
                    @switch($producto->tipo)
                        @case('PRODUCTO') Prod. @break
                        @case('INSUMO') Ins. @break
                        @case('SERVICIO') Serv. @break
                        @case('KIT') Kit @break
                        @default {{ $producto->tipo }}
                    @endswitch
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; text-align: right; font-size: 7px;">
                    @if($producto->precioActual)
                        Gs. {{ number_format($producto->precioActual->precio_venta, 0, ',', '.') }}
                    @else
                        <span style="color: #999;">S/P</span>
                    @endif
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center; font-size: 7px;">
                    @if($producto->maneja_stock)
                        @php
                            $stockTotal = $producto->stock()->sum('stock_actual') ?? 0;
                        @endphp
                        <span style="color: {{ $producto->stock_minimo && $stockTotal <= $producto->stock_minimo ? '#dc3545' : '#28a745' }};">
                            {{ number_format($stockTotal, 0) }}
                        </span>
                    @else
                        <span style="color: #999;">N/A</span>
                    @endif
                </td>
                <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center; font-size: 7px;">
                    @if($producto->activo)
                        <span style="color: #28a745; font-weight: bold;">SI</span>
                    @else
                        <span style="color: #dc3545;">NO</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="border: 1px solid #dee2e6; padding: 20px; text-align: center; color: #666;">
                    No se encontraron productos registrados
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Resumen por Tipo de Producto -->
    @if($productos->count() > 0)
    <div style="margin-top: 15px;">
        <h4 style="margin: 8px 0; font-size: 11px; border-bottom: 2px solid #333; padding-bottom: 3px;">RESUMEN POR TIPO</h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 10px;">
            <thead>
                <tr style="background-color: #343a40; color: white;">
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left;">Tipo de Producto</th>
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">Cantidad</th>
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">Activos</th>
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">Inactivos</th>
                </tr>
            </thead>
            <tbody>
                @foreach(['PRODUCTO' => 'Productos', 'INSUMO' => 'Insumos', 'SERVICIO' => 'Servicios', 'KIT' => 'Kits'] as $tipo => $label)
                    @php
                        $porTipo = $productos->where('tipo', $tipo);
                    @endphp
                    @if($porTipo->count() > 0)
                    <tr style="background-color: {{ $loop->even ? 'white' : '#f8f9fa' }};">
                        <td style="border: 1px solid #dee2e6; padding: 3px;">{{ $label }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center;">{{ $porTipo->count() }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center;">{{ $porTipo->where('activo', true)->count() }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center;">{{ $porTipo->where('activo', false)->count() }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Resumen por Categoría (Top 10) -->
    <div style="margin-top: 10px;">
        <h4 style="margin: 8px 0; font-size: 11px; border-bottom: 2px solid #333; padding-bottom: 3px;">TOP 10 CATEGORÍAS</h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 9px;">
            <thead>
                <tr style="background-color: #343a40; color: white;">
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left;">Categoría</th>
                    <th style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $categorias = $productos->groupBy('categoria_id')->map(function($items) {
                        return [
                            'nombre' => $items->first()->categoria->nombre ?? 'Sin Categoría',
                            'cantidad' => $items->count()
                        ];
                    })->sortByDesc('cantidad')->take(10);
                @endphp
                @foreach($categorias as $cat)
                <tr style="background-color: {{ $loop->even ? 'white' : '#f8f9fa' }};">
                    <td style="border: 1px solid #dee2e6; padding: 3px;">{{ $cat['nombre'] }}</td>
                    <td style="border: 1px solid #dee2e6; padding: 3px; text-align: center;">{{ $cat['cantidad'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Información Adicional -->
    <div style="margin-top: 15px; font-size: 8px; color: #666;">
        <p style="margin: 2px 0;"><strong>Total de productos:</strong> {{ $totalProductos }}</p>
        <p style="margin: 2px 0;"><strong>Productos activos:</strong> {{ $productosActivos }}</p>
        <p style="margin: 2px 0;"><strong>Productos inactivos:</strong> {{ $productosInactivos }}</p>
        <p style="margin: 2px 0;"><strong>Productos que manejan stock:</strong> {{ $productos->where('maneja_stock', true)->count() }}</p>
    </div>

    <!-- Leyenda -->
    <div style="margin-top: 15px; padding: 6px; background-color: #e9ecef; border-left: 3px solid #6c757d;">
        <p style="margin: 0; font-size: 8px; color: #495057;">
            <strong>Leyenda:</strong> U.M. = Unidad de Medida | S/P = Sin Precio | Estado: SI = Activo, NO = Inactivo
        </p>
    </div>
@endsection
