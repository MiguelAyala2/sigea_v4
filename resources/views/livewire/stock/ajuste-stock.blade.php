<div>
    <x-adminlte-card theme="light" icon="fas fa-balance-scale">
        <x-slot name="title">
            Ajuste de Stock
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
            <strong>Información:</strong> Los ajustes de stock permiten corregir diferencias entre el stock físico y el sistema.
            Siempre debe ingresar un motivo detallado del ajuste.
        </div>

        <form wire:submit.prevent="realizarAjuste">
            <div class="row">
                {{-- Buscador de Productos --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Buscar Producto *</label>
                        <div class="position-relative">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="searchProducto"
                                class="form-control"
                                placeholder="Buscar por nombre o código..."
                                autocomplete="off"
                            >
                            <div class="input-group-append" style="position: absolute; right: 5px; top: 5px;">
                                <span class="input-group-text bg-transparent border-0">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>

                            {{-- Resultados de búsqueda --}}
                            @if($mostrarResultados && count($productosEncontrados) > 0)
                                <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    @foreach($productosEncontrados as $producto)
                                        <button
                                            type="button"
                                            wire:click="seleccionarProducto({{ $producto->id }})"
                                            class="list-group-item list-group-item-action"
                                        >
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>{{ $producto->codigo }}</strong> - {{ $producto->nombre }}
                                                </div>
                                                @if($depositoId)
                                                    @php
                                                        $stockProd = \App\Models\Stock\Stock::where('producto_id', $producto->id)
                                                            ->where('deposito_id', $depositoId)
                                                            ->first();
                                                    @endphp
                                                    <small class="badge badge-primary">
                                                        Stock: {{ $stockProd ? number_format($stockProd->stock_actual, 2) : '0.00' }} {{ $producto->unidadMedida->simbolo }}
                                                    </small>
                                                @endif
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            @if($mostrarResultados && count($productosEncontrados) == 0 && strlen($searchProducto) >= 2)
                                <div class="list-group position-absolute w-100" style="z-index: 1000;">
                                    <div class="list-group-item text-muted">
                                        <i class="fas fa-info-circle"></i> No se encontraron productos
                                    </div>
                                </div>
                            @endif
                        </div>
                        @error('productoId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-md-6">
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
                    @error('depositoId') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Información de Stock Actual --}}
            @if($stockActual !== null && $productoSeleccionado)
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-secondary">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Producto:</strong> {{ $productoSeleccionado->nombre }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Stock Actual:</strong>
                                    <span class="badge badge-primary badge-lg">
                                        {{ number_format($stockActual, 2) }} {{ $productoSeleccionado->unidadMedida->simbolo }}
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Unidad:</strong> {{ $productoSeleccionado->unidadMedida->nombre }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-4">
                    <x-adminlte-select
                        name="tipoAjuste"
                        label="Tipo de Ajuste *"
                        wire:model.live="tipoAjuste"
                        enable-old-support
                    >
                        <option value="AJUSTE_POSITIVO">Ajuste Positivo (+) - Aumenta Stock</option>
                        <option value="AJUSTE_NEGATIVO">Ajuste Negativo (-) - Disminuye Stock</option>
                    </x-adminlte-select>
                    @error('tipoAjuste') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4">
                    <x-adminlte-input
                        name="cantidad"
                        label="Cantidad *"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        wire:model="cantidad"
                        enable-old-support
                    >
                        <x-slot name="appendSlot">
                            <div class="input-group-text">
                                @if($productoSeleccionado)
                                    {{ $productoSeleccionado->unidadMedida->simbolo }}
                                @else
                                    <i class="fas fa-box"></i>
                                @endif
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    @error('cantidad') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4">
                    @if($stockActual !== null && $cantidad)
                        <label>Nuevo Stock</label>
                        <div class="form-control" style="background-color: #f4f6f9;">
                            @if($tipoAjuste === 'AJUSTE_POSITIVO')
                                <span class="badge badge-success badge-lg">
                                    {{ number_format($stockActual + floatval($cantidad), 2) }}
                                    @if($productoSeleccionado)
                                        {{ $productoSeleccionado->unidadMedida->simbolo }}
                                    @endif
                                </span>
                            @else
                                <span class="badge badge-warning badge-lg">
                                    {{ number_format($stockActual - floatval($cantidad), 2) }}
                                    @if($productoSeleccionado)
                                        {{ $productoSeleccionado->unidadMedida->simbolo }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Motivo del Ajuste según tipo --}}
            <div class="row">
                <div class="col-md-12">
                    <x-adminlte-select
                        name="motivoAjuste"
                        label="Motivo del Ajuste *"
                        wire:model="motivoAjuste"
                        enable-old-support
                    >
                        <option value="">Seleccione un motivo...</option>
                        @if($tipoAjuste === 'AJUSTE_POSITIVO')
                            <optgroup label="AJUSTES POSITIVOS (+) - Aumentan el Stock">
                                @foreach($this->motivosPositivos as $key => $motivo)
                                    <option value="{{ $motivo }}">{{ $motivo }}</option>
                                @endforeach
                            </optgroup>
                        @else
                            <optgroup label="AJUSTES NEGATIVOS (-) - Disminuyen el Stock">
                                @foreach($this->motivosNegativos as $key => $motivo)
                                    <option value="{{ $motivo }}">{{ $motivo }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </x-adminlte-select>
                    @error('motivoAjuste') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <x-adminlte-textarea
                        name="motivo"
                        label="Observaciones Adicionales *"
                        rows="3"
                        placeholder="Ingrese observaciones adicionales del ajuste (mínimo 10 caracteres)..."
                        wire:model="motivo"
                        enable-old-support
                    />
                    @error('motivo') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Realizar Ajuste
                    </button>
                    <button type="button" wire:click="$refresh" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </button>
                </div>
            </div>
        </form>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Importante:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Los ajustes positivos <strong>incrementan</strong> el stock</li>
                        <li>Los ajustes negativos <strong>disminuyen</strong> el stock</li>
                        <li>Todos los ajustes quedan registrados en el kardex del producto</li>
                        <li>El motivo y las observaciones son obligatorios para auditoría</li>
                    </ul>
                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>
