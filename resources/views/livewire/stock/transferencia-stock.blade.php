<div>
    <x-adminlte-card theme="light" icon="fas fa-exchange-alt">
        <x-slot name="title">
            Transferencia de Stock entre Depósitos
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
            <strong>Información:</strong> Las transferencias permiten mover stock entre depósitos.
            Se registrarán automáticamente una salida en el depósito origen y una entrada en el depósito destino.
        </div>

        <form wire:submit.prevent="realizarTransferencia">
            <div class="row">
                <div class="col-md-12">
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
            </div>

            {{-- Información de Producto --}}
            @if($productoSeleccionado)
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-secondary">
                            <div class="row">
                                <div class="col-md-8">
                                    <strong>Producto:</strong> {{ $productoSeleccionado->nombre }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Unidad:</strong> {{ $productoSeleccionado->unidadMedida->nombre }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-sign-out-alt"></i> Depósito Origen
                            </h3>
                        </div>
                        <div class="card-body">
                            <x-adminlte-select
                                name="depositoOrigenId"
                                label="Depósito Origen *"
                                wire:model.live="depositoOrigenId"
                                enable-old-support
                            >
                                <option value="">Seleccione depósito origen...</option>
                                @foreach($depositos as $deposito)
                                    <option value="{{ $deposito->id }}">{{ $deposito->nombre }}</option>
                                @endforeach
                            </x-adminlte-select>
                            @error('depositoOrigenId') <span class="text-danger">{{ $message }}</span> @enderror

                            @if($stockOrigen !== null)
                                <div class="text-center mt-3">
                                    <h5>Stock Disponible</h5>
                                    <span class="badge badge-primary" style="font-size: 1.5rem; padding: 0.75rem 1rem;">
                                        {{ number_format($stockOrigen, 2) }}
                                        @if($productoSeleccionado)
                                            {{ $productoSeleccionado->unidadMedida->simbolo }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-sign-in-alt"></i> Depósito Destino
                            </h3>
                        </div>
                        <div class="card-body">
                            <x-adminlte-select
                                name="depositoDestinoId"
                                label="Depósito Destino *"
                                wire:model.live="depositoDestinoId"
                                enable-old-support
                            >
                                <option value="">Seleccione depósito destino...</option>
                                @foreach($depositos as $deposito)
                                    <option value="{{ $deposito->id }}">{{ $deposito->nombre }}</option>
                                @endforeach
                            </x-adminlte-select>
                            @error('depositoDestinoId') <span class="text-danger">{{ $message }}</span> @enderror

                            @if($stockDestino !== null)
                                <div class="text-center mt-3">
                                    <h5>Stock Actual</h5>
                                    <span class="badge badge-success" style="font-size: 1.5rem; padding: 0.75rem 1rem;">
                                        {{ number_format($stockDestino, 2) }}
                                        @if($productoSeleccionado)
                                            {{ $productoSeleccionado->unidadMedida->simbolo }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input
                        name="cantidad"
                        label="Cantidad a Transferir *"
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

                <div class="col-md-6">
                    @if($stockOrigen !== null && $stockDestino !== null && $cantidad)
                        <label>Vista Previa</label>
                        <div class="card">
                            <div class="card-body p-2">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <small class="text-muted">Nuevo Stock Origen</small><br>
                                        <span class="badge badge-warning badge-lg">
                                            {{ number_format($stockOrigen - floatval($cantidad), 2) }}
                                            @if($productoSeleccionado)
                                                {{ $productoSeleccionado->unidadMedida->simbolo }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Nuevo Stock Destino</small><br>
                                        <span class="badge badge-success badge-lg">
                                            {{ number_format($stockDestino + floatval($cantidad), 2) }}
                                            @if($productoSeleccionado)
                                                {{ $productoSeleccionado->unidadMedida->simbolo }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <x-adminlte-textarea
                        name="motivo"
                        label="Motivo de la Transferencia *"
                        rows="3"
                        placeholder="Ingrese el motivo detallado de la transferencia (mínimo 10 caracteres)..."
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
                        <i class="fas fa-exchange-alt"></i> Realizar Transferencia
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
                        <li>La transferencia se registra como una salida en el depósito origen</li>
                        <li>Y como una entrada en el depósito destino</li>
                        <li>Ambos movimientos quedan registrados en el kardex del producto</li>
                        <li>No se pueden transferir cantidades mayores al stock disponible</li>
                        <li>Los depósitos origen y destino deben ser diferentes</li>
                    </ul>
                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>
