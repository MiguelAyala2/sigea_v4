<div>
    <x-adminlte-card theme="light" icon="fas fa-exclamation-triangle">
        <x-slot name="title">
            Reporte de Stock Bajo y Agotado
        </x-slot>

        <x-slot name="toolsSlot">
            <button class="btn btn-sm btn-success" disabled>
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
            <button class="btn btn-sm btn-danger" disabled>
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </x-slot>

        {{-- Estadísticas --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Productos Agotados</span>
                        <span class="info-box-number">{{ $estadisticas['agotados'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Stock Bajo</span>
                        <span class="info-box-number">{{ $estadisticas['bajos'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total con Problemas</span>
                        <span class="info-box-number">{{ $estadisticas['total'] }}</span>
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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
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
                        <label>&nbsp;</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="incluirAgotados"
                                   wire:model.live="incluirAgotados">
                            <label class="custom-control-label" for="incluirAgotados">
                                Incluir Agotados
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        @if($stocks->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Depósito</th>
                            <th class="text-right">Stock Actual</th>
                            <th class="text-right">Stock Mín.</th>
                            <th class="text-right">Requerido</th>
                            <th class="text-center">Estado</th>
                            <th class="text-right">Precio Compra</th>
                            <th class="text-right">Valor Faltante</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $stock)
                            @php
                                $requerido = max(0, $stock->stock_minimo - $stock->stock_actual);
                                $precioCompra = $stock->producto->precioActual?->precio_compra ?? 0;
                                $valorFaltante = $requerido * $precioCompra;
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">{{ $stock->producto->codigo }}</span>
                                </td>
                                <td>
                                    <strong>{{ $stock->producto->nombre }}</strong>
                                </td>
                                <td>
                                    @if($stock->producto->categoria)
                                        <small>{{ $stock->producto->categoria->nombre }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $stock->deposito->nombre }}</small>
                                </td>
                                <td class="text-right">
                                    <strong class="text-{{ $stock->stock_actual <= 0 ? 'danger' : 'warning' }}">
                                        {{ number_format($stock->stock_actual, 2) }}
                                    </strong>
                                    <small class="text-muted">{{ $stock->producto->unidadMedida->simbolo }}</small>
                                </td>
                                <td class="text-right">
                                    {{ number_format($stock->stock_minimo, 2) }}
                                </td>
                                <td class="text-right">
                                    <span class="badge badge-info">
                                        {{ number_format($requerido, 2) }} {{ $stock->producto->unidadMedida->simbolo }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $stock->estado_badge }}">
                                        {{ ucfirst($stock->estado) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    @if($precioCompra > 0)
                                        <small>{{ number_format($precioCompra, 0) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($valorFaltante > 0)
                                        <strong class="text-danger">
                                            Gs. {{ number_format($valorFaltante, 0) }}
                                        </strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('stock.productos.show', $stock->producto_id) }}"
                                       class="btn btn-xs btn-info"
                                       title="Ver Producto">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('stock.productos.kardex', $stock->producto_id) }}"
                                       class="btn btn-xs btn-primary"
                                       title="Ver Kardex">
                                        <i class="fas fa-list"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-3">
                {{ $stocks->links() }}
            </div>
        @else
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                No se encontraron productos con stock bajo o agotado. ¡Todo está en orden!
            </div>
        @endif
    </x-adminlte-card>
</div>
