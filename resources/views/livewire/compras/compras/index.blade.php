<div>
    {{-- Mensajes Flash --}}
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="¡Éxito!" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="light" title="Compras / Facturas" icon="fas fa-file-invoice">
        {{-- Filtros --}}
        <div class="row mb-3">
            {{-- Búsqueda General --}}
            <div class="col-md-3 mb-2">
                <x-adminlte-input name="buscador" wire:model.live.debounce.300ms="buscador"
                    placeholder="N° factura, proveedor, RUC..."
                    igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            {{-- Proveedor --}}
            <div class="col-md-3 mb-2">
                <x-adminlte-select2 name="proveedor_id" wire:model.live="proveedor_id"
                    igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los proveedores</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">
                            {{ $proveedor->razon_social }} ({{ $proveedor->ruc }})
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            {{-- Estado --}}
            <div class="col-md-2 mb-2">
                <x-adminlte-select name="estado" wire:model.live="estado"
                    igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los estados</option>
                    @foreach(App\Models\Compras\Compra::ESTADOS as $key => $valor)
                        <option value="{{ $key }}">{{ $valor }}</option>
                    @endforeach
                </x-adminlte-select>
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

        {{-- Resumen de Filtros --}}
        @if(count($filtrosAplicados) > 0)
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-info alert-dismissible fade show py-2" role="alert">
                        <strong>Filtros aplicados:</strong>
                        @foreach($filtrosAplicados as $nombre => $valor)
                            <span class="badge badge-light mr-2">
                                {{ $nombre }}: {{ $valor }}
                            </span>
                        @endforeach
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                wire:click="$set('buscador', '')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Resumen de Resultados --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="small text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Mostrando {{ $compras->firstItem() ?? 0 }} a {{ $compras->lastItem() ?? 0 }} 
                    de {{ $compras->total() }} compras
                    @if($totalMonto > 0)
                        | Total: <strong class="text-success">Gs. {{ number_format($totalMonto, 0, ',', '.') }}</strong>
                    @endif
                </div>
            </div>
            <div class="col-md-6 text-right">
                <div class="btn-group btn-group-sm">
                    <button wire:click="exportarExcel" class="btn btn-outline-success">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </button>
                    <button wire:click="exportarPDF" class="btn btn-outline-danger ml-1">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                    <a href="{{ route('compras.compras.create') }}" class="btn btn-success ml-2">
                        <i class="fas fa-plus mr-1"></i> Nueva Compra
                    </a>
                </div>
            </div>
        </div>

        {{-- Tabla de Compras --}}
        @if($compras->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width: 5%">ID</th>
                            <th style="width: 12%">Factura</th>
                            <th style="width: 25%">Proveedor</th>
                            <th class="text-center" style="width: 10%">Fecha</th>
                            <th class="text-right" style="width: 12%">Total</th>
                            <th class="text-center" style="width: 10%">Estado</th>
                            <th class="text-center" style="width: 15%">Recepción</th>
                            <th class="text-center" style="width: 11%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($compras as $compra)
                            <tr class="{{ $compra->recepcion_completa ? 'table-success' : ($compra->recepciones->count() > 0 ? 'table-info' : '') }}">
                                {{-- ID --}}
                                <td class="text-center">
                                    <small class="text-muted">#{{ $compra->id }}</small>
                                </td>

                                {{-- Factura --}}
                                <td>
                                    <strong>{{ $compra->numero_factura }}</strong>
                                    @if($compra->timbrado)
                                        <br>
                                        <small class="text-muted">
                                            Timbrado: {{ $compra->timbrado->numero_timbrado }}
                                        </small>
                                    @endif
                                </td>

                                {{-- Proveedor --}}
                                <td>
                                    <strong>{{ $compra->proveedor->razon_social ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        RUC: {{ $compra->proveedor->ruc ?? 'N/A' }}
                                        @if($compra->proveedor && $compra->proveedor->telefono)
                                            | Tel: {{ $compra->proveedor->telefono }}
                                        @endif
                                    </small>
                                </td>

                                {{-- Fecha --}}
                                <td class="text-center">
                                    {{ $compra->fecha_emision->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">
                                        Vence: {{ $compra->fecha_vencimiento?->format('d/m/Y') ?? '-' }}
                                    </small>
                                </td>

                                {{-- Total --}}
                                <td class="text-right">
                                    <strong class="text-success">
                                        Gs. {{ number_format($compra->total, 0, ',', '.') }}
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $compra->tipo_factura == 'CREDITO' ? 'Crédito' : 'Contado' }}
                                    </small>
                                </td>

                                {{-- Estado --}}
                                <td class="text-center">
                                    @php
                                        $badgeClass = match($compra->estado) {
                                            'APROBADA' => 'success',
                                            'BORRADOR' => 'secondary',
                                            'PENDIENTE' => 'warning',
                                            'ANULADA' => 'danger',
                                            'PAGADA' => 'info',
                                            default => 'light'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">
                                        {{ $compra->estado_texto }}
                                    </span>
                                </td>

                                {{-- Recepción --}}
                                <td class="text-center">
                                    @if($compra->recepcion_completa)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i> Completa
                                        </span>
                                    @elseif($compra->recepciones->count() > 0)
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock mr-1"></i> Parcial ({{ $compra->porcentaje_recibido }}%)
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times mr-1"></i> Pendiente
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        {{-- Ver --}}
                                        <a href="{{ route('compras.compras.show', $compra) }}"
                                           class="btn btn-info" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Editar --}}
                                        @if($compra->estado == 'BORRADOR' || $compra->estado == 'PENDIENTE')
                                            <a href="{{ route('compras.compras.edit', $compra) }}"
                                               class="btn btn-warning ml-1" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        {{-- Menú desplegable --}}
                                        <div class="btn-group ml-1">
                                            <button type="button" class="btn btn-secondary dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                {{-- Duplicar --}}
                                                <a class="dropdown-item" href="#"
                                                   wire:click="duplicarCompra({{ $compra->id }})"
                                                   wire:confirm="¿Duplicar esta compra?">
                                                    <i class="fas fa-copy mr-2"></i> Duplicar
                                                </a>

                                                {{-- Recepción --}}
                                                <a class="dropdown-item"
                                                   href="{{ route('compras.recepciones.create', ['compra_id' => $compra->id]) }}">
                                                    <i class="fas fa-box-open mr-2"></i> Recepción
                                                </a>

                                                {{-- Flujo --}}
                                                <a class="dropdown-item"
                                                   href="{{ route('compras.dashboard.flujo.show', $compra) }}">
                                                    <i class="fas fa-project-diagram mr-2"></i> Ver Flujo
                                                </a>

                                                <div class="dropdown-divider"></div>

                                                {{-- Cambiar Estado --}}
                                                @if($compra->estado == 'BORRADOR')
                                                    <a class="dropdown-item" href="#"
                                                       wire:click="cambiarEstado({{ $compra->id }}, 'PENDIENTE')"
                                                       wire:confirm="¿Enviar a aprobación?">
                                                        <i class="fas fa-paper-plane mr-2"></i> Enviar a Aprobación
                                                    </a>
                                                @endif

                                                @if($compra->estado == 'PENDIENTE' && auth()->user()->can('compras.compras.aprobar'))
                                                    <a class="dropdown-item" href="#"
                                                       wire:click="cambiarEstado({{ $compra->id }}, 'APROBADA')"
                                                       wire:confirm="¿Aprobar esta compra?">
                                                        <i class="fas fa-check mr-2"></i> Aprobar
                                                    </a>
                                                @endif

                                                @if(in_array($compra->estado, ['PENDIENTE', 'APROBADA', 'BORRADOR']))
                                                    <a class="dropdown-item text-danger" href="#"
                                                       wire:click="cambiarEstado({{ $compra->id }}, 'ANULADA')"
                                                       wire:confirm="¿Anular esta compra?">
                                                        <i class="fas fa-ban mr-2"></i> Anular
                                                    </a>
                                                @endif
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
                    {{ $compras->links() }}
                </div>
            </div>
        @else
            {{-- Estado vacío --}}
            <div class="text-center py-5">
                <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No hay compras registradas</h5>
                <p class="text-muted">
                    @if(count($filtrosAplicados) > 0)
                        No se encontraron resultados con los filtros aplicados.
                    @else
                        Comienza creando una nueva compra.
                    @endif
                </p>
                <a href="{{ route('compras.compras.create') }}" class="btn btn-success btn-lg">
                    <i class="fas fa-plus mr-2"></i> Crear Primera Compra
                </a>
            </div>
        @endif
    </x-adminlte-card>
</div>
