<div>
    <form wire:submit.prevent="guardar">
        <!-- Mensajes de error/éxito -->
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        <!-- Información General -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fecha_pedido">Fecha Pedido *</label>
                    <input type="date" wire:model="fecha_pedido" class="form-control @error('fecha_pedido') is-invalid @enderror" required>
                    @error('fecha_pedido') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="fecha_necesaria">Fecha Necesaria</label>
                    <input type="date" wire:model="fecha_necesaria" class="form-control @error('fecha_necesaria') is-invalid @enderror">
                    @error('fecha_necesaria') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="tipo_pedido">Tipo de Pedido *</label>
                    <select wire:model="tipo_pedido" class="form-control @error('tipo_pedido') is-invalid @enderror" required>
                        <option value="REPOSICION_STOCK">Reposición de Stock</option>
                        <option value="COMPRA_DIRECTA">Compra Directa</option>
                        <option value="PROYECTO_ESPECIFICO">Proyecto Específico</option>
                        <option value="MANTENIMIENTO">Mantenimiento</option>
                        <option value="INSUMOS">Insumos</option>
                    </select>
                    @error('tipo_pedido') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="prioridad">Prioridad *</label>
                    <select wire:model="prioridad" class="form-control @error('prioridad') is-invalid @enderror" required>
                        <option value="NORMAL">Normal</option>
                        <option value="URGENTE">Urgente</option>
                        <option value="CRITICA">Crítica</option>
                    </select>
                    @error('prioridad') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <hr>

        <!-- Buscar y Agregar Productos -->
        <h4>Productos Solicitados</h4>

        <div class="card">
            <div class="card-body">
                @if (session()->has('error_producto'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('error_producto') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Buscar Producto *</label>
                            <div class="position-relative">
                                <input type="text"
                                       wire:model.live.debounce.500ms="search_producto"
                                       class="form-control"
                                       placeholder="Escriba nombre o código del producto..."
                                       autocomplete="off">

                                <!-- Dropdown de resultados -->
                                @if($mostrar_resultados && count($productos_encontrados) > 0)
                                <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                                    @foreach($productos_encontrados as $producto)
                                    <a href="javascript:void(0)"
                                       wire:click="seleccionarProducto({{ $producto['id'] }})"
                                       class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <div>
                                                <strong>{{ $producto['codigo'] }}</strong> - {{ $producto['nombre'] }}
                                                <br>
                                                <small class="text-muted">{{ $producto['descripcion'] }}</small>
                                            </div>
                                            <div class="text-right">
                                                <small class="text-success">
                                                    Stock: {{ number_format($producto['stock_actual'], 0) }}
                                                </small>
                                                <br>
                                                <small class="text-primary">
                                                    ₲ {{ number_format($producto['precio_compra'], 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            @if($producto_seleccionado)
                                <small class="text-success">
                                    <i class="fas fa-check"></i> {{ $producto_seleccionado->nombre }}
                                    (Stock: {{ number_format($producto_seleccionado->stock_actual ?? 0, 0) }})
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Cantidad *</label>
                            <input type="number" wire:model="cantidad_solicitada" class="form-control" step="0.01" min="0.01">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Precio Estimado *</label>
                            <input type="number" wire:model="precio_estimado" class="form-control" step="0.01" min="0">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            @if($editando_index !== null)
                                <button type="button" wire:click="agregarDetalle" class="btn btn-warning btn-block">
                                    <i class="fas fa-save"></i> Actualizar
                                </button>
                                <button type="button" wire:click="cancelarEdicion" class="btn btn-secondary btn-block btn-sm mt-1">
                                    Cancelar
                                </button>
                            @else
                                <button type="button" wire:click="agregarDetalle" class="btn btn-success btn-block">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tabla de productos agregados -->
                @if(count($detalles) > 0)
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-right">Stock Actual</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">Precio Est.</th>
                                <th class="text-right">Subtotal</th>
                                <th class="text-center" width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detalles as $index => $detalle)
                            <tr class="{{ $editando_index === $index ? 'table-warning' : '' }}">
                                <td>{{ $detalle['producto_nombre'] }}</td>
                                <td class="text-right">{{ number_format($detalle['stock_actual'] ?? 0, 2) }}</td>
                                <td class="text-right">{{ number_format($detalle['cantidad_solicitada'], 2) }}</td>
                                <td class="text-right">₲ {{ number_format($detalle['precio_estimado'], 0, ',', '.') }}</td>
                                <td class="text-right">₲ {{ number_format($detalle['cantidad_solicitada'] * $detalle['precio_estimado'], 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <button type="button" wire:click="editarDetalle({{ $index }})" class="btn btn-sm btn-info" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" wire:click="eliminarDetalle({{ $index }})" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td colspan="4" class="text-right">TOTAL ESTIMADO:</td>
                                <td class="text-right">₲ {{ number_format($this->calcularTotal(), 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> No hay productos agregados. Busque y agregue productos al pedido.
                </div>
                @endif
            </div>
        </div>

        <!-- Observaciones -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea wire:model="observaciones" class="form-control" rows="3" placeholder="Ingrese observaciones adicionales..."></textarea>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Guardar Pedido
                </button>
                <a href="{{ route('compras.pedidos.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>

    <style>
    .list-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    </style>
</div>
