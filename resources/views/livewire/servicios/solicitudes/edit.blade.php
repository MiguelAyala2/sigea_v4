<div>
    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="light" title="Editar Solicitud de Servicio" icon="fas fa-clipboard-list">
        <form wire:submit.prevent="actualizar">
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="numero_solicitud" label="N° Solicitud"
                        value="{{ $solicitud->numero_solicitud }}" disabled>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-hashtag"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-6">
                    <x-adminlte-input name="fecha" label="Fecha" type="date" wire:model="fecha"
                        enable-old-support error-key="fecha">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="buscarCliente">Cliente</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control @error('cliente_id') is-invalid @enderror"
                                   id="buscarCliente" wire:model.live="buscarCliente"
                                   placeholder="Buscar cliente por nombre o documento..." autocomplete="off">
                        </div>
                        @error('cliente_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        @if($mostrarListaClientes && count($clientesEncontrados) > 0)
                            <div class="list-group mt-1" style="position: absolute; z-index: 1000; width: 47%;">
                                @foreach($clientesEncontrados as $cliente)
                                    <button type="button" class="list-group-item list-group-item-action"
                                            wire:click="seleccionarCliente({{ $cliente->id }})">
                                        <strong>{{ $cliente->documento }}</strong> - {{ $cliente->nombre }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="buscarProducto">Equipo/Producto</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-box"></i></span>
                            </div>
                            <input type="text" class="form-control @error('producto_id') is-invalid @enderror"
                                   id="buscarProducto" wire:model.live="buscarProducto"
                                   placeholder="Buscar producto por nombre o código..." autocomplete="off">
                        </div>
                        @error('producto_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        @if($mostrarListaProductos && count($productosEncontrados) > 0)
                            <div class="list-group mt-1" style="position: absolute; z-index: 1000; width: 47%;">
                                @foreach($productosEncontrados as $producto)
                                    <button type="button" class="list-group-item list-group-item-action"
                                            wire:click="seleccionarProducto({{ $producto->id }})">
                                        <strong>{{ $producto->codigo }}</strong> - {{ $producto->nombre }}
                                        <small class="text-muted">(Stock: {{ $producto->cantidad }})</small>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-adminlte-select name="tipo_servicio" label="Tipo de Servicio" wire:model="tipo_servicio"
                        enable-old-support error-key="tipo_servicio">
                        <option value="">Seleccione tipo</option>
                        <option value="mantenimiento">Mantenimiento</option>
                        <option value="reparacion">Reparación</option>
                        <option value="diagnostico">Diagnóstico</option>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-tools"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                </div>

                <div class="col-md-4">
                    <x-adminlte-select name="prioridad" label="Prioridad" wire:model="prioridad"
                        enable-old-support error-key="prioridad">
                        <option value="">Seleccione prioridad</option>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                </div>

                <div class="col-md-4">
                    <x-adminlte-select name="estado" label="Estado" wire:model="estado"
                        enable-old-support error-key="estado">
                        <option value="">Seleccione estado</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="completado">Completado</option>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-tasks"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <x-adminlte-textarea name="observaciones" label="Observaciones" rows="3"
                        wire:model="observaciones" placeholder="Observaciones adicionales..."
                        enable-old-support error-key="observaciones">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-comment"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar Solicitud
                    </button>
                    <a href="{{ route('servicios.solicitudes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>
