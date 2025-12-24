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

    <x-adminlte-card theme="light" title="Clientes" icon="fas fa-users">
        <div class="row mb-3">
            <div class="col-md-5">
                <x-adminlte-input name="buscador" wire:model.live="buscador" placeholder="Buscar cliente..."
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
                    <option value="fisica">Persona Física</option>
                    <option value="juridica">Persona Jurídica</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-user-tag"></i>
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
                <a href="{{ route('servicios.clientes.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Cliente
                </a>
                <button wire:click="pdf" class="btn btn-danger btn-sm" title="Exportar PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <button wire:click="excel" class="btn btn-success btn-sm" title="Exportar Excel">
                    <i class="fas fa-file-excel"></i>
                </button>
            </div>
        </div>

        @if($clientes->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Documento</th>
                            <th>Nombre / Razón Social</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                            <tr>
                                <td>
                                    @if($cliente->tipo_cliente === 'fisica')
                                        <span class="badge badge-info">
                                            <i class="fas fa-user"></i> Física
                                        </span>
                                    @else
                                        <span class="badge badge-primary">
                                            <i class="fas fa-building"></i> Jurídica
                                        </span>
                                    @endif
                                </td>
                                <td><strong>{{ $cliente->documento }}</strong></td>
                                <td>{{ $cliente->nombre }}</td>
                                <td>
                                    @if($cliente->telefono || $cliente->celular)
                                        <small class="text-muted">
                                            @if($cliente->telefono)
                                                <i class="fas fa-phone"></i> {{ $cliente->telefono }}
                                            @endif
                                            @if($cliente->celular)
                                                <i class="fas fa-mobile-alt"></i> {{ $cliente->celular }}
                                            @endif
                                        </small>
                                    @else
                                        <small class="text-muted">Sin teléfono</small>
                                    @endif
                                </td>
                                <td>
                                    @if($cliente->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button wire:click="verDetalles({{ $cliente->id }})" class="btn btn-info btn-xs" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('servicios.clientes.edit', $cliente->id) }}"
                                           class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($cliente->activo)
                                            <button wire:click="inactivar({{ $cliente->id }})"
                                                    class="btn btn-secondary btn-xs"
                                                    title="Inactivar"
                                                    onclick="return confirm('¿Inactivar cliente?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $cliente->id }})"
                                                    class="btn btn-success btn-xs"
                                                    title="Activar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button wire:click="eliminar({{ $cliente->id }})"
                                                class="btn btn-danger btn-xs"
                                                title="Eliminar"
                                                onclick="return confirm('¿Eliminar cliente? Esta acción no se puede deshacer.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} registros
                </div>
                <div>
                    {{ $clientes->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay clientes registrados</h5>
                <p class="text-muted">Crea tu primer cliente para comenzar</p>
                <a href="{{ route('servicios.clientes.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear Primer Cliente
                </a>
            </div>
        @endif
    </x-adminlte-card>

    {{-- Modal de Ver Detalles --}}
    @if($showModal && $clienteSeleccionado)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title"><i class="fas fa-user"></i> Detalles del Cliente</h5>
                        <button type="button" class="close" wire:click="cerrarModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tipo de Cliente:</strong><br>
                                    @if($clienteSeleccionado->tipo_cliente === 'fisica')
                                        <span class="badge badge-info"><i class="fas fa-user"></i> Persona Física</span>
                                    @else
                                        <span class="badge badge-primary"><i class="fas fa-building"></i> Persona Jurídica</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Estado:</strong><br>
                                    @if($clienteSeleccionado->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <p><strong>Documento:</strong><br>{{ $clienteSeleccionado->documento }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Nombre / Razón Social:</strong><br>{{ $clienteSeleccionado->nombre }}</p>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <p><strong>Teléfono:</strong><br>{{ $clienteSeleccionado->telefono ?? 'No registrado' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Celular:</strong><br>{{ $clienteSeleccionado->celular ?? 'No registrado' }}</p>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <p><strong>Email:</strong><br>{{ $clienteSeleccionado->email ?? 'No registrado' }}</p>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <p><strong>Dirección:</strong><br>{{ $clienteSeleccionado->direccion ?? 'No registrada' }}</p>
                            </div>
                        </div>

                        @if($clienteSeleccionado->observaciones)
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <p><strong>Observaciones:</strong><br>{{ $clienteSeleccionado->observaciones }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="row mt-3 pt-3 border-top">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> Registrado: {{ $clienteSeleccionado->created_at->format('d/m/Y H:i') }}<br>
                                    @if($clienteSeleccionado->updated_at != $clienteSeleccionado->created_at)
                                        <i class="fas fa-edit"></i> Última actualización: {{ $clienteSeleccionado->updated_at->format('d/m/Y H:i') }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
