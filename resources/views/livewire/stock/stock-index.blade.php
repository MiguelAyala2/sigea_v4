<div>
    <x-adminlte-card theme="light" icon="fas fa-boxes">
        <x-slot name="title">
            Consulta de Stock
        </x-slot>

        <x-slot name="toolsSlot">
            <a href="{{ route('stock.stock.ajuste') }}" class="btn btn-sm btn-warning">
                <i class="fas fa-balance-scale"></i> Ajustar Stock
            </a>
            <a href="{{ route('stock.stock.transferencia') }}" class="btn btn-sm btn-info">
                <i class="fas fa-exchange-alt"></i> Transferir
            </a>
            <a href="{{ route('stock.stock.inventario') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-clipboard-list"></i> Inventario
            </a>
        </x-slot>

        {{-- Estadísticas --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $estadisticas['total'] }}</h3>
                        <p>Total de Productos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $estadisticas['agotados'] }}</h3>
                        <p>Agotados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $estadisticas['bajos'] }}</h3>
                        <p>Stock Bajo</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $estadisticas['normales'] }}</h3>
                        <p>Stock Normal</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter"></i> Filtros de Búsqueda
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input
                            name="busqueda"
                            label="Buscar Producto"
                            placeholder="Nombre, código o código de barras..."
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
                    <div class="col-md-2">
                        <x-adminlte-select
                            name="estadoStock"
                            label="Estado"
                            wire:model.live="estadoStock"
                        >
                            <option value="">Todos</option>
                            <option value="agotado">Agotado</option>
                            <option value="bajo">Stock Bajo</option>
                            <option value="normal">Normal</option>
                            <option value="alto">Stock Alto</option>
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="soloActivos"
                                       wire:model.live="soloActivos">
                                <label class="custom-control-label" for="soloActivos">
                                    Solo Activos
                                </label>
                            </div>
                            <button wire:click="limpiarFiltros" class="btn btn-sm btn-secondary mt-2">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla de Stock --}}
        @if($stocks->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Depósito</th>
                            <th class="text-right">Cant. Existente</th>
                            <th class="text-right">Stock Mín.</th>
                            <th class="text-right">Stock Máx.</th>
                            <th class="text-right">Precio Compra</th>
                            <th class="text-right">Precio Venta</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $stock)
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">{{ $stock->producto->codigo }}</span>
                                </td>
                                <td>
                                    <strong>{{ $stock->producto->nombre }}</strong>
                                    @if($stock->producto->codigo_barras)
                                        <br><small class="text-muted">
                                            <i class="fas fa-barcode"></i> {{ $stock->producto->codigo_barras }}
                                        </small>
                                    @endif
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
                                    <strong>{{ number_format($stock->stock_actual, 2) }}</strong>
                                    <small class="text-muted">{{ $stock->producto->unidadMedida->simbolo }}</small>
                                </td>
                                <td class="text-right">
                                    {{ number_format($stock->stock_minimo, 2) }}
                                </td>
                                <td class="text-right">
                                    @if($stock->stock_maximo)
                                        {{ number_format($stock->stock_maximo, 2) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($stock->producto->precioActual)
                                        <small class="text-muted">₲</small> {{ number_format($stock->producto->precioActual->precio_compra, 0, ',', '.') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($stock->producto->precioActual)
                                        <strong class="text-success">₲</strong> <strong>{{ number_format($stock->producto->precioActual->precio_venta, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $stock->estado_badge }}">
                                        {{ ucfirst($stock->estado) }}
                                    </span>
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
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                No se encontraron registros de stock con los filtros aplicados.
            </div>
        @endif
    </x-adminlte-card>
</div>
