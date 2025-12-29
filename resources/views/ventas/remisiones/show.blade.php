@extends('adminlte::page')

@section('title', 'Remisión ' . $remision->numero_remision)

@section('content_header')
    <h1><i class="fas fa-truck"></i> Remisión {{ $remision->numero_remision }}</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Información de la Remisión --}}
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Información General
            </h3>
            <div class="card-tools">
                <a href="{{ route('ventas.remisiones.pdf', $remision) }}"
                   class="btn btn-danger btn-sm"
                   target="_blank">
                    <i class="fas fa-file-pdf"></i> Ver PDF
                </a>
                <a href="{{ route('ventas.remisiones.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Número:</strong></div>
                        <div class="col-md-8">{{ $remision->numero_remision }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Estado:</strong></div>
                        <div class="col-md-8">
                            @switch($remision->estado)
                                @case('BORRADOR')
                                    <span class="badge badge-secondary">Borrador</span>
                                    @break
                                @case('EMITIDA')
                                    <span class="badge badge-primary">Emitida</span>
                                    @break
                                @case('EN_TRANSITO')
                                    <span class="badge badge-info">En Tránsito</span>
                                    @break
                                @case('ENTREGADA')
                                    <span class="badge badge-success">Entregada</span>
                                    @break
                                @case('ANULADA')
                                    <span class="badge badge-danger">Anulada</span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Cliente:</strong></div>
                        <div class="col-md-8">
                            {{ $remision->cliente->nombre_razon_social ?? 'N/A' }}
                            @if($remision->cliente && $remision->cliente->ruc_ci)
                                <br><small class="text-muted">RUC/CI: {{ $remision->cliente->ruc_ci }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Factura Origen:</strong></div>
                        <div class="col-md-8">
                            @if($remision->factura)
                                <a href="{{ route('ventas.facturas.show', $remision->factura) }}" target="_blank">
                                    {{ $remision->factura->numero_factura }}
                                    <i class="fas fa-external-link-alt fa-sm"></i>
                                </a>
                            @else
                                <span class="text-muted">Sin factura</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Fecha Emisión:</strong></div>
                        <div class="col-md-8">{{ $remision->fecha_emision->format('d/m/Y') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Fecha Entrega:</strong></div>
                        <div class="col-md-8">{{ $remision->fecha_entrega ? $remision->fecha_entrega->format('d/m/Y') : 'No especificada' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Sucursal:</strong></div>
                        <div class="col-md-8">{{ $remision->sucursal->nombre ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Depósito:</strong></div>
                        <div class="col-md-8">{{ $remision->deposito->nombre ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Responsable:</strong></div>
                        <div class="col-md-8">{{ $remision->responsable->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            @if($remision->direccion_entrega)
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Dirección de Entrega:</strong><br>
                        {{ $remision->direccion_entrega }}
                    </div>
                </div>
            @endif

            @if($remision->observaciones)
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Observaciones:</strong><br>
                        {{ $remision->observaciones }}
                    </div>
                </div>
            @endif

            @if($remision->estado === 'ENTREGADA')
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Recibido por:</strong> {{ $remision->receptor_nombre }}<br>
                        <strong>CI:</strong> {{ $remision->receptor_ci }}<br>
                        <strong>Fecha de Recepción:</strong> {{ $remision->fecha_recepcion ? $remision->fecha_recepcion->format('d/m/Y H:i') : 'N/A' }}
                    </div>
                </div>
            @endif

            @if($remision->estado === 'ANULADA' && $remision->motivo_anulacion)
                <hr>
                <div class="alert alert-danger">
                    <strong>Motivo de Anulación:</strong><br>
                    {{ $remision->motivo_anulacion }}
                </div>
            @endif
        </div>
    </div>

    {{-- Detalles de la Remisión --}}
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="card-title">
                <i class="fas fa-boxes"></i> Productos
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th>Descripción</th>
                            <th class="text-center" style="width: 10%">Cantidad</th>
                            <th class="text-center" style="width: 10%">Unidad</th>
                            <th class="text-right" style="width: 15%">Precio Unit.</th>
                            <th class="text-right" style="width: 15%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($remision->detalles as $index => $detalle)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $detalle->producto_descripcion }}
                                    @if($detalle->observaciones)
                                        <br><small class="text-muted">{{ $detalle->observaciones }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($detalle->cantidad, 2, ',', '.') }}</td>
                                <td class="text-center">{{ $detalle->unidad_medida }}</td>
                                <td class="text-right">₲ {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                                <td class="text-right">₲ {{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                            <td class="text-right">
                                <strong>₲ {{ number_format($remision->detalles->sum('subtotal'), 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    @if($remision->estado !== 'ANULADA')
        <div class="card">
            <div class="card-header bg-secondary">
                <h3 class="card-title">
                    <i class="fas fa-cogs"></i> Acciones
                </h3>
            </div>
            <div class="card-body">
                @if($remision->estado === 'BORRADOR')
                    <form action="{{ route('ventas.remisiones.emitir', $remision) }}" method="POST" style="display: inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="return confirm('¿Está seguro de emitir esta remisión?')">
                            <i class="fas fa-paper-plane"></i> Emitir Remisión
                        </button>
                    </form>
                @endif

                @if(in_array($remision->estado, ['EMITIDA', 'EN_TRANSITO']))
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalMarcarEntregada">
                        <i class="fas fa-check-circle"></i> Marcar como Entregada
                    </button>
                @endif

                @if($remision->estado !== 'ENTREGADA')
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalAnular">
                        <i class="fas fa-ban"></i> Anular Remisión
                    </button>
                @endif
            </div>
        </div>
    @endif
@stop

{{-- Modal Marcar Entregada --}}
<div class="modal fade" id="modalMarcarEntregada" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('ventas.remisiones.marcarEntregada', $remision) }}" method="POST">
                @csrf
                <div class="modal-header bg-success">
                    <h5 class="modal-title"><i class="fas fa-check-circle"></i> Marcar como Entregada</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="receptor_nombre">Nombre del Receptor <span class="text-danger">*</span></label>
                        <input type="text" name="receptor_nombre" id="receptor_nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="receptor_ci">CI del Receptor <span class="text-danger">*</span></label>
                        <input type="text" name="receptor_ci" id="receptor_ci" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Entrega</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Anular --}}
<div class="modal fade" id="modalAnular" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('ventas.remisiones.anular', $remision) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger">
                    <h5 class="modal-title"><i class="fas fa-ban"></i> Anular Remisión</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="motivo_anulacion">Motivo de Anulación <span class="text-danger">*</span></label>
                        <textarea name="motivo_anulacion" id="motivo_anulacion" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Esta acción no se puede deshacer.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Anulación</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('css')
    <style>
        .table-sm td, .table-sm th {
            font-size: 0.875rem;
        }
    </style>
@stop
