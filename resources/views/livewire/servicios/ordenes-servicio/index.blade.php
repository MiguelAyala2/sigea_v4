<div>
    <x-adminlte-card theme="success" icon="fas fa-tools">
        <div class="row mb-3">
            <div class="col-md-4">
                <x-adminlte-input name="buscador" label="Buscar" wire:model.live="buscador"
                    placeholder="Buscar por código o cliente">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-3">
                <x-adminlte-select name="buscarEstado" label="Estado" wire:model.live="buscarEstado">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="pausada">Pausada</option>
                    <option value="finalizada">Finalizada</option>
                    <option value="entregada">Entregada</option>
                    <option value="cancelada">Cancelada</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-filter"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="bg-success">
                    <tr>
                        <th>N° Orden</th>
                        <th>Cliente</th>
                        <th>Equipo</th>
                        <th>Técnico</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ordenes as $orden)
                        <tr>
                            <td><strong>{{ $orden->codigo }}</strong></td>
                            <td>{{ $orden->cliente }}</td>
                            <td>{{ $orden->equipo }}</td>
                            <td>
                                @if($orden->tecnico_id)
                                    <span class="badge badge-info">
                                        <i class="fas fa-user"></i> {{ $orden->tecnico_nombre }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">Sin asignar</span>
                                @endif
                            </td>
                            <td>{!! $orden->estado_badge !!}</td>
                            <td>
                                <button wire:click="verOrden({{ $orden->id }})"
                                    class="btn btn-info btn-sm" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <a href="{{ route('servicios.ordenes.edit', $orden->id) }}"
                                    class="btn btn-primary btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if($orden->estado === 'pendiente')
                                    <button wire:click="abrirModalAsignar({{ $orden->id }})"
                                        class="btn btn-warning btn-sm" title="Asignar técnico">
                                        <i class="fas fa-user-plus"></i>
                                    </button>

                                    @if($orden->tecnico_id)
                                        <button wire:click="iniciarOrden({{ $orden->id }})"
                                            class="btn btn-success btn-sm" title="Iniciar orden"
                                            onclick="return confirm('¿Iniciar esta orden de servicio?')">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay órdenes de servicio registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $ordenes->links() }}
        </div>
    </x-adminlte-card>

    <!-- Modal Ver Orden -->
    @if($mostrarModal && $ordenSeleccionada)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title">
                            <i class="fas fa-tools"></i> Detalles de la Orden {{ $ordenSeleccionada->codigo }}
                        </h5>
                        <button type="button" class="close" wire:click="cerrarModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Información de la Orden -->
                            <div class="col-md-6">
                                <h6 class="text-primary">Información de la Orden</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th width="40%">Código:</th>
                                        <td>{{ $ordenSeleccionada->codigo }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha Orden:</th>
                                        <td>{{ $ordenSeleccionada->fecha_orden->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Estado:</th>
                                        <td>{!! $ordenSeleccionada->estado_badge !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Técnico Asignado:</th>
                                        <td>{{ $ordenSeleccionada->tecnico_nombre }}</td>
                                    </tr>
                                    @if($ordenSeleccionada->fecha_inicio)
                                    <tr>
                                        <th>Fecha Inicio:</th>
                                        <td>{{ $ordenSeleccionada->fecha_inicio->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @endif
                                    @if($ordenSeleccionada->observaciones)
                                    <tr>
                                        <th>Observaciones:</th>
                                        <td>{{ $ordenSeleccionada->observaciones }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Información del Cliente y Equipo -->
                            <div class="col-md-6">
                                <h6 class="text-primary">Cliente y Equipo</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th width="40%">Cliente:</th>
                                        <td>{{ $ordenSeleccionada->cliente }}</td>
                                    </tr>
                                    <tr>
                                        <th>Equipo:</th>
                                        <td>{{ $ordenSeleccionada->equipo }}</td>
                                    </tr>
                                    @if($ordenSeleccionada->presupuesto)
                                    <tr>
                                        <th>Presupuesto:</th>
                                        <td>{{ $ordenSeleccionada->presupuesto->codigo }}</td>
                                    </tr>
                                    <tr>
                                        <th>Monto Total:</th>
                                        <td><strong>{{ $ordenSeleccionada->presupuesto->monto_formateado }}</strong></td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <!-- Servicios a Realizar -->
                        @if($ordenSeleccionada->presupuesto && $ordenSeleccionada->presupuesto->diagnostico)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary">Tipos de Servicio</h6>
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Tipo de Servicio</th>
                                            <th class="text-right">Cantidad</th>
                                            <th class="text-right">Precio Unitario</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ordenSeleccionada->presupuesto->diagnostico->tiposServicio as $servicio)
                                        <tr>
                                            <td>{{ $servicio->tipoServicio->codigo ?? 'N/A' }}</td>
                                            <td>{{ $servicio->tipoServicio->nombre ?? 'N/A' }}</td>
                                            <td class="text-right">{{ $servicio->cantidad }}</td>
                                            <td class="text-right">₲ {{ number_format($servicio->costo_unitario, 0, ',', '.') }}</td>
                                            <td class="text-right">₲ {{ number_format($servicio->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold">
                                            <td colspan="4" class="text-right">SUBTOTAL SERVICIOS:</td>
                                            <td class="text-right">₲ {{ number_format($ordenSeleccionada->presupuesto->subtotal_servicios, 0, ',', '.') }}</td>
                                        </tr>
                                        @if($ordenSeleccionada->presupuesto->descuento_promocion > 0)
                                        <tr class="text-danger">
                                            <td colspan="4" class="text-right">Descuento Promoción:</td>
                                            <td class="text-right">- ₲ {{ number_format($ordenSeleccionada->presupuesto->descuento_promocion, 0, ',', '.') }}</td>
                                        </tr>
                                        @endif
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="4" class="text-right">TOTAL SERVICIOS:</td>
                                            <td class="text-right">₲ {{ number_format($ordenSeleccionada->presupuesto->total_servicios, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Repuestos Necesarios -->
                        @if($ordenSeleccionada->presupuesto->diagnostico->repuestos->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary">Repuestos Necesarios</h6>
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Repuesto</th>
                                            <th class="text-right">Cantidad</th>
                                            <th class="text-right">Precio Unitario</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ordenSeleccionada->presupuesto->diagnostico->repuestos as $repuesto)
                                        <tr>
                                            <td>{{ $repuesto->producto->codigo ?? 'N/A' }}</td>
                                            <td>{{ $repuesto->producto->nombre ?? 'N/A' }}</td>
                                            <td class="text-right">{{ $repuesto->cantidad }}</td>
                                            <td class="text-right">₲ {{ number_format($repuesto->costo, 0, ',', '.') }}</td>
                                            <td class="text-right">₲ {{ number_format($repuesto->cantidad * $repuesto->costo, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold">
                                            <td colspan="4" class="text-right">SUBTOTAL REPUESTOS:</td>
                                            <td class="text-right">₲ {{ number_format($ordenSeleccionada->presupuesto->subtotal_repuestos, 0, ',', '.') }}</td>
                                        </tr>
                                        @if($ordenSeleccionada->presupuesto->descuento_descuento > 0)
                                        <tr class="text-danger">
                                            <td colspan="4" class="text-right">Descuento Aplicado:</td>
                                            <td class="text-right">- ₲ {{ number_format($ordenSeleccionada->presupuesto->descuento_descuento, 0, ',', '.') }}</td>
                                        </tr>
                                        @endif
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="4" class="text-right">TOTAL REPUESTOS:</td>
                                            <td class="text-right">₲ {{ number_format($ordenSeleccionada->presupuesto->total_repuestos, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Total General -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <table class="table table-sm table-bordered">
                                    <tr class="bg-success text-white">
                                        <td class="text-right font-weight-bold" style="font-size: 16px;">TOTAL GENERAL:</td>
                                        <td class="text-right font-weight-bold" style="font-size: 18px; width: 200px;">₲ {{ number_format($ordenSeleccionada->presupuesto->monto_total, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Modal Asignar Técnico -->
    @if($mostrarModalAsignar && $ordenParaAsignar)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus"></i> Asignar Técnico
                        </h5>
                        <button type="button" class="close" wire:click="cerrarModalAsignar">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Orden:</strong> {{ $ordenParaAsignar->codigo }}</p>
                        <p><strong>Cliente:</strong> {{ $ordenParaAsignar->cliente }}</p>
                        <p><strong>Equipo:</strong> {{ $ordenParaAsignar->equipo }}</p>

                        <div class="form-group">
                            <label>Seleccione un Técnico *</label>
                            <select wire:model="tecnicoSeleccionado" class="form-control">
                                <option value="">Seleccione...</option>
                                @foreach($tecnicos as $tecnico)
                                    <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarModalAsignar">Cancelar</button>
                        <button type="button" class="btn btn-success" wire:click="asignarTecnico">
                            <i class="fas fa-check"></i> Asignar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
