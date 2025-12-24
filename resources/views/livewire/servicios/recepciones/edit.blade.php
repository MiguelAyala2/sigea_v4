<div>
    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="primary" title="Editar Recepción" icon="fas fa-clipboard-check" collapsible>
        <form wire:submit.prevent="actualizar">
            <div class="row">
                <div class="col-md-3">
                    <x-adminlte-input name="numero_recepcion" label="N° Recepción"
                        value="{{ $recepcion->numero_recepcion }}" disabled>
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-secondary">
                                <i class="fas fa-hashtag"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-3">
                    <x-adminlte-input name="fecha_recepcion" label="Fecha de Recepción *" type="date" wire:model="fecha_recepcion"
                        enable-old-support error-key="fecha_recepcion">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-6">
                    <x-adminlte-select name="solicitud_id" label="Solicitud Relacionada" wire:model.live="solicitud_id"
                        enable-old-support error-key="solicitud_id">
                        <option value="">Seleccione una solicitud</option>
                        @foreach($solicitudesCliente as $solicitud)
                            <option value="{{ $solicitud->id }}">
                                {{ $solicitud->numero_solicitud }} - {{ $solicitud->cliente->nombre }}
                            </option>
                        @endforeach
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="buscarCliente">Cliente *</label>
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
                    <x-adminlte-input name="contacto_cliente" label="Contacto del Cliente"
                        wire:model="contacto_cliente" placeholder="Teléfono o email"
                        enable-old-support error-key="contacto_cliente">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-phone"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>

            {{-- Mostrar detalles de la solicitud seleccionada --}}
            @if($solicitudSeleccionada)
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Detalles de la Solicitud</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>N° Solicitud:</strong><br>
                            {{ $solicitudSeleccionada->numero_solicitud }}
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha:</strong><br>
                            {{ $solicitudSeleccionada->fecha->format('d/m/Y') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Equipo/Producto:</strong><br>
                            {{ $solicitudSeleccionada->producto->nombre }}
                        </div>
                        <div class="col-md-3">
                            <strong>Tipo de Servicio:</strong><br>
                            <span class="badge badge-info">
                                {{ \App\Models\Servicios\SolicitudServicio::TIPOS_SERVICIO[$solicitudSeleccionada->tipo_servicio] }}
                            </span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <strong>Prioridad:</strong>
                            @if($solicitudSeleccionada->prioridad === 'alta')
                                <span class="badge badge-danger">Alta</span>
                            @elseif($solicitudSeleccionada->prioridad === 'media')
                                <span class="badge badge-warning">Media</span>
                            @else
                                <span class="badge badge-secondary">Baja</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </form>
    </x-adminlte-card>

    <x-adminlte-card theme="light" title="Datos del Equipo / Producto" icon="fas fa-tools" collapsible>
        <form wire:submit.prevent="actualizar">
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-select name="tipo_equipo" label="Tipo de Equipo *" wire:model="tipo_equipo"
                        enable-old-support error-key="tipo_equipo">
                        <option value="">Seleccione tipo de equipo</option>
                        <option value="Bomba de Agua">Bomba de Agua</option>
                        <option value="Motobomba">Motobomba</option>
                        <option value="Hidrolavadora">Hidrolavadora</option>
                        <option value="Generador">Generador</option>
                        <option value="Compresor">Compresor</option>
                        <option value="Otro">Otro</option>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-cogs"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                </div>

                <div class="col-md-3">
                    <x-adminlte-input name="marca" label="Marca" wire:model="marca"
                        placeholder="Marca del equipo" enable-old-support error-key="marca">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-tag"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-3">
                    <x-adminlte-input name="modelo" label="Modelo" wire:model="modelo"
                        placeholder="Modelo del equipo" enable-old-support error-key="modelo">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-barcode"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="numero_serie" label="N° de Serie" wire:model="numero_serie"
                        placeholder="Número de serie del equipo" enable-old-support error-key="numero_serie">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-hashtag"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-6">
                    <x-adminlte-select name="estado_recepcion" label="Estado de Recepción *" wire:model="estado_recepcion"
                        enable-old-support error-key="estado_recepcion">
                        <option value="">Seleccione estado</option>
                        <option value="bueno">Bueno</option>
                        <option value="regular">Regular</option>
                        <option value="malo">Malo</option>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-select name="estado" label="Estado *" wire:model="estado"
                        enable-old-support error-key="estado">
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
                    <x-adminlte-textarea name="descripcion_problema" label="Descripción del Problema / Motivo *" rows="3"
                        wire:model="descripcion_problema" placeholder="Detalle el problema o motivo del servicio..."
                        enable-old-support error-key="descripcion_problema">
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
                    <x-adminlte-textarea name="accesorios_recibidos" label="Accesorios Recibidos" rows="2"
                        wire:model="accesorios_recibidos" placeholder="Cable, control remoto, manual, etc."
                        enable-old-support error-key="accesorios_recibidos">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-box"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Recepción
                    </button>
                    <a href="{{ route('servicios.recepciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>
