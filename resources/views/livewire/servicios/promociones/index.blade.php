<div>
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="¡Éxito!" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="light" title="Promociones" icon="fas fa-tags">
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
                    <option value="general">General</option>
                    <option value="servicio">Servicio</option>
                    <option value="producto">Producto</option>
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
                <a href="{{ route('servicios.promociones.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nueva Promoción
                </a>
            </div>
        </div>

        @if($promociones->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre Promoción</th>
                            <th>Tipo</th>
                            <th>Descuento</th>
                            <th>Vigencia</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promociones as $promocion)
                            <tr>
                                <td><strong>{{ $promocion->codigo }}</strong></td>
                                <td>{{ $promocion->nombre }}</td>
                                <td>
                                    @if($promocion->tipo === 'general')
                                        <span class="badge badge-primary">General</span>
                                    @elseif($promocion->tipo === 'servicio')
                                        <span class="badge badge-info">Servicio</span>
                                    @else
                                        <span class="badge badge-warning">Producto</span>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($promocion->descuento, 0) }}%</strong></td>
                                <td>
                                    <small>
                                        {{ $promocion->fecha_inicio->format('d/m/Y') }} - {{ $promocion->fecha_fin->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td>
                                    @if($promocion->estaVigente())
                                        <span class="badge badge-success">Activa</span>
                                    @elseif(!$promocion->activo)
                                        <span class="badge badge-danger">Inactiva</span>
                                    @else
                                        <span class="badge badge-secondary">Finalizada</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button wire:click="verPromocion({{ $promocion->id }})"
                                                class="btn btn-info btn-xs" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('servicios.promociones.edit', $promocion->id) }}"
                                           class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($promocion->activo)
                                            <button wire:click="inactivar({{ $promocion->id }})"
                                                    class="btn btn-secondary btn-xs"
                                                    title="Inactivar"
                                                    onclick="return confirm('¿Inactivar promoción?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $promocion->id }})"
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
                    Mostrando {{ $promociones->firstItem() }} a {{ $promociones->lastItem() }} de {{ $promociones->total() }} registros
                </div>
                <div>
                    {{ $promociones->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay promociones registradas</h5>
                <p class="text-muted">Crea tu primera promoción para comenzar</p>
                <a href="{{ route('servicios.promociones.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear Primera Promoción
                </a>
            </div>
        @endif
    </x-adminlte-card>

    {{-- Modal Ver Promoción --}}
    <div class="modal fade @if($mostrarModal) show @endif" id="modalVerPromocion"
         style="@if($mostrarModal) display: block; @endif"
         tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">
                        <i class="fas fa-eye"></i> Detalles de la Promoción
                    </h5>
                    <button type="button" class="close" wire:click="cerrarModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($promocionSeleccionada)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Código:</strong><br>
                                <span class="badge badge-primary">{{ $promocionSeleccionada->codigo }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Tipo:</strong><br>
                                @if($promocionSeleccionada->tipo === 'general')
                                    <span class="badge badge-primary">General</span>
                                @elseif($promocionSeleccionada->tipo === 'servicio')
                                    <span class="badge badge-info">Servicio</span>
                                @else
                                    <span class="badge badge-warning">Producto</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Nombre:</strong><br>
                                {{ $promocionSeleccionada->nombre }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Descuento:</strong><br>
                                <h4><span class="badge badge-success">{{ number_format($promocionSeleccionada->descuento, 0) }}%</span></h4>
                            </div>
                            <div class="col-md-8">
                                <strong>Vigencia:</strong><br>
                                Del {{ $promocionSeleccionada->fecha_inicio->format('d/m/Y') }}
                                al {{ $promocionSeleccionada->fecha_fin->format('d/m/Y') }}
                            </div>
                        </div>

                        @if($promocionSeleccionada->descripcion)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>Descripción:</strong><br>
                                    <p class="text-muted">{{ $promocionSeleccionada->descripcion }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <strong>Estado:</strong><br>
                                @if($promocionSeleccionada->estaVigente())
                                    <span class="badge badge-success">Activa y Vigente</span>
                                @elseif(!$promocionSeleccionada->activo)
                                    <span class="badge badge-danger">Inactiva</span>
                                @else
                                    <span class="badge badge-secondary">Finalizada</span>
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
