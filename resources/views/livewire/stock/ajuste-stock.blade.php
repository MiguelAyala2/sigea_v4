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
                <div class="col-md-6">
                    <x-adminlte-select
                        name="productoId"
                        label="Producto *"
                        wire:model.live="productoId"
                        enable-old-support
                    >
                        <option value="">Seleccione un producto...</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}">
                                {{ $producto->codigo }} - {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                    @error('productoId') <span class="text-danger">{{ $message }}</span> @enderror
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
                        wire:model="tipoAjuste"
                        enable-old-support
                    >
                        <option value="AJUSTE_POSITIVO">Ajuste Positivo (+)</option>
                        <option value="AJUSTE_NEGATIVO">Ajuste Negativo (-)</option>
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

            <div class="row">
                <div class="col-md-12">
                    <x-adminlte-textarea
                        name="motivo"
                        label="Motivo del Ajuste *"
                        rows="3"
                        placeholder="Ingrese el motivo detallado del ajuste (mínimo 10 caracteres)..."
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
                        <li>Los ajustes positivos incrementan el stock</li>
                        <li>Los ajustes negativos disminuyen el stock</li>
                        <li>Todos los ajustes quedan registrados en el kardex del producto</li>
                        <li>El motivo del ajuste es obligatorio para auditoría</li>
                    </ul>
                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>
