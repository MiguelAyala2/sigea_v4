<div>
    <x-adminlte-card theme="light" icon="fas fa-dollar-sign">
        <x-slot name="title">
            Reporte de Inventario Valorizado
        </x-slot>

        <x-slot name="toolsSlot">
            <button class="btn btn-sm btn-success" disabled>
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
            <button class="btn btn-sm btn-danger" disabled>
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </x-slot>

        {{-- Totales --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Productos</span>
                        <span class="info-box-number">{{ $totales['cantidad_productos'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Valor Compra Total</span>
                        <span class="info-box-number">
                            Gs. {{ number_format($totales['total_valor_compra'], 0) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Valor Venta Total</span>
                        <span class="info-box-number">
                            Gs. {{ number_format($totales['total_valor_venta'], 0) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Utilidad Potencial</span>
                        <span class="info-box-number">
                            Gs. {{ number_format($totales['total_utilidad_potencial'], 0) }}
                        </span>
                    </div>
                </div>
            </div>
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
                    <div class="col-md-3">
                        <x-adminlte-input
                            name="busqueda"
                            label="Buscar Producto"
                            placeholder="Nombre o código..."
                            wire:model.live.debounce.500ms="busqueda"
                        >
                            <x-slot name="appendSlot">
                                <div class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-input>
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
                    <div class="col-md-3">
                        <x-adminlte-select
                            name="ordenarPor"
                            label="Ordenar Por"
                            wire:model.live="ordenarPor"
                        >
                            <option value="valor_desc">Valor Compra (Mayor a Menor)</option>
                            <option value="valor_asc">Valor Compra (Menor a Mayor)</option>
                            <option value="utilidad_desc">Utilidad (Mayor a Menor)</option>
                            <option value="utilidad_asc">Utilidad (Menor a Mayor)</option>
                            <option value="stock_desc">Stock (Mayor a Menor)</option>
                            <option value="nombre">Nombre (A-Z)</option>
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="soloConStock"
                                   wire:model.live="soloConStock">
                            <label class="custom-control-label" for="soloConStock">
                                Solo con Stock
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        @if($stocksValorizados->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Depósito</th>
                            <th class="text-right">Stock</th>
                            <th class="text-right">P. Compra</th>
                            <th class="text-right">P. Venta</th>
                            <th class="text-right">Valor Compra</th>
                            <th class="text-right">Valor Venta</th>
                            <th class="text-right">Utilidad Pot.</th>
                            <th class="text-right">Margen %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocksValorizados as $item)
                            @php
                                $margen = $item['valor_compra'] > 0
                                    ? (($item['valor_venta'] - $item['valor_compra']) / $item['valor_compra']) * 100
                                    : 0;
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">{{ $item['stock']->producto->codigo }}</span>
                                </td>
                                <td>
                                    <strong>{{ $item['stock']->producto->nombre }}</strong>
                                </td>
                                <td>
                                    @if($item['stock']->producto->categoria)
                                        <small>{{ $item['stock']->producto->categoria->nombre }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $item['stock']->deposito->nombre }}</small>
                                </td>
                                <td class="text-right">
                                    <strong>{{ number_format($item['stock']->stock_actual, 2) }}</strong>
                                    <small class="text-muted">{{ $item['stock']->producto->unidadMedida->simbolo }}</small>
                                </td>
                                <td class="text-right">
                                    @if($item['precio_compra'] > 0)
                                        <small>{{ number_format($item['precio_compra'], 0) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($item['precio_venta'] > 0)
                                        <small>{{ number_format($item['precio_venta'], 0) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <strong class="text-warning">
                                        {{ number_format($item['valor_compra'], 0) }}
                                    </strong>
                                </td>
                                <td class="text-right">
                                    <strong class="text-success">
                                        {{ number_format($item['valor_venta'], 0) }}
                                    </strong>
                                </td>
                                <td class="text-right">
                                    <strong class="text-primary">
                                        {{ number_format($item['utilidad_potencial'], 0) }}
                                    </strong>
                                </td>
                                <td class="text-right">
                                    @if($margen > 0)
                                        <span class="badge badge-info">
                                            {{ number_format($margen, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <th colspan="7" class="text-right">TOTALES:</th>
                            <th class="text-right">
                                <strong class="text-warning">
                                    Gs. {{ number_format($totales['total_valor_compra'], 0) }}
                                </strong>
                            </th>
                            <th class="text-right">
                                <strong class="text-success">
                                    Gs. {{ number_format($totales['total_valor_venta'], 0) }}
                                </strong>
                            </th>
                            <th class="text-right">
                                <strong class="text-primary">
                                    Gs. {{ number_format($totales['total_utilidad_potencial'], 0) }}
                                </strong>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-3">
                {{ $stocksValorizados->links() }}
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                No se encontraron productos con los filtros aplicados.
            </div>
        @endif

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota:</strong> Los valores se calculan multiplicando el stock actual por el precio de compra/venta.
                    La utilidad potencial representa la ganancia si se vendiera todo el inventario al precio actual.
                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>
