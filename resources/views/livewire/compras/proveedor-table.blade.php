<div>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Buscar proveedores...">
                </div>
                <div class="col-md-3">
                    <select class="form-control" wire:model.live="tipo">
                        <option value="">Todos los tipos</option>
                        <option value="PRODUCTOS">Productos</option>
                        <option value="SERVICIOS">Servicios</option>
                        <option value="AMBOS">Ambos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" wire:model.live="estado">
                        <option value="">Todos</option>
                        <option value="activos">Activos</option>
                        <option value="inactivos">Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" wire:model.live="perPage">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Razón Social</th>
                        <th>RUC</th>
                        <th>Tipo</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $proveedor)
                        <tr>
                            <td>{{ $proveedor->id }}</td>
                            <td>{{ $proveedor->razon_social }}</td>
                            <td>{{ $proveedor->ruc_formateado }}</td>
                            <td>
                                <span class="badge badge-info">{{ $proveedor->tipo_proveedor_texto }}</span>
                            </td>
                            <td>{{ $proveedor->telefono }}</td>
                            <td>{{ $proveedor->email }}</td>
                            <td>
                                @if($proveedor->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @can('compras.proveedores.ver')
                                        <a href="{{ route('compras.proveedores.show', $proveedor) }}" class="btn btn-info btn-sm" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can('compras.proveedores.editar')
                                        <a href="{{ route('compras.proveedores.edit', $proveedor) }}" class="btn btn-primary btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('compras.proveedores.eliminar')
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                                onclick="confirm('¿Está seguro de eliminar este proveedor?') || event.stopImmediatePropagation()"
                                                wire:click="$emit('delete', {{ $proveedor->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-info-circle"></i> No se encontraron proveedores
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-sm">
                        Mostrando {{ $proveedores->firstItem() ?? 0 }} a {{ $proveedores->lastItem() ?? 0 }} de {{ $proveedores->total() }} proveedores
                    </p>
                </div>
                <div class="col-md-6">
                    {{ $proveedores->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
