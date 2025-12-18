<div>
    {{-- Mensajes Flash --}}
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="¡Éxito!" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="light" title="Recepción de Mercadería" icon="fas fa-box-open">
        {{-- Filtros --}}
        <div class="row mb-3">
            {{-- Búsqueda General --}}
            <div class="col-md-3 mb-2">
                <x-adminlte-input name="buscador" wire:model.live.debounce.300ms="buscador"
                    placeholder="N° remisión, guía, factura..."
                    igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            {{-- Estado --}}
            <div class="col-md-2 mb-2">
                <x-adminlte-select name="estado" wire:model.live="estado"
                    igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los estados</option>
                    @foreach(App\Models\Compras\CompraRecepcion::ESTADOS as $key => $valor)
                        <option value="{{ $key }}">{{ $valor }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>

            {{-- Depósito --}}
            <div class="col-md-3 mb-2">
                <x-adminlte-select2 name="deposito_id" wire:model.live="deposito_id"
                    igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los depósitos</option>
                    @foreach($depositos as $deposito)
                        <option value="{{ $deposito->id }}">
                            {{ $deposito->sucursal->nombre ?? 'N/A' }} / {{ $deposito->nombre }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            {{-- Fechas --}}
            <div class="col-md-4 mb-2">
                <div class="row">
                    <div class="col-6">
                        <x-adminlte-input-date name="fecha_desde" wire:model.live="fecha_desde"
                            placeholder="Desde" igroup-size="sm" fgroup-class="mb-0">
                            <x-slot name="appendSlot">
                                <div class="input-group-text bg-secondary">
                                    <i class="fas fa-calendar"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-input-date>
                    </div>
                    <div class="col-6">
                        <x-adminlte-input-date name="fecha_hasta" wire:model.live="fecha_hasta"
                            placeholder="Hasta" igroup-size="sm" fgroup-class="mb-0">
                            <x-slot name="appendSlot">
                                <div class="input-group-text bg-secondary">
                                    <i class="fas fa-calendar"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-input-date>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumen y Acciones --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="small text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Mostrando {{ $recepciones->firstItem() ?? 0 }} a {{ $recepciones->lastItem() ?? 0 }} 
                    de {{ $recepciones->total() }} recepciones
                </div>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('compras.recepciones.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus mr-1"></i> Nueva Recepción
                </a>
            </div>
        </div>

        {{-- Tabla de Recepciones --}}
        @if($recepciones->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 8%">ID</th>
                            <th style="width: 12%">Factura</th>
                            <th style="width: 20%">Proveedor</th>
                            <th style="width: 15%">Depósito</th>
                            <th style="width: 10%">Fecha Recep.</th>
                            <th style="width: 10%">Estado</th>
                            <th style="width: 10%">Documentos</th>
                            <th style="width: 15%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recepciones as $recepcion)
                            <tr class="{{ $recepcion->estado == 'COMPLETA' ? 'table-success' : ($recepcion->estado == 'PARCIAL' ? 'table-info' : 'table-warning') }}">
                                {{-- ID --}}
                                <td>
                                    <small class="text-muted">#{{ $recepcion->id }}</small>
                                </td>

                                {{-- Factura --}}
                                <td>
                                    @if($recepcion->compra)
                                        <a href="{{ route('compras.compras.show', $recepcion->compra) }}" 
                                           class="text-primary" title="Ver compra">
                                            <strong>{{ $recepcion->compra->numero_factura }}</strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">
                                            Gs. {{ number_format($recepcion->compra->total ?? 0, 0, ',', '.') }}
                                        </small>
                                    @else
                                        <span class="text-danger">Compra no encontrada</span>
                                    @endif
                                </td>

                                {{-- Proveedor --}}
                                <td>
                                    @if($recepcion->compra && $recepcion->compra->proveedor)
                                        <strong>{{ $recepcion->compra->proveedor->razon_social }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            RUC: {{ $recepcion->compra->proveedor->ruc }}
                                        </small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                {{-- Depósito --}}
                                <td>
                                    @if($recepcion->deposito)
                                        <strong>{{ $recepcion->deposito->nombre }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $recepcion->deposito->sucursal->nombre ?? 'N/A' }}
                                        </small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                {{-- Fecha Recepción --}}
                                <td>
                                    {{ $recepcion->fecha_recepcion_formateada }}
                                    <br>
                                    @if($recepcion->fecha_remision)
                                        <small class="text-muted">
                                            Rem: {{ $recepcion->fecha_remision_formateada }}
                                        </small>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td>
                                    @php
                                        $badgeClass = match($recepcion->estado) {
                                            'COMPLETA' => 'success',
                                            'PARCIAL' => 'warning',
                                            'PENDIENTE' => 'danger',
                                            'RECHAZADA' => 'dark',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">
                                        {{ $recepcion->estado_texto }}
                                    </span>
                                    @if($recepcion->estado == 'PARCIAL')
                                        <br>
                                        <small class="text-muted">
                                            {{ $recepcion->porcentaje_recibido_formateado }}
                                        </small>
                                    @endif
                                </td>

                                {{-- Documentos --}}
                                <td>
                                    @if($recepcion->numero_remision)
                                        <small>
                                            <i class="fas fa-file-alt mr-1"></i>
                                            {{ $recepcion->numero_remision }}
                                        </small>
                                        <br>
                                    @endif
                                    @if($recepcion->guia_transporte)
                                        <small>
                                            <i class="fas fa-truck mr-1"></i>
                                            {{ $recepcion->guia_transporte }}
                                        </small>
                                    @endif
                                    @if(!$recepcion->numero_remision && !$recepcion->guia_transporte)
                                        <small class="text-muted">Sin documentos</small>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        {{-- Ver --}}
                                        <a href="{{ route('compras.recepciones.show', $recepcion) }}"
                                           class="btn btn-info" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Menú desplegable --}}
                                        <div class="btn-group ml-1">
                                            <button type="button" class="btn btn-secondary dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                {{-- Completar --}}
                                                @if($recepcion->estado != 'COMPLETA')
                                                    <a class="dropdown-item" href="#"
                                                       wire:click="marcarComoCompleta({{ $recepcion->id }})"
                                                       wire:confirm="¿Marcar como recepción completa?">
                                                        <i class="fas fa-check-circle mr-2"></i> Completar Recepción
                                                    </a>
                                                @endif

                                                {{-- Ver Compra --}}
                                                @if($recepcion->compra)
                                                    <a class="dropdown-item"
                                                       href="{{ route('compras.compras.show', $recepcion->compra) }}">
                                                        <i class="fas fa-file-invoice mr-2"></i> Ver Compra
                                                    </a>
                                                @endif

                                                {{-- Ver Flujo --}}
                                                @if($recepcion->compra)
                                                    <a class="dropdown-item"
                                                       href="{{ route('compras.dashboard.flujo.show', $recepcion->compra) }}">
                                                        <i class="fas fa-project-diagram mr-2"></i> Ver Flujo
                                                    </a>
                                                @endif

                                                <div class="dropdown-divider"></div>

                                                {{-- Estadísticas --}}
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-chart-bar mr-2"></i> Estadísticas
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <select class="form-control form-control-sm" style="width: 70px; display:inline-block;"
                            wire:model.live="paginado">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <small class="text-muted">registros por página</small>
                </div>
                <div>
                    {{ $recepciones->links() }}
                </div>
            </div>
        @else
            {{-- Estado vacío --}}
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No hay recepciones registradas</h5>
                <p class="text-muted">
                    @if($buscador || $estado || $deposito_id || $fecha_desde || $fecha_hasta)
                        No se encontraron resultados con los filtros aplicados.
                    @else
                        Comienza registrando una nueva recepción de mercadería.
                    @endif
                </p>
                <a href="{{ route('compras.recepciones.create') }}" class="btn btn-success btn-lg">
                    <i class="fas fa-plus mr-2"></i> Registrar Primera Recepción
                </a>
            </div>
        @endif

        {{-- Resumen por Estado --}}
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $recepciones->where('estado', 'PENDIENTE')->count() }}</h3>
                        <p>Recepciones Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $recepciones->where('estado', 'PARCIAL')->count() }}</h3>
                        <p>Recepciones Parciales</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $recepciones->where('estado', 'COMPLETA')->count() }}</h3>
                        <p>Recepciones Completas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $recepciones->count() }}</h3>
                        <p>Total Recepciones</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
        </div>
    </x-adminlte-card>

    {{-- Estilos para small-box --}}
    @push('styles')
    <style>
        .small-box {
            border-radius: 5px;
            position: relative;
            display: block;
            margin-bottom: 20px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
            color: white;
        }
        .small-box>.inner {
            padding: 10px;
        }
        .small-box h3 {
            font-size: 38px;
            font-weight: bold;
            margin: 0 0 10px 0;
            white-space: nowrap;
            padding: 0;
        }
        .small-box p {
            font-size: 15px;
            margin: 0;
        }
        .small-box .icon {
            position: absolute;
            top: -10px;
            right: 10px;
            z-index: 0;
            font-size: 70px;
            color: rgba(255,255,255,0.15);
        }
    </style>
    @endpush
</div>
