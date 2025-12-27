@extends('adminlte::page')

@section('title', 'Editar Orden de Servicio')

@section('content_header')
    <h1><i class="fas fa-tools"></i> Orden de Servicio {{ $orden->codigo }}</h1>
@stop

@section('content')
    <div class="row">
        <!-- Información General -->
        <div class="col-md-12">
            <x-adminlte-card theme="success" icon="fas fa-info-circle" title="Información General">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th width="40%">Código Orden:</th>
                                <td><strong>{{ $orden->codigo }}</strong></td>
                            </tr>
                            <tr>
                                <th>Presupuesto:</th>
                                <td>{{ $orden->presupuesto->codigo }}</td>
                            </tr>
                            <tr>
                                <th>Fecha Orden:</th>
                                <td>{{ $orden->fecha_orden->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>{!! $orden->estado_badge !!}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th width="40%">Cliente:</th>
                                <td>{{ $orden->cliente }}</td>
                            </tr>
                            <tr>
                                <th>Equipo:</th>
                                <td>{{ $orden->equipo }}</td>
                            </tr>
                            <tr>
                                <th>Técnico Asignado:</th>
                                <td>{{ $orden->tecnico_nombre }}</td>
                            </tr>
                            @if($orden->fecha_inicio)
                            <tr>
                                <th>Fecha Inicio:</th>
                                <td>{{ $orden->fecha_inicio->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <a href="{{ route('servicios.ordenes.imprimir-orden', $orden->id) }}"
                           class="btn btn-danger" target="_blank">
                            <i class="fas fa-file-pdf"></i> Imprimir Orden de Trabajo
                        </a>
                        <a href="{{ route('servicios.ordenes.imprimir-contrato', $orden->id) }}"
                           class="btn btn-primary" target="_blank">
                            <i class="fas fa-file-contract"></i> Imprimir Contrato
                        </a>
                        <a href="{{ route('servicios.ordenes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    <!-- Servicios a Realizar -->
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card theme="primary" icon="fas fa-wrench" title="Tipos de Servicio">
                <table class="table table-sm table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Código</th>
                            <th>Tipo de Servicio</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">Precio Unitario</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orden->presupuesto->diagnostico->tiposServicio as $servicio)
                        <tr>
                            <td>{{ $servicio->tipoServicio->codigo ?? 'N/A' }}</td>
                            <td>{{ $servicio->tipoServicio->nombre ?? 'N/A' }}</td>
                            <td class="text-right">{{ $servicio->cantidad }}</td>
                            <td class="text-right">₲ {{ number_format($servicio->costo_unitario, 0, ',', '.') }}</td>
                            <td class="text-right">₲ {{ number_format($servicio->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay servicios definidos</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">SUBTOTAL SERVICIOS:</td>
                            <td class="text-right">₲ {{ number_format($orden->presupuesto->subtotal_servicios, 0, ',', '.') }}</td>
                        </tr>
                        @if($orden->presupuesto->descuento_promocion > 0)
                        <tr class="text-danger">
                            <td colspan="4" class="text-right">Descuento Promoción:</td>
                            <td class="text-right">- ₲ {{ number_format($orden->presupuesto->descuento_promocion, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="font-weight-bold bg-light">
                            <td colspan="4" class="text-right">TOTAL SERVICIOS:</td>
                            <td class="text-right">₲ {{ number_format($orden->presupuesto->total_servicios, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-adminlte-card>
        </div>
    </div>

    <!-- Repuestos Necesarios -->
    @if($orden->presupuesto->diagnostico->repuestos->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card theme="warning" icon="fas fa-cogs" title="Repuestos Necesarios">
                <table class="table table-sm table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Código</th>
                            <th>Repuesto</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">Precio Unitario</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orden->presupuesto->diagnostico->repuestos as $repuesto)
                        <tr>
                            <td>{{ $repuesto->producto->codigo }}</td>
                            <td>{{ $repuesto->producto->nombre }}</td>
                            <td class="text-right">{{ $repuesto->cantidad }}</td>
                            <td class="text-right">₲ {{ number_format($repuesto->costo, 0, ',', '.') }}</td>
                            <td class="text-right">₲ {{ number_format($repuesto->cantidad * $repuesto->costo, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">SUBTOTAL REPUESTOS:</td>
                            <td class="text-right">₲ {{ number_format($orden->presupuesto->subtotal_repuestos, 0, ',', '.') }}</td>
                        </tr>
                        @if($orden->presupuesto->descuento_descuento > 0)
                        <tr class="text-danger">
                            <td colspan="4" class="text-right">Descuento Aplicado:</td>
                            <td class="text-right">- ₲ {{ number_format($orden->presupuesto->descuento_descuento, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="font-weight-bold bg-light">
                            <td colspan="4" class="text-right">TOTAL REPUESTOS:</td>
                            <td class="text-right">₲ {{ number_format($orden->presupuesto->total_repuestos, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-adminlte-card>
        </div>
    </div>
    @endif

    <!-- Total General -->
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card theme="success">
                <table class="table table-sm table-bordered mb-0">
                    <tr class="bg-success text-white">
                        <td class="text-right font-weight-bold" style="font-size: 16px;">TOTAL GENERAL:</td>
                        <td class="text-right font-weight-bold" style="font-size: 18px; width: 200px;">₲ {{ number_format($orden->presupuesto->monto_total, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </x-adminlte-card>
        </div>
    </div>
@stop
