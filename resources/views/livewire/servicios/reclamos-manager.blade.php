<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-danger text-white">
            <h3 class="card-title">Datos del Reclamo</h3>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="registrarReclamo">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cliente *</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="buscarCliente"
                                    class="form-control @error('cliente_id') is-invalid @enderror"
                                    placeholder="Buscar cliente por nombre o documento..."
                                    autocomplete="off"
                                >
                                @if($clienteSeleccionado)
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" wire:click="limpiarCliente" title="Limpiar selección">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endif
                            </div>

                            @if($clienteSeleccionado)
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i> {{ $clienteSeleccionado->nombre }} - {{ $clienteSeleccionado->documento }}
                                </small>
                            @endif

                            @if($mostrarListaClientes && count($clientesEncontrados) > 0)
                                <div class="list-group" style="position: absolute; z-index: 1000; width: 93%; max-height: 200px; overflow-y: auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    @foreach($clientesEncontrados as $cliente)
                                        <button
                                            type="button"
                                            wire:click="seleccionarCliente({{ $cliente->id }})"
                                            class="list-group-item list-group-item-action"
                                            style="cursor: pointer;"
                                        >
                                            <strong>{{ $cliente->nombre }}</strong><br>
                                            <small class="text-muted">Doc: {{ $cliente->documento }}</small>
                                        </button>
                                    @endforeach
                                </div>
                            @elseif($mostrarListaClientes && strlen($buscarCliente) >= 2)
                                <div class="alert alert-warning mt-2 mb-0" style="padding: 0.5rem;">
                                    <small>No se encontraron clientes con ese criterio</small>
                                </div>
                            @elseif(!$clienteSeleccionado && strlen($buscarCliente) > 0 && strlen($buscarCliente) < 2)
                                <small class="text-muted">Escriba al menos 2 caracteres para buscar</small>
                            @endif

                            @error('cliente_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Servicio Relacionado (Opcional)</label>
                            @if($clienteSeleccionado)
                                <select wire:model="orden_servicio_id" class="form-control">
                                    <option value="">Ninguno</option>
                                    @foreach($ordenes as $orden)
                                        <option value="{{ $orden->id }}">
                                            {{ $orden->codigo }} - {{ $orden->equipo }} ({{ $orden->fecha_orden->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                @if(count($ordenes) === 0)
                                    <small class="text-muted">Este cliente no tiene órdenes de servicio registradas</small>
                                @endif
                            @else
                                <select class="form-control" disabled>
                                    <option value="">Seleccione primero un cliente</option>
                                </select>
                                <small class="text-muted">Debe seleccionar un cliente primero</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo de Reclamo *</label>
                            <select wire:model="tipo_reclamo" class="form-control @error('tipo_reclamo') is-invalid @enderror">
                                <option value="calidad_servicio">Calidad del Servicio</option>
                                <option value="demora_entrega">Demora en Entrega</option>
                                <option value="falla_post_servicio">Falla Post-Servicio</option>
                                <option value="atencion_cliente">Atención al Cliente</option>
                                <option value="costo_facturacion">Costo/Facturación</option>
                                <option value="otro">Otro</option>
                            </select>
                            @error('tipo_reclamo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Prioridad *</label>
                            <select wire:model="prioridad" class="form-control @error('prioridad') is-invalid @enderror">
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                            @error('prioridad') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha del Reclamo *</label>
                            <input type="date" wire:model="fecha_reclamo" class="form-control @error('fecha_reclamo') is-invalid @enderror">
                            @error('fecha_reclamo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Descripción del Reclamo *</label>
                            <textarea wire:model="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4" placeholder="Detalle el motivo del reclamo"></textarea>
                            @error('descripcion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Responsable Asignado (Opcional)</label>
                            <select wire:model="responsable_id" class="form-control">
                                <option value="">Sin asignar</option>
                                @foreach($responsables as $responsable)
                                    <option value="{{ $responsable->id }}">{{ $responsable->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Canal de Recepción *</label>
                            <select wire:model="canal_recepcion" class="form-control @error('canal_recepcion') is-invalid @enderror">
                                <option value="presencial">Presencial</option>
                                <option value="telefono">Teléfono</option>
                                <option value="email">Email</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="web">Web</option>
                            </select>
                            @error('canal_recepcion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-danger btn-lg">
                    <i class="fas fa-save"></i> Registrar Reclamo
                </button>
                <a href="{{ route('servicios.reclamos.seguimiento') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </form>
        </div>
    </div>
</div>
