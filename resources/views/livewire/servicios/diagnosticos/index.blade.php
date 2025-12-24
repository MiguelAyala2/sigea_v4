<div>
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="¡Éxito!" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="light" title="Diagnósticos" icon="fas fa-stethoscope">
        <div class="row mb-3">
            <div class="col-md-3">
                <x-adminlte-input name="buscador" wire:model.live="buscador" placeholder="Buscar..."
                    fgroup-class="mb-0" igroup-size="sm">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="col-md-2">
                <x-adminlte-select name="buscarEstadoDiagnostico" wire:model.live="buscarEstadoDiagnostico" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Estado Diagnóstico</option>
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
            <div class="col-md-2">
                <x-adminlte-select name="buscarEstado" wire:model.live="buscarEstado" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los estados</option>
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
            <div class="col-md-2">
                <x-adminlte-select name="buscarActivo" wire:model.live="buscarActivo" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-filter"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
            <div class="col-md-3 text-right">
                <a href="{{ route('servicios.diagnosticos.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Diagnóstico
                </a>
            </div>
        </div>

        @if($diagnosticos->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>N° Diagnóstico</th>
                            <th>Fecha</th>
                            <th>N° Solicitud</th>
                            <th>Cliente</th>
                            <th>Equipo/Producto</th>
                            <th>Estado Diagnóstico</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diagnosticos as $diagnostico)
                            <tr>
                                <td><strong>{{ $diagnostico->numero_diagnostico }}</strong></td>
                                <td>{{ $diagnostico->fecha_diagnostico->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $diagnostico->solicitud->numero_solicitud }}
                                    </span>
                                </td>
                                <td>{{ $diagnostico->cliente->nombre }}</td>
                                <td>{{ $diagnostico->producto->nombre }}</td>
                                <td>
                                    @if(!$diagnostico->activo)
                                        <span class="badge badge-danger">Inactivo</span>
                                    @elseif($diagnostico->estado_diagnostico === 'reparable')
                                        <span class="badge badge-success">Reparable</span>
                                    @elseif($diagnostico->estado_diagnostico === 'no_reparable')
                                        <span class="badge badge-danger">No Reparable</span>
                                    @else
                                        <span class="badge badge-warning">Requiere Repuestos</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$diagnostico->activo)
                                        <span class="badge badge-danger">Inactivo</span>
                                    @elseif($diagnostico->estado === 'pendiente')
                                        <span class="badge badge-secondary">Pendiente</span>
                                    @elseif($diagnostico->estado === 'en_proceso')
                                        <span class="badge badge-warning">En Proceso</span>
                                    @else
                                        <span class="badge badge-success">Completado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button wire:click="verDiagnostico({{ $diagnostico->id }})"
                                                class="btn btn-info btn-xs" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('servicios.diagnosticos.edit', $diagnostico->id) }}"
                                           class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($diagnostico->activo)
                                            <button wire:click="inactivar({{ $diagnostico->id }})"
                                                    class="btn btn-secondary btn-xs"
                                                    title="Inactivar"
                                                    onclick="return confirm('¿Inactivar diagnóstico?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $diagnostico->id }})"
                                                    class="btn btn-success btn-xs"
                                                    title="Activar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Mostrando {{ $diagnosticos->firstItem() }} a {{ $diagnosticos->lastItem() }} de {{ $diagnosticos->total() }} registros
                </div>
                <div>
                    {{ $diagnosticos->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-stethoscope fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay diagnósticos registrados</h5>
                <p class="text-muted">Crea tu primer diagnóstico para comenzar</p>
                <a href="{{ route('servicios.diagnosticos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primer Diagnóstico
                </a>
            </div>
        @endif
    </x-adminlte-card>

    {{-- Modal Ver Diagnóstico --}}
    <div class="modal fade @if($mostrarModal) show @endif" id="modalVerDiagnostico"
         style="@if($mostrarModal) display: block; @endif"
         tabindex="-1" role="dialog" aria-labelledby="modalVerDiagnosticoLabel"
         @if($mostrarModal) aria-modal="true" @endif>
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="modalVerDiagnosticoLabel">
                        <i class="fas fa-eye"></i> Detalles del Diagnóstico
                    </h5>
                    <button type="button" class="close" wire:click="cerrarModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($diagnosticoSeleccionado)

            {{-- Información General --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>N° Diagnóstico:</strong><br>
                    <span class="badge badge-primary">{{ $diagnosticoSeleccionado->numero_diagnostico }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Fecha:</strong><br>
                    {{ $diagnosticoSeleccionado->fecha_diagnostico->format('d/m/Y') }}
                </div>
                <div class="col-md-3">
                    <strong>Cliente:</strong><br>
                    {{ $diagnosticoSeleccionado->cliente->nombre }}
                </div>
                <div class="col-md-3">
                    <strong>Equipo/Producto:</strong><br>
                    {{ $diagnosticoSeleccionado->producto->nombre }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>N° Solicitud:</strong><br>
                    <span class="badge badge-info">{{ $diagnosticoSeleccionado->solicitud->numero_solicitud }}</span>
                </div>
                <div class="col-md-4">
                    <strong>Estado Diagnóstico:</strong><br>
                    @if($diagnosticoSeleccionado->estado_diagnostico === 'reparable')
                        <span class="badge badge-success">Reparable</span>
                    @elseif($diagnosticoSeleccionado->estado_diagnostico === 'no_reparable')
                        <span class="badge badge-danger">No Reparable</span>
                    @else
                        <span class="badge badge-warning">Requiere Repuestos</span>
                    @endif
                </div>
                <div class="col-md-4">
                    <strong>Estado:</strong><br>
                    @if($diagnosticoSeleccionado->estado === 'pendiente')
                        <span class="badge badge-secondary">Pendiente</span>
                    @elseif($diagnosticoSeleccionado->estado === 'en_proceso')
                        <span class="badge badge-warning">En Proceso</span>
                    @else
                        <span class="badge badge-success">Completado</span>
                    @endif
                </div>
            </div>

            <hr>

            {{-- Problema y Solución --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Problema Detectado:</strong><br>
                    <div class="alert alert-danger">
                        {{ $diagnosticoSeleccionado->problema_detectado }}
                    </div>
                </div>
            </div>

            @if($diagnosticoSeleccionado->solucion_propuesta)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Solución Propuesta:</strong><br>
                        <div class="alert alert-success">
                            {{ $diagnosticoSeleccionado->solucion_propuesta }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tipos de Servicio --}}
            @if($diagnosticoSeleccionado->tiposServicio->count() > 0)
                <h6 class="mb-2"><i class="fas fa-list-ul text-info"></i> Tipos de Servicio</h6>
                <table class="table table-bordered table-sm mb-3">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Código</th>
                            <th>Tipo de Servicio</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Precio Unitario</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diagnosticoSeleccionado->tiposServicio as $item)
                            <tr>
                                <td><strong>{{ $item->tipoServicio->codigo }}</strong></td>
                                <td>{{ $item->tipoServicio->descripcion }}</td>
                                <td class="text-center">{{ $item->cantidad }}</td>
                                <td class="text-right">₲ {{ number_format($item->costo_unitario, 0, ',', '.') }}</td>
                                <td class="text-right">₲ {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-info text-white">
                            <td colspan="4" class="text-right"><strong>TOTAL SERVICIOS:</strong></td>
                            <td class="text-right">
                                <strong>₲ {{ number_format($diagnosticoSeleccionado->tiposServicio->sum('subtotal'), 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @endif

            {{-- Repuestos --}}
            @if($diagnosticoSeleccionado->repuestos->count() > 0)
                <h6 class="mb-2"><i class="fas fa-cogs text-success"></i> Repuestos Necesarios</h6>
                <table class="table table-bordered table-sm mb-3">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>Código</th>
                            <th>Repuesto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Precio Unitario</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalRepuestos = 0;
                        @endphp
                        @foreach($diagnosticoSeleccionado->repuestos as $repuesto)
                            @php
                                $subtotal = $repuesto->cantidad * $repuesto->costo;
                                $totalRepuestos += $subtotal;
                            @endphp
                            <tr>
                                <td><strong>{{ $repuesto->producto->codigo }}</strong></td>
                                <td>{{ $repuesto->producto->nombre }}</td>
                                <td class="text-center">{{ $repuesto->cantidad }}</td>
                                <td class="text-right">₲ {{ number_format($repuesto->costo, 0, ',', '.') }}</td>
                                <td class="text-right">₲ {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-success text-white">
                            <td colspan="4" class="text-right"><strong>TOTAL REPUESTOS:</strong></td>
                            <td class="text-right"><strong>₲ {{ number_format($totalRepuestos, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            @endif

            {{-- Total General --}}
            @php
                $totalServicios = $diagnosticoSeleccionado->tiposServicio->sum('subtotal');
                $totalRep = $diagnosticoSeleccionado->repuestos->sum(function($r) { return $r->cantidad * $r->costo; });
                $totalGeneral = $totalServicios + $totalRep;
            @endphp
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info text-right mb-0">
                        <h5><strong>TOTAL GENERAL: ₲ {{ number_format($totalGeneral, 0, ',', '.') }}</strong></h5>
                    </div>
                </div>
            </div>

            @if($diagnosticoSeleccionado->observaciones)
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Observaciones:</strong><br>
                        <p class="text-muted">{{ $diagnosticoSeleccionado->observaciones }}</p>
                    </div>
                </div>
            @endif

                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cerrarModal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Backdrop del modal --}}
    @if($mostrarModal)
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
