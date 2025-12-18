<div>
    @if($compra)
        {{-- Header --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-project-diagram mr-2"></i>
                        Flujo de Compra #{{ $compra->numero_factura }}
                    </h5>
                    <div class="badge badge-{{ $compra->estado == 'APROBADA' ? 'success' : 'warning' }}">
                        {{ $compra->estado_texto }}
                    </div>
                </div>
                <small class="text-muted">
                    Proveedor: {{ $compra->proveedor->razon_social ?? 'N/A' }} | 
                    Total: Gs. {{ number_format($compra->total, 0, ',', '.') }}
                </small>
            </div>
        </div>

        {{-- Barra de Progreso --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Progreso total del flujo</span>
                    <span class="font-weight-bold">{{ $progresoTotal }}%</span>
                </div>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                         role="progressbar" 
                         style="width: {{ $progresoTotal }}%"
                         aria-valuenow="{{ $progresoTotal }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        {{ $progresoTotal }}%
                    </div>
                </div>
            </div>
        </div>

        {{-- Etapas del Flujo --}}
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-list-ol mr-2"></i> Etapas del Proceso</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($estadosFlujo as $index => $etapa)
                        <div class="timeline-item {{ $etapa['estado'] == 'completado' ? 'completed' : ($etapa['estado'] == 'actual' ? 'current' : 'pending') }}">
                            <div class="timeline-icon bg-{{ $etapa['color'] }}">
                                <i class="{{ $etapa['icono'] }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">{{ $etapa['nombre'] }}</h6>
                                    @if($etapa['estado'] == 'completado')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i> Completado
                                        </span>
                                    @elseif($etapa['estado'] == 'actual')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-spinner mr-1"></i> En Proceso
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-clock mr-1"></i> Pendiente
                                        </span>
                                    @endif
                                </div>
                                @if($etapa['fecha'])
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ $etapa['fecha'] }}
                                    </small>
                                @endif
                                
                                {{-- Acciones específicas por etapa --}}
                                @if($etapa['estado'] == 'actual' || $etapa['estado'] == 'pendiente')
                                    <div class="mt-2">
                                        @switch($etapa['nombre'])
                                            @case('Pedido de Compra')
                                                <a href="{{ route('compras.pedidos.index') }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-plus mr-1"></i> Crear Pedido
                                                </a>
                                                @break
                                            @case('Recepción')
                                                @if(!$compra->recepcion_completa)
                                                    <a href="{{ route('compras.recepciones.create', ['compra_id' => $compra->id]) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-box-open mr-1"></i> Registrar Recepción
                                                    </a>
                                                @endif
                                                @break
                                            @case('Pago')
                                                @if(!$compra->estaPagada())
                                                    <a href="{{ route('compras.pagos.index') }}"
                                                       class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-money-bill-wave mr-1"></i> Registrar Pago
                                                    </a>
                                                @endif
                                                @break
                                        @endswitch
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Documentos Relacionados --}}
        @if(count($documentosRelacionados) > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-link mr-2"></i> Documentos Relacionados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Origen</th>
                                    <th>Relación</th>
                                    <th>Destino</th>
                                    <th>Completado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documentosRelacionados as $doc)
                                    <tr>
                                        <td>
                                            <small class="text-muted">{{ $doc['origen'] }}</small><br>
                                            <strong>#{{ $doc['origen_id'] }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $doc['relacion'] }}</span><br>
                                            <small>{{ $doc['porcentaje'] }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $doc['destino'] }}</small><br>
                                            <strong>#{{ $doc['destino_id'] }}</strong>
                                        </td>
                                        <td>
                                            @if($doc['es_completa'])
                                                <span class="badge badge-success">Completo</span>
                                            @else
                                                <span class="badge badge-warning">Parcial</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $doc['fecha'] }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- Selector de Compra --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-search mr-2"></i> Seleccionar Compra</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <x-adminlte-select2 
                            name="compra_id" 
                            wire:model.live="compraId"
                            label="Seleccione una compra"
                            placeholder="Busque por número de factura o proveedor...">
                            <option value="">-- Seleccione una compra --</option>
                            @foreach(\App\Models\Compras\Compra::orderBy('fecha_emision', 'desc')->limit(100)->get() as $c)
                                <option value="{{ $c->id }}">
                                    #{{ $c->numero_factura }} - 
                                    {{ $c->proveedor->razon_social ?? 'Sin proveedor' }} - 
                                    Gs. {{ number_format($c->total, 0, ',', '.') }} - 
                                    {{ $c->fecha_emision->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary btn-block" 
                                onclick="location.href='{{ route('compras.compras.index') }}'">
                            <i class="fas fa-list mr-1"></i> Ver todas las compras
                        </button>
                    </div>
                </div>
                
                @if($compraId)
                    <div class="mt-3 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2">Cargando información de la compra...</p>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-project-diagram fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Visualización del Flujo de Compra</h5>
                        <p class="text-muted">Seleccione una compra para ver su flujo completo</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-icon {
            position: absolute;
            left: -30px;
            top: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 1;
        }
        .timeline-content {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        .timeline-item.completed .timeline-content {
            border-left: 4px solid #28a745;
        }
        .timeline-item.current .timeline-content {
            border-left: 4px solid #007bff;
            background: #f8f9fa;
        }
        .timeline-item.pending .timeline-content {
            border-left: 4px solid #6c757d;
            opacity: 0.8;
        }
    </style>
@endpush
