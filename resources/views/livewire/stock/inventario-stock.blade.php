<div>
    <x-adminlte-card theme="light" icon="fas fa-clipboard-list">
        <x-slot name="title">
            Toma de Inventario Físico
        </x-slot>

        <x-slot name="toolsSlot">
            <a href="{{ route('stock.stock.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </x-slot>

        {{-- Mensajes de éxito/error --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Información:</strong> El inventario físico permite ajustar el stock del sistema según el conteo real.
            Ingrese la cantidad física contada en cada producto. Las diferencias se ajustarán automáticamente.
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
                    <div class="col-md-4">
                        <x-adminlte-select
                            name="depositoId"
                            label="Depósito *"
                            wire:model.live="depositoId"
                            enable-old-support
                        >
                            <option value="">Seleccione un depósito...</option>
                            @foreach($depositos as $deposito)
                                <option value="{{ $deposito->id }}">{{ $deposito->nombre }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-select
                            name="categoriaId"
                            label="Categoría"
                            wire:model.live="categoriaId"
                            enable-old-support
                        >
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input
                            name="busqueda"
                            label="Buscar Producto"
                            placeholder="Nombre, código..."
                            wire:model.live.debounce.500ms="busqueda"
                        >
                            <x-slot name="appendSlot">
                                <div class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-input>
                    </div>
                </div>
            </div>
        </div>

        @if($depositoId)
            {{-- Tabla de Inventario --}}
            @if($stocks->count() > 0)
                <form wire:submit.prevent="procesarInventario">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="100">Código</th>
                                    <th>Producto</th>
                                    <th width="80">Unidad</th>
                                    <th width="120" class="text-right">Stock Sistema</th>
                                    <th width="150">Conteo Físico</th>
                                    <th width="120" class="text-right">Diferencia</th>
                                    <th width="80" class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stocks as $stock)
                                    @php
                                        $conteoFisico = $conteos[$stock->id] ?? '';
                                        $diferencia = $conteoFisico !== '' ? floatval($conteoFisico) - $stock->stock_actual : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <small class="badge badge-secondary">{{ $stock->producto->codigo }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $stock->producto->nombre }}</strong>
                                            @if($stock->ubicacion)
                                                <br><small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i> {{ $stock->ubicacion }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <small>{{ $stock->producto->unidadMedida->simbolo }}</small>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ number_format($stock->stock_actual, 2) }}</strong>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   step="0.01"
                                                   min="0"
                                                   class="form-control form-control-sm"
                                                   placeholder="0.00"
                                                   wire:model.live="conteos.{{ $stock->id }}">
                                        </td>
                                        <td class="text-right">
                                            @if($diferencia !== null)
                                                @if($diferencia > 0)
                                                    <span class="badge badge-success">
                                                        +{{ number_format($diferencia, 2) }}
                                                    </span>
                                                @elseif($diferencia < 0)
                                                    <span class="badge badge-danger">
                                                        {{ number_format($diferencia, 2) }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        0.00
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($diferencia !== null)
                                                @if($diferencia == 0)
                                                    <i class="fas fa-check text-success" title="Correcto"></i>
                                                @else
                                                    <i class="fas fa-exclamation-triangle text-warning" title="Con diferencia"></i>
                                                @endif
                                            @else
                                                <i class="fas fa-minus text-muted" title="Sin contar"></i>
                                            @endif
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

                    {{-- Observaciones y Botón --}}
                    <div class="row mt-3">
                        <div class="col-md-8">
                            <x-adminlte-textarea
                                name="observaciones"
                                label="Observaciones del Inventario"
                                rows="2"
                                placeholder="Observaciones generales del inventario (opcional)..."
                                wire:model="observaciones"
                            />
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-save"></i> Procesar Inventario
                                </button>
                                <button type="button" wire:click="$refresh" class="btn btn-secondary btn-block">
                                    <i class="fas fa-redo"></i> Limpiar Conteos
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Importante:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Ingrese la cantidad física contada en cada producto</li>
                                <li>Las diferencias se calcularán automáticamente</li>
                                <li>Al procesar, se ajustará el stock del sistema al conteo físico</li>
                                <li>Solo se procesarán los productos con diferencias</li>
                                <li>Todos los ajustes quedarán registrados en el kardex</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No se encontraron productos con stock en el depósito seleccionado.
                </div>
            @endif
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Seleccione un depósito para comenzar el inventario.
            </div>
        @endif
    </x-adminlte-card>
</div>
