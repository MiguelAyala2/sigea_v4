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

    <x-tabla titulo="Productos" buscador="buscador">
        <x-slot name="headerBotones">
            <a href="{{ route('stock.productos.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Nuevo Producto
            </a>
        </x-slot>

        <x-slot name="cabeceras">
            <th>Código</th>
            <th>Imagen</th>
            <th>
                <x-adminlte-input name="buscarNombre"
                    wire:model.live.debounce.300ms="buscarNombre"
                    placeholder="Nombre"
                    igroup-size="sm" />
            </th>
            <th>Categoría</th>
            <th>Marca</th>
            <th>Tipo</th>
            <th>U. Medida</th>
            <th>Estado</th>
            <th>Acciones</th>
        </x-slot>

        @forelse($productos as $producto)
            <tr class="{{ $producto->activo ? '' : 'table-secondary' }}">
                <td>
                    <span class="badge badge-secondary">{{ $producto->codigo }}</span>
                    @if($producto->codigo_barras)
                        <br><small class="text-muted"><i class="fas fa-barcode"></i> {{ $producto->codigo_barras }}</small>
                    @endif
                </td>
                <td class="text-center">
                    @if($producto->imagenPrincipal)
                        <img src="{{ asset('storage/' . $producto->imagenPrincipal->path) }}"
                             alt="{{ $producto->nombre }}"
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    @elseif($producto->imagenes->first())
                        <img src="{{ asset('storage/' . $producto->imagenes->first()->path) }}"
                             alt="{{ $producto->nombre }}"
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    @else
                        <div style="width: 50px; height: 50px; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                    @endif
                </td>
                <td>
                    <strong>{{ $producto->nombre }}</strong>
                    @if($producto->modelo)
                        <br><small class="text-muted">Modelo: {{ $producto->modelo }}</small>
                    @endif
                </td>
                <td>
                    @if($producto->categoria)
                        <small>{{ $producto->categoria->nombre }}</small>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($producto->marca)
                        {{ $producto->marca->nombre }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($producto->tipo == 'PRODUCTO')
                        <span class="badge badge-primary">Producto</span>
                    @elseif($producto->tipo == 'INSUMO')
                        <span class="badge badge-info">Insumo</span>
                    @elseif($producto->tipo == 'SERVICIO')
                        <span class="badge badge-warning">Servicio</span>
                    @else
                        <span class="badge badge-secondary">Kit</span>
                    @endif
                </td>
                <td>
                    <small>{{ $producto->unidadMedida->simbolo }}</small>
                </td>
                <td>
                    @if($producto->activo)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('stock.productos.edit', $producto->id) }}"
                           class="btn btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>

                        @if($producto->activo)
                            <button type="button" class="btn btn-secondary"
                                    wire:click="inactivar({{ $producto->id }})"
                                    wire:confirm="¿Inactivar producto {{ $producto->nombre }}?"
                                    title="Inactivar">
                                <i class="fas fa-ban"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-success"
                                    wire:click="activar({{ $producto->id }})"
                                    wire:confirm="¿Activar producto {{ $producto->nombre }}?"
                                    title="Activar">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif

                        <button type="button" class="btn btn-danger"
                                wire:click="eliminar({{ $producto->id }})"
                                wire:confirm="¿Está seguro de eliminar el producto {{ $producto->nombre }}? Esta acción no se puede deshacer."
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted">
                    No se encontraron productos
                </td>
            </tr>
        @endforelse

        <x-slot name="paginacion">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }}
                        de {{ $productos->total() }} registros
                    </small>
                </div>
                <div>
                    {{ $productos->links() }}
                </div>
            </div>
        </x-slot>
    </x-tabla>
</div>
