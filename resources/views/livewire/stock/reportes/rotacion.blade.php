<div>
    <x-adminlte-card theme="light" icon="fas fa-sync-alt">
        <x-slot name="title">
            Reporte de Rotación de Inventario
        </x-slot>

        <x-slot name="toolsSlot">
            <button class="btn btn-sm btn-success" disabled>
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
            <button class="btn btn-sm btn-danger" disabled>
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </x-slot>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Información:</strong> El índice de rotación mide cuántas veces se renueva el inventario en el período.
            <strong>Rotación = Salidas / Stock Promedio</strong>
        </div>

        {{-- Filtros --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter"></i> Filtros
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <x-adminlte-input
                            name="fechaDesde"
                            label="Fecha Desde"
                            type="date"
                            wire:model="fechaDesde"
                        />
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-input
                            name="fechaHasta"
                            label="Fecha Hasta"
                            type="date"
                            wire:model="fechaHasta"
                        />
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-select
                            name="depositoId"
                            label="Depósito"
                            wire:model.live="depositoId"
                        >
                            <option value="">Todos</option>
                            @foreach($depositos as $deposito)
                                <option value="{{ $deposito->id }}">{{ $deposito->nombre }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-select
                            name="categoriaId"
                            label="Categoría"
                            wire:model.live="categoriaId"
                        >
                            <option value="">Todas</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-input
                            name="busqueda"
                            label="Buscar"
                            placeholder="Producto..."
                            wire:model.live.debounce.500ms="busqueda"
                        />
                    </div>
                    <div class="col-md-2">
                        <x-adminlte-select
                            name="ordenarPor"
                            label="Ordenar Por"
                            wire:model.live="ordenarPor"
                        >
                            <option value="rotacion_desc">Rotación (Mayor a Menor)</option>
                            <option value="rotacion_asc">Rotación (Menor a Mayor)</option>
                            <option value="salidas_desc">Salidas (Mayor a Menor)</option>
                            <option value="salidas_asc">Salidas (Menor a Mayor)</option>
                            <option value="nombre">Nombre (A-Z)</option>
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button wire:click="aplicarFiltros" class="btn btn-primary">
                            <i class="fas fa-search"></i> Aplicar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Leyenda de Clasificación --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Clasificación ABC</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <span class="badge badge-success badge-lg">Clase A - Alta</span>
                                <br><small>Rotación ≥ 6 veces</small>
                            </div>
                            <div class="col-md-3">
                                <span class="badge badge-primary badge-lg">Clase B - Media</span>
                                <br><small>Rotación 2-6 veces</small>
                            </div>
                            <div class="col-md-3">
                                <span class="badge badge-warning badge-lg">Clase C - Baja</span>
                                <br><small>Rotación 0-2 veces</small>
                            </div>
                            <div class="col-md-3">
                                <span class="badge badge-danger badge-lg">Clase D - Sin movimiento</span>
                                <br><small>Rotación = 0</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        @if($productosConRotacion->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-right">Entradas</th>
                            <th class="text-right">Salidas</th>
                            <th class="text-right">Stock Actual</th>
                            <th class="text-right">Stock Prom.</th>
                            <th class="text-right">Índice Rotación</th>
                            <th class="text-right">Días Inventario</th>
                            <th class="text-center">Clasificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productosConRotacion as $item)
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">{{ $item['producto']->codigo }}</span>
                                </td>
                                <td>
                                    <strong>{{ $item['producto']->nombre }}</strong>
                                </td>
                                <td>
                                    @if($item['producto']->categoria)
                                        <small>{{ $item['producto']->categoria->nombre }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <span class="text-success">
                                        {{ number_format($item['total_entradas'], 2) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <span class="text-danger">
                                        {{ number_format($item['total_salidas'], 2) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    {{ number_format($item['stock_actual'], 2) }}
                                    <small class="text-muted">{{ $item['producto']->unidadMedida->simbolo }}</small>
                                </td>
                                <td class="text-right">
                                    {{ number_format($item['stock_promedio'], 2) }}
                                </td>
                                <td class="text-right">
                                    <strong class="text-{{ $item['clasificacion']['badge'] }}">
                                        {{ number_format($item['rotacion'], 2) }}
                                    </strong>
                                </td>
                                <td class="text-right">
                                    @if($item['dias_inventario'] > 0)
                                        {{ number_format($item['dias_inventario'], 0) }} días
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $item['clasificacion']['badge'] }}">
                                        {{ $item['clasificacion']['clase'] }} - {{ $item['clasificacion']['texto'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación manual --}}
            <div class="mt-3">
                {{ $productosConRotacion->links() }}
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                No se encontraron productos con los filtros aplicados.
            </div>
        @endif
    </x-adminlte-card>
</div>
