<div>
    <form wire:submit.prevent="guardar">
        <!-- Mensajes de error/éxito -->
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        <!-- Buscar Cliente -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Buscar Cliente *</label>
                    <div class="position-relative">
                        <input type="text"
                               wire:model.live.debounce.500ms="search_cliente"
                               class="form-control @error('cliente_id') is-invalid @enderror"
                               placeholder="Buscar cliente por nombre o documento..."
                               autocomplete="off"
                               required>

                        <!-- Dropdown de clientes -->
                        @if($mostrar_clientes && count($clientes_encontrados) > 0)
                        <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                            @foreach($clientes_encontrados as $cliente)
                            <a href="javascript:void(0)"
                               wire:click="seleccionarCliente({{ $cliente['id'] }})"
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $cliente['nombre'] }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            Documento: {{ $cliente['documento'] }}
                                        </small>
                                    </div>
                                    <div class="text-right">
                                        @if($cliente['telefono'])
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> {{ $cliente['telefono'] }}
                                        </small>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    @if($cliente_seleccionado)
                        <small class="text-success mt-2 d-block">
                            <i class="fas fa-check-circle"></i> Cliente seleccionado: {{ $cliente_seleccionado->nombre }}
                        </small>
                    @endif
                    @error('cliente_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Información del Cliente (solo si hay cliente seleccionado) -->
        @if($cliente_seleccionado)
        <div class="card bg-light mb-3">
            <div class="card-body">
                <h6 class="card-title text-primary"><i class="fas fa-user"></i> Datos del Cliente</h6>
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Nombre:</strong> {{ $cliente_seleccionado->nombre }}</p>
                    </div>
                    <div class="col-md-2">
                        <p class="mb-1"><strong>Documento:</strong> {{ $cliente_seleccionado->documento }}</p>
                    </div>
                    <div class="col-md-2">
                        <p class="mb-1"><strong>Teléfono:</strong> {{ $cliente_seleccionado->telefono ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Dirección:</strong> {{ $cliente_seleccionado->direccion ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Datos de Empresa, Sucursal y Funcionario (Solo visualización) -->
        <div class="card bg-light mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1"><strong><i class="fas fa-building"></i> Empresa:</strong> {{ $empresa_nombre }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong><i class="fas fa-store"></i> Sucursal:</strong> {{ $sucursal_nombre }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong><i class="fas fa-user-tie"></i> Funcionario:</strong> {{ $funcionario_nombre }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Pedido -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fecha Pedido *</label>
                    <input type="date" wire:model="fecha_pedido" class="form-control @error('fecha_pedido') is-invalid @enderror" required>
                    @error('fecha_pedido') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Tipo de Entrega *</label>
                    <select wire:model.live="tipo_entrega" class="form-control @error('tipo_entrega') is-invalid @enderror" required>
                        <option value="RETIRO_LOCAL">Retiro en Local</option>
                        <option value="DELIVERY">Delivery</option>
                        <option value="ENVIO_TRANSPORTE">Envío por Transporte</option>
                    </select>
                    @error('tipo_entrega') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        @if($tipo_entrega === 'DELIVERY' || $tipo_entrega === 'ENVIO_TRANSPORTE')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Dirección de Entrega</label>
                    <input type="text" wire:model="direccion_entrega" class="form-control" placeholder="Ingrese la dirección de entrega">
                </div>
            </div>
        </div>
        @endif

        <hr>

        <!-- Búsqueda y Agregado de Productos -->
        <h4>Productos del Pedido</h4>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Buscar Producto</label>
                    <div class="position-relative">
                        <input type="text"
                               wire:model.live.debounce.500ms="search_producto"
                               class="form-control"
                               placeholder="Buscar producto por código o nombre..."
                               autocomplete="off">

                        <!-- Dropdown de productos -->
                        @if($mostrar_productos && count($productos_encontrados) > 0)
                        <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                            @foreach($productos_encontrados as $producto)
                            <a href="javascript:void(0)"
                               wire:click="agregarProducto({{ $producto['id'] }})"
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $producto['codigo'] }}</strong> - {{ $producto['nombre'] }}
                                        <br>
                                        <small class="text-muted">Stock: {{ number_format($producto['stock_actual'], 2) }}</small>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-primary font-weight-bold">₲ {{ number_format($producto['precio_venta'], 0, ',', '.') }}</small>
                                        <br>
                                        <small class="badge badge-info">IVA {{ $producto['iva_porcentaje'] }}%</small>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        @if(count($detalles) > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Precio Unit.</th>
                        <th class="text-center">IVA %</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-center" width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detalles as $index => $detalle)
                    <tr class="{{ $editando_index === $index ? 'table-warning' : '' }}">
                        <td>{{ $detalle['producto_codigo'] }}</td>
                        <td>{{ $detalle['producto_nombre'] }}</td>

                        @if($editando_index === $index)
                            <td class="text-right">
                                <input type="number" wire:model.defer="cantidad_solicitada_edit" class="form-control form-control-sm" step="0.01" min="0.01" placeholder="Cantidad">
                            </td>
                            <td class="text-right">
                                <input type="number" wire:model.defer="precio_unitario_edit" class="form-control form-control-sm" step="0.01" min="0" placeholder="Precio">
                            </td>
                        @else
                            <td class="text-right">{{ number_format($detalle['cantidad_solicitada'], 2) }}</td>
                            <td class="text-right">₲ {{ number_format($detalle['precio_unitario'], 0, ',', '.') }}</td>
                        @endif

                        <td class="text-center">{{ $detalle['iva_porcentaje'] }}%</td>
                        <td class="text-right">₲ {{ number_format($this->calcularSubtotal($detalle) + $this->calcularIva($detalle), 0, ',', '.') }}</td>

                        <td class="text-center">
                            @if($editando_index === $index)
                                <button type="button" wire:click.prevent="guardarEdicionDetalle" class="btn btn-sm btn-success" title="Guardar cambios">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" wire:click.prevent="cancelarEdicion" class="btn btn-sm btn-secondary" title="Cancelar">
                                    <i class="fas fa-times"></i>
                                </button>
                            @else
                                <button type="button" wire:click.prevent="editarDetalle({{ $index }})" class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" wire:click.prevent="eliminarDetalle({{ $index }})" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Eliminar este producto?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $totales = $this->calcularTotal();
                    @endphp
                    <tr class="font-weight-bold bg-light">
                        <td colspan="5" class="text-right">SUBTOTAL:</td>
                        <td class="text-right">₲ {{ number_format($totales['subtotal'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    @if($totales['iva_10'] > 0)
                    <tr>
                        <td colspan="5" class="text-right">IVA 10%:</td>
                        <td class="text-right">₲ {{ number_format($totales['iva_10'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    @endif
                    @if($totales['iva_5'] > 0)
                    <tr>
                        <td colspan="5" class="text-right">IVA 5%:</td>
                        <td class="text-right">₲ {{ number_format($totales['iva_5'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    @endif
                    <tr class="font-weight-bold table-success">
                        <td colspan="5" class="text-right">TOTAL GENERAL:</td>
                        <td class="text-right">₲ {{ number_format($totales['total'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No hay productos agregados. Busque y seleccione productos para agregarlos al pedido.
        </div>
        @endif

        <!-- Observaciones -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea wire:model="observaciones" class="form-control" rows="3" placeholder="Observaciones del pedido..."></textarea>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-lg" {{ count($detalles) === 0 || !$cliente_id ? 'disabled' : '' }}>
                    <i class="fas fa-save"></i> Guardar Pedido
                </button>
                <a href="{{ route('ventas.pedidos.historial') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>

    @if($editando_index !== null)
    <div class="alert alert-info mt-2">
        <i class="fas fa-info-circle"></i> Editando producto en posición {{ $editando_index + 1 }}
    </div>
    @endif

    <style>
    .list-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .table-warning {
        background-color: #fff3cd !important;
    }
    </style>
</div>
