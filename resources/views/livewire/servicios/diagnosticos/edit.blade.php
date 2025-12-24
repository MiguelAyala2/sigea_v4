<div>
    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    {{-- Datos de Diagnóstico --}}
    <x-adminlte-card theme="warning" title="Datos de Diagnóstico" icon="fas fa-stethoscope" collapsible>
        <form wire:submit.prevent="actualizar">
            <div class="row">
                <div class="col-md-3">
                    <x-adminlte-input name="numero_diagnostico" label="N° Diagnóstico"
                        wire:model="numero_diagnostico" disabled>
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-secondary">
                                <i class="fas fa-hashtag"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-3">
                    <x-adminlte-input name="fecha_diagnostico" label="Fecha de Diagnóstico *" type="date" wire:model="fecha_diagnostico"
                        enable-old-support error-key="fecha_diagnostico">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

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
                            <div class="list-group mt-1" style="position: absolute; z-index: 1000; width: 48%;">
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
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-select name="solicitud_id" label="Solicitud Relacionada *" wire:model.live="solicitud_id"
                        enable-old-support error-key="solicitud_id">
                        <option value="">Seleccione una solicitud</option>
                        @foreach($solicitudesCliente as $solicitud)
                            <option value="{{ $solicitud->id }}">
                                {{ $solicitud->numero_solicitud }} - {{ $solicitud->producto->nombre }}
                            </option>
                        @endforeach
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                </div>

                <div class="col-md-6">
                    <x-adminlte-select name="recepcion_id" label="Recepción Relacionada *" wire:model.live="recepcion_id"
                        enable-old-support error-key="recepcion_id">
                        <option value="">Seleccione una recepción</option>
                        @foreach($recepcionesCliente as $recepcion)
                            <option value="{{ $recepcion->id }}">
                                {{ $recepcion->numero_recepcion }} - {{ $recepcion->fecha_recepcion->format('d/m/Y') }}
                            </option>
                        @endforeach
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                </div>
            </div>
        </form>
    </x-adminlte-card>

    {{-- Detalles de la Solicitud --}}
    @if($solicitudSeleccionada)
        <x-adminlte-card theme="info" title="Detalles de la Solicitud" icon="fas fa-info-circle" collapsible>
            <div class="row">
                <div class="col-md-3">
                    <p><strong>N° Solicitud:</strong><br>{{ $solicitudSeleccionada->numero_solicitud }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Fecha:</strong><br>{{ $solicitudSeleccionada->fecha->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Equipo/Producto:</strong><br>{{ $solicitudSeleccionada->producto->nombre }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Tipo de Servicio:</strong><br>
                        <span class="badge badge-info">
                            {{ \App\Models\Servicios\SolicitudServicio::TIPOS_SERVICIO[$solicitudSeleccionada->tipo_servicio] }}
                        </span>
                    </p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <p><strong>Prioridad:</strong>
                        @if($solicitudSeleccionada->prioridad === 'alta')
                            <span class="badge badge-danger">Alta</span>
                        @elseif($solicitudSeleccionada->prioridad === 'media')
                            <span class="badge badge-warning">Media</span>
                        @else
                            <span class="badge badge-secondary">Baja</span>
                        @endif
                    </p>
                </div>
            </div>
        </x-adminlte-card>
    @endif

    {{-- Detalles de la Recepción --}}
    @if($recepcionSeleccionada)
        <x-adminlte-card theme="warning" title="Detalles de la Recepción" icon="fas fa-clipboard-check" collapsible>
            <div class="row">
                <div class="col-md-12">
                    <p><strong>Descripción del Problema:</strong><br>
                        {{ $recepcionSeleccionada->descripcion_problema }}
                    </p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <p><strong>Accesorios Recibidos:</strong><br>
                        {{ $recepcionSeleccionada->accesorios_recibidos ?? 'Ninguno' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Estado de Recepción:</strong><br>
                        @if($recepcionSeleccionada->estado_recepcion === 'bueno')
                            <span class="badge badge-success">Bueno</span>
                        @elseif($recepcionSeleccionada->estado_recepcion === 'regular')
                            <span class="badge badge-warning">Regular</span>
                        @else
                            <span class="badge badge-danger">Malo</span>
                        @endif
                    </p>
                </div>
            </div>
        </x-adminlte-card>
    @endif

    {{-- Datos del Diagnóstico --}}
    <x-adminlte-card theme="light" title="Resultado del Diagnóstico" icon="fas fa-tools" collapsible>
        <form wire:submit.prevent="actualizar">
            <div class="row">
                <div class="col-md-12">
                    <x-adminlte-textarea name="problema_detectado" label="Problema Detectado *" rows="3"
                        wire:model="problema_detectado" placeholder="Describa el problema detectado durante el diagnóstico..."
                        enable-old-support error-key="problema_detectado">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <x-adminlte-textarea name="solucion_propuesta" label="Solución Propuesta" rows="3"
                        wire:model="solucion_propuesta" placeholder="Describa la solución propuesta..."
                        enable-old-support error-key="solucion_propuesta">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-select name="estado_diagnostico" label="Estado del Diagnóstico *" wire:model="estado_diagnostico"
                        enable-old-support error-key="estado_diagnostico">
                        <option value="">Seleccione estado</option>
                        <option value="reparable">Reparable</option>
                        <option value="no_reparable">No Reparable</option>
                        <option value="requiere_repuestos">Requiere Repuestos</option>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-wrench"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                </div>

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
                    <x-adminlte-textarea name="observaciones" label="Observaciones" rows="2"
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
        </form>
    </x-adminlte-card>

    {{-- Tipos de Servicio --}}
    <x-adminlte-card theme="info" title="Tipos de Servicio" icon="fas fa-list-ul" collapsible>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="buscarTipoServicio">Buscar Tipo de Servicio</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" id="buscarTipoServicio"
                               wire:model.live="buscarTipoServicio"
                               placeholder="Buscar por código o descripción..." autocomplete="off">
                    </div>

                    @if($mostrarListaTiposServicio && count($tiposServicioEncontrados) > 0)
                        <div class="list-group mt-1" style="position: absolute; z-index: 1000; width: 98%;">
                            @foreach($tiposServicioEncontrados as $tipo)
                                <button type="button" class="list-group-item list-group-item-action"
                                        wire:click="agregarTipoServicio({{ $tipo->id }})">
                                    <strong>{{ $tipo->codigo }}</strong> - {{ $tipo->descripcion }} - ₲ {{ number_format($tipo->costo, 0, ',', '.') }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(count($tiposServicio) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Código</th>
                            <th>Tipo de Servicio</th>
                            <th width="100" class="text-center">Cantidad</th>
                            <th width="150" class="text-right">Precio Unitario</th>
                            <th width="150" class="text-right">Subtotal</th>
                            <th width="80" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tiposServicio as $index => $tipo)
                            <tr>
                                <td><strong>{{ $tipo['codigo'] }}</strong></td>
                                <td>{{ $tipo['descripcion'] }}</td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-sm text-center"
                                           wire:model.live="tiposServicio.{{ $index }}.cantidad" min="1">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control form-control-sm text-right"
                                           wire:model.live="tiposServicio.{{ $index }}.costo_unitario" min="0" step="0.01"
                                           placeholder="Precio Unitario">
                                </td>
                                <td class="text-right">
                                    <strong>₲ {{ number_format($this->calcularSubtotalTipoServicio($tipo), 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-xs"
                                            wire:click="eliminarTipoServicio({{ $index }})"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-info text-white">
                            <td colspan="4" class="text-right"><strong>TOTAL SERVICIOS:</strong></td>
                            <td class="text-right"><strong>₲ {{ number_format($this->calcularTotalTiposServicio(), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No se han agregado tipos de servicio.
            </div>
        @endif
    </x-adminlte-card>

    {{-- Repuestos Necesarios --}}
    <x-adminlte-card theme="success" title="Repuestos Necesarios" icon="fas fa-cogs" collapsible>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="buscarRepuesto">Buscar Repuesto</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" id="buscarRepuesto"
                               wire:model.live="buscarRepuesto"
                               placeholder="Buscar por nombre o código..." autocomplete="off">
                    </div>

                    @if($mostrarListaRepuestos && count($repuestosEncontrados) > 0)
                        <div class="list-group mt-1" style="position: absolute; z-index: 1000; width: 98%;">
                            @foreach($repuestosEncontrados as $producto)
                                <button type="button" class="list-group-item list-group-item-action"
                                        wire:click="agregarRepuesto({{ $producto->id }})">
                                    <strong>{{ $producto->codigo }}</strong> - {{ $producto->nombre }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(count($repuestos) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>Código</th>
                            <th>Repuesto o Producto</th>
                            <th width="100" class="text-center">Cantidad</th>
                            <th width="150" class="text-right">Precio Unitario</th>
                            <th width="150" class="text-right">Subtotal</th>
                            <th width="80" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($repuestos as $index => $repuesto)
                            <tr>
                                <td><strong>{{ $repuesto['codigo'] }}</strong></td>
                                <td>{{ $repuesto['nombre'] }}</td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-sm text-center"
                                           wire:model.live="repuestos.{{ $index }}.cantidad" min="1">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control form-control-sm text-right"
                                           wire:model.live="repuestos.{{ $index }}.costo_unitario" min="0" step="0.01"
                                           placeholder="Precio Unitario">
                                </td>
                                <td class="text-right">
                                    <strong>₲ {{ number_format($this->calcularSubtotal($repuesto), 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-xs"
                                            wire:click="eliminarRepuesto({{ $index }})"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <td colspan="4" class="text-right"><strong>TOTAL REPUESTOS:</strong></td>
                            <td class="text-right"><strong>₲ {{ number_format($this->calcularTotalRepuestos(), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                        @if(count($tiposServicio) > 0)
                        <tr class="bg-info">
                            <td colspan="4" class="text-right"><strong>Tipos de Servicio:</strong></td>
                            <td class="text-right"><strong>₲ {{ number_format($this->calcularTotalTiposServicio(), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                        @endif
                        <tr class="bg-success text-white">
                            <td colspan="4" class="text-right"><strong>TOTAL GENERAL:</strong></td>
                            <td class="text-right"><strong>₲ {{ number_format($this->calcularTotalGeneral(), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No se han agregado repuestos necesarios.
            </div>
        @endif
    </x-adminlte-card>

    {{-- Botones de Acción --}}
    <div class="row">
        <div class="col-md-12">
            <button type="button" wire:click="actualizar" class="btn btn-warning">
                <i class="fas fa-save"></i> Actualizar Diagnóstico
            </button>
            <a href="{{ route('servicios.diagnosticos.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </div>
</div>
