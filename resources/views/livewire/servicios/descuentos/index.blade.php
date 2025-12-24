<div>
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="¡Éxito!" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="light" title="Descuentos" icon="fas fa-percent">
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
                <x-adminlte-select name="buscarTipo" wire:model.live="buscarTipo" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los tipos</option>
                    <option value="porcentaje">Porcentaje</option>
                    <option value="monto_fijo">Monto Fijo</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-filter"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
            <div class="col-md-2">
                <x-adminlte-select name="buscarEstado" wire:model.live="buscarEstado" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-toggle-on"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
            <div class="col-md-5 text-right">
                <a href="{{ route('servicios.descuentos.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Descuento
                </a>
            </div>
        </div>

        @if($descuentos->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Tipo Descuento</th>
                            <th>Valor</th>
                            <th>Aplicable a</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($descuentos as $descuento)
                            <tr>
                                <td><strong>{{ $descuento->codigo }}</strong></td>
                                <td>{{ $descuento->descripcion }}</td>
                                <td>
                                    @if($descuento->tipo_descuento === 'porcentaje')
                                        <span class="badge badge-primary">Porcentaje</span>
                                    @else
                                        <span class="badge badge-success">Monto Fijo</span>
                                    @endif
                                </td>
                                <td><strong>{{ $descuento->valor_formateado }}</strong></td>
                                <td><small>{{ $descuento->aplicable_a }}</small></td>
                                <td>
                                    @if($descuento->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button wire:click="verDescuento({{ $descuento->id }})"
                                                class="btn btn-info btn-xs" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('servicios.descuentos.edit', $descuento->id) }}"
                                           class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($descuento->activo)
                                            <button wire:click="inactivar({{ $descuento->id }})"
                                                    class="btn btn-secondary btn-xs"
                                                    title="Inactivar"
                                                    onclick="return confirm('¿Inactivar descuento?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $descuento->id }})"
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
                    Mostrando {{ $descuentos->firstItem() }} a {{ $descuentos->lastItem() }} de {{ $descuentos->total() }} registros
                </div>
                <div>
                    {{ $descuentos->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-percent fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay descuentos registrados</h5>
                <p class="text-muted">Crea tu primer descuento para comenzar</p>
                <a href="{{ route('servicios.descuentos.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear Primer Descuento
                </a>
            </div>
        @endif
    </x-adminlte-card>

    {{-- Modal Ver Descuento --}}
    <div class="modal fade @if($mostrarModal) show @endif" id="modalVerDescuento"
         style="@if($mostrarModal) display: block; @endif"
         tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">
                        <i class="fas fa-eye"></i> Detalles del Descuento
                    </h5>
                    <button type="button" class="close" wire:click="cerrarModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($descuentoSeleccionado)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Código:</strong><br>
                                <span class="badge badge-primary">{{ $descuentoSeleccionado->codigo }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Tipo:</strong><br>
                                @if($descuentoSeleccionado->tipo_descuento === 'porcentaje')
                                    <span class="badge badge-primary">Porcentaje</span>
                                @else
                                    <span class="badge badge-success">Monto Fijo</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Descripción:</strong><br>
                                {{ $descuentoSeleccionado->descripcion }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Valor:</strong><br>
                                <h4><span class="badge badge-success">{{ $descuentoSeleccionado->valor_formateado }}</span></h4>
                            </div>
                            <div class="col-md-8">
                                <strong>Aplicable a:</strong><br>
                                {{ $descuentoSeleccionado->aplicable_a }}
                            </div>
                        </div>

                        @if($descuentoSeleccionado->fecha_inicio && $descuentoSeleccionado->fecha_fin)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>Vigencia:</strong><br>
                                    Del {{ $descuentoSeleccionado->fecha_inicio->format('d/m/Y') }}
                                    al {{ $descuentoSeleccionado->fecha_fin->format('d/m/Y') }}
                                </div>
                            </div>
                        @endif

                        @if($descuentoSeleccionado->observaciones)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>Observaciones:</strong><br>
                                    <p class="text-muted">{{ $descuentoSeleccionado->observaciones }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <strong>Estado:</strong><br>
                                @if($descuentoSeleccionado->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </div>
                        </div>
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

    @if($mostrarModal)
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
