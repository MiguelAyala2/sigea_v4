<div>
    <x-adminlte-card theme="primary" icon="fas fa-file-invoice-dollar">
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
                    <option value="pendiente_aprobacion">Pendiente Aprobación</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="rechazado">Rechazado</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-filter"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>

            <div class="col-md-5 d-flex align-items-end">
                <a href="{{ route('servicios.presupuestos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Presupuesto
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="bg-primary">
                    <tr>
                        <th>N° Presupuesto</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Equipo</th>
                        <th>Monto Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presupuestos as $presupuesto)
                        <tr>
                            <td><strong>{{ $presupuesto->codigo }}</strong></td>
                            <td>{{ $presupuesto->fecha_presupuesto->format('d/m/Y') }}</td>
                            <td>{{ $presupuesto->cliente }}</td>
                            <td>{{ $presupuesto->equipo }}</td>
                            <td><strong>{{ $presupuesto->monto_formateado }}</strong></td>
                            <td>{!! $presupuesto->estado_badge !!}</td>
                            <td>
                                <button wire:click="verPresupuesto({{ $presupuesto->id }})"
                                    class="btn btn-info btn-sm" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>

                                @if($presupuesto->estado === 'pendiente_aprobacion')
                                    <a href="{{ route('servicios.presupuestos.edit', $presupuesto->id) }}"
                                        class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button wire:click="aprobar({{ $presupuesto->id }})"
                                        class="btn btn-success btn-sm" title="Aprobar"
                                        onclick="return confirm('¿Aprobar este presupuesto?')">
                                        <i class="fas fa-check"></i>
                                    </button>

                                    <button wire:click="rechazar({{ $presupuesto->id }})"
                                        class="btn btn-danger btn-sm" title="Rechazar"
                                        onclick="return confirm('¿Rechazar este presupuesto?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay presupuestos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $presupuestos->links() }}
        </div>
    </x-adminlte-card>

    @if($mostrarModal && $presupuestoSeleccionado)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title"><i class="fas fa-file-invoice-dollar"></i> Detalle del Presupuesto</h5>
                        <button type="button" class="close" wire:click="cerrarModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- Información General --}}
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><strong>Información General</strong></h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <p class="mb-2"><strong>N° Presupuesto:</strong><br>{{ $presupuestoSeleccionado->codigo }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-2"><strong>Fecha:</strong><br>{{ $presupuestoSeleccionado->fecha_presupuesto->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-2"><strong>Estado:</strong><br>{!! $presupuestoSeleccionado->estado_badge !!}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-2"><strong>N° Diagnóstico:</strong><br>{{ $presupuestoSeleccionado->diagnostico->codigo }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Información del Cliente y Equipo --}}
                        <div class="card mt-2">
                            <div class="card-header">
                                <h6 class="mb-0"><strong>Cliente y Equipo</strong></h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Cliente:</strong><br>{{ $presupuestoSeleccionado->diagnostico->recepcion->solicitud->cliente->nombre }}</p>
                                        <p class="mb-2"><strong>Documento:</strong> {{ $presupuestoSeleccionado->diagnostico->recepcion->solicitud->cliente->documento }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Equipo:</strong><br>{{ $presupuestoSeleccionado->diagnostico->recepcion->producto->nombre ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>N° Solicitud:</strong> {{ $presupuestoSeleccionado->diagnostico->recepcion->solicitud->codigo }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="mb-0"><strong>Problema Detectado:</strong><br>{{ $presupuestoSeleccionado->diagnostico->problema_detectado }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Detalle de Servicios --}}
                        <div class="card mt-2">
                            <div class="card-header">
                                <h6 class="mb-0"><strong>Tipos de Servicio</strong></h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Tipo de Servicio</th>
                                                <th class="text-center" width="100">Cantidad</th>
                                                <th class="text-right" width="150">Precio Unitario</th>
                                                <th class="text-right" width="150">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($presupuestoSeleccionado->diagnostico->tiposServicio->count() > 0)
                                                @foreach($presupuestoSeleccionado->diagnostico->tiposServicio as $ts)
                                                    <tr>
                                                        <td>{{ $ts->tipoServicio->codigo ?? 'N/A' }}</td>
                                                        <td>{{ $ts->tipoServicio->descripcion ?? 'N/A' }}</td>
                                                        <td class="text-center">{{ $ts->cantidad }}</td>
                                                        <td class="text-right">₲ {{ number_format($ts->costo_unitario, 0, ',', '.') }}</td>
                                                        <td class="text-right">₲ {{ number_format($ts->cantidad * $ts->costo_unitario, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No hay tipos de servicio registrados</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold">
                                                <td colspan="4" class="text-right">SUBTOTAL SERVICIOS:</td>
                                                <td class="text-right">₲ {{ number_format($presupuestoSeleccionado->subtotal_servicios, 0, ',', '.') }}</td>
                                            </tr>
                                            @if($presupuestoSeleccionado->descuento_promocion > 0)
                                            <tr>
                                                <td colspan="4" class="text-right">Descuento Promoción
                                                    @if($presupuestoSeleccionado->promocion)
                                                        ({{ $presupuestoSeleccionado->promocion->nombre }})
                                                    @endif:
                                                </td>
                                                <td class="text-right text-danger">- ₲ {{ number_format($presupuestoSeleccionado->descuento_promocion, 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            <tr class="font-weight-bold">
                                                <td colspan="4" class="text-right">TOTAL SERVICIOS:</td>
                                                <td class="text-right">₲ {{ number_format($presupuestoSeleccionado->total_servicios, 0, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Detalle de Repuestos --}}
                        <div class="card mt-2">
                            <div class="card-header">
                                <h6 class="mb-0"><strong>Repuestos Necesarios</strong></h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Repuesto</th>
                                                <th class="text-center" width="100">Cantidad</th>
                                                <th class="text-right" width="150">Precio Unitario</th>
                                                <th class="text-right" width="150">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($presupuestoSeleccionado->diagnostico->repuestos->count() > 0)
                                                @foreach($presupuestoSeleccionado->diagnostico->repuestos as $rep)
                                                    <tr>
                                                        <td>{{ $rep->producto->codigo ?? 'N/A' }}</td>
                                                        <td>{{ $rep->producto->nombre ?? 'N/A' }}</td>
                                                        <td class="text-center">{{ $rep->cantidad }}</td>
                                                        <td class="text-right">₲ {{ number_format($rep->costo, 0, ',', '.') }}</td>
                                                        <td class="text-right">₲ {{ number_format($rep->cantidad * $rep->costo, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No hay repuestos registrados</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold">
                                                <td colspan="4" class="text-right">SUBTOTAL REPUESTOS:</td>
                                                <td class="text-right">₲ {{ number_format($presupuestoSeleccionado->subtotal_repuestos, 0, ',', '.') }}</td>
                                            </tr>
                                            @if($presupuestoSeleccionado->descuento_descuento > 0)
                                            <tr>
                                                <td colspan="4" class="text-right">Descuento Aplicado
                                                    @if($presupuestoSeleccionado->descuento)
                                                        ({{ $presupuestoSeleccionado->descuento->descripcion }})
                                                    @endif:
                                                </td>
                                                <td class="text-right text-danger">- ₲ {{ number_format($presupuestoSeleccionado->descuento_descuento, 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            <tr class="font-weight-bold">
                                                <td colspan="4" class="text-right">TOTAL REPUESTOS:</td>
                                                <td class="text-right">₲ {{ number_format($presupuestoSeleccionado->total_repuestos, 0, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Total General --}}
                        <div class="card mt-2">
                            <div class="card-body bg-light">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="text-right"><h5 class="mb-0"><strong>TOTAL GENERAL:</strong></h5></td>
                                                <td class="text-right" width="150"><h5 class="mb-0"><strong>{{ $presupuestoSeleccionado->monto_formateado }}</strong></h5></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Observaciones --}}
                        @if($presupuestoSeleccionado->observaciones)
                        <div class="card mt-2">
                            <div class="card-header">
                                <h6 class="mb-0"><strong>Observaciones</strong></h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $presupuestoSeleccionado->observaciones }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarModal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                        @if($presupuestoSeleccionado->estado === 'pendiente_aprobacion')
                            <a href="{{ route('servicios.presupuestos.edit', $presupuestoSeleccionado->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
